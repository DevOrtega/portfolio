<template>
  <div class="guaguas-tracker min-h-screen bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
      <div class="mb-6">
        <router-link to="/projects" class="text-indigo-400 hover:text-indigo-300 flex items-center gap-2 mb-4 transition-colors">
          <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          {{ $t('mapDemo.backToProjects') }}
        </router-link>
        <h1 class="text-3xl font-bold mb-2">{{ $t('guaguas.title') }}</h1>
        <p class="text-gray-400">{{ $t('guaguas.subtitle') }}</p>
        <InfoBanner type="info" class="mt-2">
          {{ $t('guaguas.infoBanner') }} <strong>Guaguas Municipales</strong> {{ $t('guaguas.infoBannerColors') }}, <strong>Guaguas Global</strong> {{ $t('guaguas.infoBannerGlobal') }} {{ $t('guaguas.infoBannerNight') }}. 
          {{ $t('guaguas.infoBannerHours') }}
          <br><small class="opacity-80">{{ $t('guaguas.infoBannerNote') }}</small>
        </InfoBanner>
      </div>

      <!-- Loading state -->
      <div v-if="isLoadingData" class="flex flex-col items-center justify-center py-20">
        <LoadingSpinner size="large" />
        <p class="mt-4 text-gray-400">{{ $t('guaguas.loadingRoutes') }}</p>
      </div>

      <!-- Error state -->
      <InfoBanner v-else-if="dataError" type="error" class="mb-4">
        ❌ Error al cargar los datos: {{ dataError }}
      </InfoBanner>

      <!-- Main content when data is loaded -->
      <template v-else>
        <!-- Stats Panel -->
        <BusStatsPanel 
          v-model:selected-line="selectedLine"
          :stats="busStats"
          :available-lines="busLines"
          class="mb-4"
        />

        <!-- Mensaje cuando no hay servicio -->
        <InfoBanner v-if="activeBuses.length === 0" type="warning" class="mb-4">
          🌙 {{ $t('guaguas.noService') }} 
          <span v-if="isNightTime()">{{ $t('guaguas.nightSchedule') }}</span>
          <span v-else>{{ $t('guaguas.nightLinesSchedule') }}</span>
        </InfoBanner>

        <!-- Map and sidebar tree container -->
        <div class="relative">
        <!-- Button to toggle tree -->
        <button 
          @click="treeVisible = !treeVisible"
          class="absolute top-4 right-4 z-[1000] bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-lg shadow-lg transition-all"
          :class="{ 'right-4 sm:right-[280px] md:right-[320px]': treeVisible }"
        >
          <el-icon v-if="treeVisible"><ArrowRight /></el-icon>
          <el-icon v-else><ArrowLeft /></el-icon>
        </button>

        <!-- Sidebar tree (extracted component) -->
        <BusLinesTree
          :visible="treeVisible"
          :tree-data="treeData"
          :hidden-bus-ids="hiddenBusIds"
          :expanded-keys="expandedKeys"
          :is-line-selected="isLineSelected"
          @node-click="handleNodeClick"
          @node-expand="handleNodeExpand"
          @node-collapse="handleNodeCollapse"
        />

        <div class="bg-gray-800 rounded-lg overflow-hidden h-[400px] sm:h-[500px] md:h-[600px] lg:h-[700px] xl:h-[800px]">
        <l-map
          ref="map"
          :zoom="zoom"
          :center="center"
          :options="mapOptions"
          @ready="onMapReady"
        >
          <l-tile-layer
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
          />
          
          <!-- Ruta seleccionada siguiendo carreteras reales -->
          <l-polyline
            v-if="selectedRoute"
            :lat-lngs="selectedRoute"
            :color="selectedRouteColor"
            :weight="4"
            :opacity="0.7"
          />
          
          <!-- Bus markers -->
          <l-marker
            v-for="bus in filteredBuses"
            :key="bus.id"
            :lat-lng="[bus.lat, bus.lng]"
            @click="selectBus(bus)"
            :icon="createBusIcon(bus)"
          >
            <l-popup v-if="selectedBusId === bus.id">
              <BusPopup :bus="bus" />
            </l-popup>
          </l-marker>
        </l-map>
      </div>
      </div><!-- Fin contenedor relativo -->

      <!-- Legend Component -->
      <BusLegend 
        :exclusive-mode="exclusiveMode" 
        :exclusive-line="exclusiveLine"
        :selected-count="selectedLines.size"
        class="mt-4"
      />
      </template><!-- Fin template v-else (main content) -->
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { LMap, LTileLayer, LMarker, LPopup, LPolyline } from '@vue-leaflet/vue-leaflet';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { ElIcon } from 'element-plus';
import { ArrowRight, ArrowLeft } from '@element-plus/icons-vue';
import 'element-plus/dist/index.css';

// Components
import InfoBanner from '@/components/InfoBanner.vue';
import LoadingSpinner from '@/components/LoadingSpinner.vue';
import { BusLegend, BusLinesTree, BusPopup, BusStatsPanel } from '@/components/guaguas';

// Composables
import { useBusMap } from '@/composables/useBusMap';
import { useBusSchedule } from '@/composables/useBusSchedule';
import { useBusSelection } from '@/composables/useBusSelection';
import { useBusData } from '@/composables/useBusData';

// Leaflet icons
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

// Configure Leaflet icons with local assets
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl,
  iconUrl,
  shadowUrl,
});

// Initialize composables
const { mapOptions, getResponsiveZoom, createBusIcon } = useBusMap();
const { isNightTime, isInService } = useBusSchedule();
const { 
  routes: apiRoutes, 
  busLines: apiBusLines, 
  mainLines,
  companies: apiCompanies,
  isLoading: isLoadingData,
  error: dataError,
  loadBusData,
  isLoaded: isDataLoaded
} = useBusData();

// Map state
const center = ref([28.050, -15.450]); // Centered on middle zone of Gran Canaria
const zoom = ref(getResponsiveZoom());

// Handle window resize
const handleResize = () => {
  zoom.value = getResponsiveZoom();
  // Close tree on mobile when resizing down
  if (isMobile() && treeVisible.value) {
    treeVisible.value = false;
  }
};

// UI State
const selectedLine = ref('');
const selectedBusId = ref(null);
const buses = ref([]);
const updateInterval = ref(null);

// Sidebar tree control - closed by default on mobile
const isMobile = () => window.innerWidth < 640;
const treeVisible = ref(!isMobile());
const hiddenBusIds = ref(new Set());
const expandedKeys = ref(['municipales', 'guaguas_global']);

// Initialize bus selection composable (pass mainLines from API)
const {
  selectedLines,
  exclusiveMode,
  exclusiveLine,
  isLineSelected,
  updateBusVisibility
} = useBusSelection(buses, hiddenBusIds, mainLines);

// Click tracking for double-click detection (used in handleNodeClick)
const lastClickTime = ref(0);
const lastClickedLine = ref(null);
const previousHiddenBusIds = ref(new Set());
const previousSelectedLines = ref(new Set());

// Selected route visualization
const selectedRoute = ref(null);
const selectedRouteColor = ref('#FDB913');

// Available bus lines - Computed from API data
const busLines = computed(() => {
  const lines = new Set();
  Object.values(apiBusLines.value).forEach(companyLines => {
    companyLines.forEach(line => lines.add(line));
  });
  return Array.from(lines);
});

/**
 * Generate simulated buses based on real routes from API
 * Data sourced from database (originally from guaguas.com and globalsu.net)
 */
const generateBuses = () => {
  // Get routes from API
  const routes = apiRoutes.value;
  
  if (!routes || routes.length === 0) {
    return [];
  }

  // Generate multiple buses per line only if they are in service
  const allBuses = [];
  
  // First we get OSRM routes for each line (async)
  for (const route of routes) {
    // Check if the line should be in service
    if (!isInService(route.type, route.line)) {
      continue; // Skip this line if not in service
    }
    
    // Number of buses per line and direction
    const busesPerDirection = route.type === 'interurban' ? 1 : (route.type === 'night' ? 1 : 2);
    const lineIndex = routes.indexOf(route);
    
    // Create buses in both directions (outbound and inbound)
    const directions = ['outbound', 'inbound'];
    
    for (const [dirIndex, tripDirection] of directions.entries()) {
      // Get route coordinates SPECIFIC for each direction (API uses snake_case)
      const routeCoords = tripDirection === 'outbound' 
        ? route.route_coords_outbound 
        : route.route_coords_inbound;
      
      // Validate that coordinates exist
      if (!routeCoords || routeCoords.length < 2) {
        console.warn(`Route ${route.line} ${tripDirection} does not have enough valid points`);
        continue;
      }
      
      // Origin and destination according to direction
      const origin = tripDirection === 'outbound' ? route.origin : route.destination;
      const destination = tripDirection === 'outbound' ? route.destination : route.origin;
      
      // Stops according to direction (API uses snake_case)
      const stops = tripDirection === 'outbound' ? route.stops_outbound : route.stops_inbound;
      
      for (let i = 0; i < busesPerDirection; i++) {
        // Distribute buses uniformly along the route
        const totalBuses = busesPerDirection;
        const spacing = 1 / (totalBuses + 1);
        const routeProgress = spacing * (i + 1);
        
        // Calculate exact index in route
        const exactIndex = routeProgress * (routeCoords.length - 1);
        const routeIndex = Math.floor(exactIndex);
        const nextRouteIndex = Math.min(routeIndex + 1, routeCoords.length - 1);
        
        const [lat1, lng1] = routeCoords[routeIndex];
        const [lat2, lng2] = routeCoords[nextRouteIndex];
        
        // Interpolate exact position between two points (NO random variation)
        const segmentProgress = exactIndex - routeIndex;
        const finalLat = lat1 + (lat2 - lat1) * segmentProgress;
        const finalLng = lng1 + (lng2 - lng1) * segmentProgress;
        
        // Calculate direction angle to orient the icon
        const angle = Math.atan2(lng2 - lng1, lat2 - lat1) * (180 / Math.PI);
        
        const delayed = Math.random() > 0.85; // 15% probability of delay
        
        // Calculate current stop based on progress
        const currentStopIndex = Math.floor(routeProgress * stops.length);
        const nextStop = stops[Math.min(currentStopIndex, stops.length - 1)];
        
        allBuses.push({
          id: `bus-${lineIndex}-${dirIndex}-${i}`,
          line: route.line,
          type: route.type,
          company: route.company,
          origin: origin,
          destination: destination,
          tripDirection: tripDirection, // 'outbound' or 'inbound'
          stops: stops, // List of stops for this direction
          nextStop: nextStop,
          lat: finalLat,
          lng: finalLng,
          speed: route.type === 'interurban' ? 0.025 : 0.02, // Speed factor
          angle: angle,
          routeCoords: routeCoords,
          currentRouteIndex: routeIndex,
          routeProgress: segmentProgress,
          timeToNext: Math.floor(Math.random() * 8) + 2,
          delayed: delayed,
          delayMinutes: delayed ? Math.floor(Math.random() * 10) + 2 : 0,
          lastUpdate: new Date().toLocaleTimeString('es-ES'),
          color: route.color
        });
      }
    }
  }
  
  return allBuses;
};

// Generate tree structure for Element Plus
const treeData = computed(() => {
  const municipalesBuses = buses.value.filter(b => b.company === 'municipales' && b.type !== 'night');
  const globalBuses = buses.value.filter(b => b.company === 'guaguas_global');
  const nightBusesList = buses.value.filter(b => b.type === 'night');
  
  // Group by line
  const groupByLine = (busList) => {
    const grouped = {};
    busList.forEach(bus => {
      if (!grouped[bus.line]) {
        grouped[bus.line] = [];
      }
      grouped[bus.line].push(bus);
    });
    return grouped;
  };
  
  const municipalesGrouped = groupByLine(municipalesBuses);
  const globalGrouped = groupByLine(globalBuses);
  const nightGrouped = groupByLine(nightBusesList);
  
  return [
    {
      id: 'municipales',
      label: `Guaguas Municipales (${municipalesBuses.length})`,
      type: 'company',
      children: Object.entries(municipalesGrouped).map(([line, busList]) => ({
        id: `municipales-line-${line}`,
        label: `Línea ${line} (${busList.length})`,
        type: 'line',
        line: line,
        company: 'municipales',
        children: busList.map((bus, idx) => {
          const dirIcon = bus.tripDirection === 'outbound' ? '🟢→' : '🟠←';
          return {
            id: bus.id,
            label: `${dirIcon} ${line}-${idx + 1}: ${bus.origin} → ${bus.destination}`,
            type: 'bus',
            bus: bus
          };
        })
      }))
    },
    {
      id: 'guaguas_global',
      label: `Guaguas Global (${globalBuses.length})`,
      type: 'company',
      children: Object.entries(globalGrouped).map(([line, busList]) => ({
        id: `guaguas_global-line-${line}`,
        label: `Línea ${line} (${busList.length})`,
        type: 'line',
        line: line,
        company: 'guaguas_global',
        children: busList.map((bus, idx) => {
          const dirIcon = bus.tripDirection === 'outbound' ? '🟢→' : '🟠←';
          return {
            id: bus.id,
            label: `${dirIcon} ${line}-${idx + 1}: ${bus.origin} → ${bus.destination}`,
            type: 'bus',
            bus: bus
          };
        })
      }))
    },
    {
      id: 'night',
      label: `Líneas nocturnas (${nightBusesList.length})`,
      type: 'company',
      children: Object.entries(nightGrouped).map(([line, busList]) => ({
        id: `night-line-${line}`,
        label: `Line ${line} (${busList.length})`,
        type: 'line',
        line: line,
        company: 'municipales',
        children: busList.map((bus, idx) => {
          const dirIcon = bus.tripDirection === 'outbound' ? '🟢→' : '🟠←';
          return {
            id: bus.id,
            label: `${dirIcon} ${line}-${idx + 1}: ${bus.origin} → ${bus.destination}`,
            type: 'bus',
            bus: bus
          };
        })
      }))
    }
  ];
});

// Note: Routes now come pre-computed from backend (OSRM cached on server)
// No need for frontend OSRM calls anymore - buses follow real roads

// Handle tree clicks
const handleNodeClick = async (data, node) => {
  const now = Date.now();
  const DOUBLE_CLICK_THRESHOLD = 300; // ms
  
  if (data.type === 'bus') {
    // Click on an individual bus - show only this bus and its route
    const bus = buses.value.find(b => b.id === data.id);
    if (bus) {
      // Hide all other buses
      buses.value.forEach(b => {
        if (b.id !== data.id) {
          hiddenBusIds.value.add(b.id);
        } else {
          hiddenBusIds.value.delete(b.id);
        }
      });
      
      // Route now comes pre-computed from backend (OSRM cached on server)
      selectedRoute.value = bus.routeCoords || [];
      selectedRouteColor.value = bus.type === 'night' ? '#9933FF' : (bus.company === 'municipales' ? '#FDB913' : '#0066CC');
      selectedBusId.value = bus.id;
    }
    
    // Force reactivity
    hiddenBusIds.value = new Set(hiddenBusIds.value);
    return;
  }
  
  if (data.type === 'line') {
    const lineKey = `${data.company}-${data.line}`;
    const isDoubleClick = lastClickedLine.value === lineKey && (now - lastClickTime.value) < DOUBLE_CLICK_THRESHOLD;
    
    if (isDoubleClick) {
      // DOUBLE CLICK - Exclusive mode
      if (exclusiveMode.value && exclusiveLine.value?.line === data.line && exclusiveLine.value?.company === data.company) {
        // We're already in exclusive mode for this line - EXIT exclusive mode
        exclusiveMode.value = false;
        exclusiveLine.value = null;
        
        // Restore previous state
        selectedLines.value = new Set(previousSelectedLines.value);
        hiddenBusIds.value = new Set(previousHiddenBusIds.value);
        
        // If none were selected before, show main lines
        if (selectedLines.value.size === 0) {
          updateBusVisibility();
        }
      } else {
        // ENTER exclusive mode
        // Save current state before entering
        previousSelectedLines.value = new Set(selectedLines.value);
        previousHiddenBusIds.value = new Set(hiddenBusIds.value);
        
        // Activate exclusive mode
        exclusiveMode.value = true;
        exclusiveLine.value = { line: data.line, company: data.company };
        
        // Select ONLY this line
        selectedLines.value = new Set([lineKey]);
        
        // Hide all buses except those from this line
        const newHiddenBusIds = new Set();
        buses.value.forEach(bus => {
          if (bus.line !== data.line || bus.company !== data.company) {
            newHiddenBusIds.add(bus.id);
          }
        });
        hiddenBusIds.value = newHiddenBusIds;
      }
    } else {
      // SIMPLE CLICK - Add/remove from selection
      if (exclusiveMode.value) {
        // If we're in exclusive mode, exit first
        exclusiveMode.value = false;
        exclusiveLine.value = null;
        selectedLines.value = new Set(previousSelectedLines.value);
      }
      
      // Toggle the line in selection
      if (selectedLines.value.has(lineKey)) {
        selectedLines.value.delete(lineKey);
      } else {
        selectedLines.value.add(lineKey);
      }
      
      // Update bus visibility
      updateBusVisibility();
    }
    
    // Update click tracking
    lastClickTime.value = now;
    lastClickedLine.value = lineKey;
    
    // Clear selected route
    selectedRoute.value = null;
    
    // Force reactivity
    selectedLines.value = new Set(selectedLines.value);
    return;
  }
  
  if (data.type === 'company') {
    // Click on a company - toggle all lines of that company
    const companyId = data.id;
    const companyBuses = buses.value.filter(b => {
      if (companyId === 'night') {
        return b.type === 'night';
      }
      return b.company === companyId && b.type !== 'night';
    });
    
    // Get all unique lines from this company
    const companyLineKeys = new Set();
    companyBuses.forEach(bus => {
      companyLineKeys.add(`${bus.company}-${bus.line}`);
    });
    
    // Check if all are selected
    const allSelected = [...companyLineKeys].every(key => selectedLines.value.has(key));
    
    if (allSelected) {
      // Deselect all
      companyLineKeys.forEach(key => selectedLines.value.delete(key));
    } else {
      // Select all
      companyLineKeys.forEach(key => selectedLines.value.add(key));
    }
    
    // Exit exclusive mode if it was active
    if (exclusiveMode.value) {
      exclusiveMode.value = false;
      exclusiveLine.value = null;
    }
    
    updateBusVisibility();
    
    // Clear selected route
    selectedRoute.value = null;
    
    // Force reactivity
    selectedLines.value = new Set(selectedLines.value);
  }
};

// Handle tree node expansion
const handleNodeExpand = (data, node) => {
  if (!expandedKeys.value.includes(node.key)) {
    expandedKeys.value.push(node.key);
  }
};

// Handle tree node collapse
const handleNodeCollapse = (data, node) => {
  expandedKeys.value = expandedKeys.value.filter(k => k !== node.key);
};

// Simulate realistic bus movement following real routes
const updateBusPositions = () => {
  buses.value = buses.value.filter(bus => {
    // Check if the bus is still in service according to the time
    if (!isInService(bus.type, bus.line)) {
      return false; // Remove buses out of service
    }
    return true;
  }).map(bus => {
    const currentIndex = bus.currentRouteIndex || 0;
    const progress = bus.routeProgress || 0;
    
    // Variable speed: interurban buses are faster
    const baseSpeed = bus.type === 'interurban' ? 0.035 : 0.025;
    // Add small variation so they don't all move the same
    const speedVariation = 1 + (Math.sin(Date.now() / 1000 + bus.id.charCodeAt(4)) * 0.1);
    const speedFactor = baseSpeed * speedVariation;
    
    let newProgress = progress + speedFactor;
    let newIndex = currentIndex;
    
    // If completed current segment, advance to next point
    if (newProgress >= 1.0) {
      newProgress = newProgress - 1.0; // Keep the excess for continuity
      newIndex = currentIndex + 1;
      
      // If reached the end of route, return to start (circular route)
      if (newIndex >= bus.routeCoords.length - 1) {
        newIndex = 0;
        newProgress = 0;
      }
    }
    
    // Interpolate exact position between two consecutive points
    const [lat1, lng1] = bus.routeCoords[newIndex];
    const nextIndex = Math.min(newIndex + 1, bus.routeCoords.length - 1);
    const [lat2, lng2] = bus.routeCoords[nextIndex];
    
    const newLat = lat1 + (lat2 - lat1) * newProgress;
    const newLng = lng1 + (lng2 - lng1) * newProgress;
    
    // Calculate direction angle to orient the icon
    const angle = Math.atan2(lng2 - lng1, lat2 - lat1) * (180 / Math.PI);
    
    // Calculate total progress in the route (0 to 1)
    const totalProgress = (newIndex + newProgress) / (bus.routeCoords.length - 1);
    
    // Calculate next stop based on progress
    let nextStop = bus.nextStop;
    if (bus.stops && bus.stops.length > 0) {
      const stopIndex = Math.min(
        Math.floor(totalProgress * bus.stops.length),
        bus.stops.length - 1
      );
      nextStop = bus.stops[stopIndex];
    }
    
    // Update time to next stop (simulation)
    let newTime = bus.timeToNext;
    if (Math.random() > 0.85) {
      newTime = Math.max(1, newTime - 1);
      if (newTime <= 1) {
        newTime = Math.floor(Math.random() * 5) + 2;
      }
    }
    
    return {
      ...bus,
      lat: newLat,
      lng: newLng,
      currentRouteIndex: newIndex,
      routeProgress: newProgress,
      angle: angle,
      nextStop: nextStop,
      timeToNext: newTime,
      lastUpdate: new Date().toLocaleTimeString('es-ES')
    };
  });
  
  // If no buses (all out of service), regenerate to check schedules
  if (buses.value.length === 0) {
    buses.value = generateBuses();
  }
};

const filteredBuses = computed(() => {
  let filtered = buses.value;
  
  // Filter by line if selected
  if (selectedLine.value) {
    filtered = filtered.filter(bus => bus.line === selectedLine.value);
  }
  
  // Exclude buses hidden from tree
  filtered = filtered.filter(bus => !hiddenBusIds.value.has(bus.id));
  
  return filtered;
});

const activeBuses = computed(() => buses.value);

const delayedBuses = computed(() => buses.value.filter(bus => bus.delayed));

const municipalesBuses = computed(() => buses.value.filter(bus => bus.company === 'municipales' && bus.type !== 'night'));

const globalBuses = computed(() => buses.value.filter(bus => bus.company === 'guaguas_global'));

const nightBuses = computed(() => buses.value.filter(bus => bus.type === 'night'));

// Stats object for BusStatsPanel component
const busStats = computed(() => ({
  active: activeBuses.value.length,
  municipales: municipalesBuses.value.length,
  guaguas_global: globalBuses.value.length,
  night: nightBuses.value.length,
  delayed: delayedBuses.value.length
}));

/**
 * Select a bus to view its details
 * @param {Object} bus - Bus object
 */
const selectBus = (bus) => {
  selectedBusId.value = bus.id;
};

const onMapReady = () => {
  // Map initialized successfully
};

onMounted(async () => {
  // Load bus data from API
  try {
    await loadBusData();
  } catch (err) {
    console.error('Failed to load bus data:', err);
    return;
  }
  
  buses.value = generateBuses();
  
  // Initialize visibility: hide lines that are not main
  // Main lines are shown by default
  updateBusVisibility();
  
  // Update bus positions every 5 seconds
  updateInterval.value = setInterval(() => {
    updateBusPositions();
  }, 5000);
  
  // Add resize listener
  window.addEventListener('resize', handleResize);
});

// Watch for data changes to regenerate buses
watch(isDataLoaded, (loaded) => {
  if (loaded) {
    buses.value = generateBuses();
    updateBusVisibility();
  }
});

onUnmounted(() => {
  if (updateInterval.value) {
    clearInterval(updateInterval.value);
  }
  
  // Remove resize listener
  window.removeEventListener('resize', handleResize);
});
</script>

<style scoped>
/* Style for custom bus icons without background */
:deep(.custom-bus-icon) {
  background: none !important;
  border: none !important;
  box-shadow: none !important;
}

:deep(.custom-bus-icon svg) {
  display: block;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
  transition: transform 0.2s ease;
}

:deep(.custom-bus-icon:hover svg) {
  transform: scale(1.1);
}

.bus-popup {
  min-width: 250px;
  color: #1f2937;
}

:deep(.leaflet-popup-content-wrapper) {
  border-radius: 8px;
}

:deep(.leaflet-popup-content) {
  margin: 12px;
}

/* Sidebar tree transition */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}

.slide-enter-from {
  transform: translateX(100%);
}

.slide-leave-to {
  transform: translateX(100%);
}

/* Element Plus tree styles */
:deep(.el-tree) {
  background: transparent !important;
  color: white !important;
}

:deep(.el-tree-node__content) {
  background: transparent !important;
  color: white !important;
  padding: 8px 0;
  transition: background-color 0.2s;
}

:deep(.el-tree-node__content:hover) {
  background: rgba(75, 85, 99, 0.5) !important;
}

:deep(.el-tree-node__expand-icon) {
  color: #9ca3af !important;
}

:deep(.el-tree-node.is-expanded > .el-tree-node__content .el-tree-node__expand-icon) {
  color: white !important;
}

:deep(.el-tree-node__label) {
  color: white !important;
}

/* Custom scrollbar for tree */
.overflow-y-auto::-webkit-scrollbar {
  width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #1f2937;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #4b5563;
  border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>
