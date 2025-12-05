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
          ℹ️ Demo con simulación basada en rutas y horarios reales de <strong>Guaguas Municipales</strong> (amarillo - urbanas), <strong>Global</strong> (azul - interurbanas) y <strong>Líneas Nocturnas</strong> (morado - L1, L2, L3, 64, 65). 
          Las guaguas solo aparecen dentro de sus horarios operativos y se mueven por rutas geográficamente precisas de Gran Canaria.
          <br><small class="opacity-80">Nota: Gran Canaria no dispone de feeds GTFS públicos. Los datos son simulados con máxima fidelidad a la realidad.</small>
        </InfoBanner>
      </div>

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

      <!-- Contenedor del mapa y árbol lateral -->
      <div class="relative">
        <!-- Botón para toggle del árbol -->
        <button 
          @click="treeVisible = !treeVisible"
          class="absolute top-4 right-4 z-[1000] bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-lg shadow-lg transition-all"
          :class="{ 'right-4 sm:right-[280px] md:right-[320px]': treeVisible }"
        >
          <el-icon v-if="treeVisible"><ArrowRight /></el-icon>
          <el-icon v-else><ArrowLeft /></el-icon>
        </button>

        <!-- Árbol lateral (componente extraído) -->
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
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { LMap, LTileLayer, LMarker, LPopup, LPolyline } from '@vue-leaflet/vue-leaflet';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { ElIcon } from 'element-plus';
import { ArrowRight, ArrowLeft } from '@element-plus/icons-vue';
import 'element-plus/dist/index.css';

// Components
import InfoBanner from '@/components/InfoBanner.vue';
import { BusLegend, BusLinesTree, BusPopup, BusStatsPanel } from '@/components/guaguas';

// Composables
import { useBusMap } from '@/composables/useBusMap';
import { useBusSchedule } from '@/composables/useBusSchedule';
import { useBusSelection } from '@/composables/useBusSelection';

// Data
import { MAIN_LINES, BUS_LINES, COMPANY_COLORS, getRoutes } from '@/data/guaguas';
import { getParada } from '@/data/guaguas/paradas';

// Leaflet icons
import iconUrl from 'leaflet/dist/images/marker-icon.png';
import iconRetinaUrl from 'leaflet/dist/images/marker-icon-2x.png';
import shadowUrl from 'leaflet/dist/images/marker-shadow.png';

// Configurar los íconos de Leaflet con assets locales
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl,
  iconUrl,
  shadowUrl,
});

// Initialize composables
const { mapOptions, getResponsiveZoom, createBusIcon } = useBusMap();
const { isNightTime, isInService } = useBusSchedule();

// Map state
const center = ref([28.050, -15.450]); // Centrado en zona media de Gran Canaria
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
const expandedKeys = ref(['municipales', 'global']);

// Initialize bus selection composable
const {
  selectedLines,
  exclusiveMode,
  exclusiveLine,
  isLineSelected,
  updateBusVisibility
} = useBusSelection(buses, hiddenBusIds);

// Click tracking for double-click detection (used in handleNodeClick)
const lastClickTime = ref(0);
const lastClickedLine = ref(null);
const previousHiddenBusIds = ref(new Set());
const previousSelectedLines = ref(new Set());

// Selected route visualization
const selectedRoute = ref(null);
const selectedRouteColor = ref('#FDB913');

// Available bus lines - EXPANDIDO (según guaguas.com y guaguasglobal.com)
const busLines = [
  // Municipales urbanas (guaguas.com)
  '1', '2', '6', '7', '8', '9', '10', '11', '12', '13', '17', '18', '19', '20', '21', '22', '24', '25', '26', '33', '35', '41', '44', '45', '47', '48', '49', '50', '51', '52', '53', '54', '55', '70', '80', '81', '82', '84', '91',
  // Global interurbanas (guaguasglobal.com)
  '1', '5', '10', '11', '12', '15', '18', '21', '25', '30', '32', '38', '41', '45', '50', '55', '60', '66', '80', '90', '91', '105',
  // Nocturnas
  'L1', 'L2', 'L3', '64', '65'
];

// PARADAS y getParada ahora se importan desde data/guaguas/paradas.js
// Las rutas se importan desde data/guaguas/routes.js

/**
 * Generate simulated buses based on real routes
 * Data sourced from https://www.guaguas.com and http://globalsu.net
 * Note: No public GTFS feed available for Gran Canaria
 */
const generateBuses = () => {
  // Obtener rutas desde el archivo de datos
  const routes = getRoutes();

  // Generar múltiples guaguas por línea solo si están en servicio
  const allBuses = [];
  
  // Primero obtenemos las rutas OSRM para cada línea (async)
  for (const route of routes) {
    // Verificar si la línea debe estar en servicio
    if (!isInService(route.type, route.line)) {
      continue; // Saltar esta línea si no está en servicio
    }
    
    // Número de guaguas por línea y sentido
    const busesPerDirection = route.type === 'interurban' ? 1 : (route.type === 'night' ? 1 : 2);
    const lineIndex = routes.indexOf(route);
    
    // Crear guaguas en ambos sentidos (ida y vuelta)
    const directions = ['outbound', 'inbound']; // ida y vuelta
    
    for (const [dirIndex, tripDirection] of directions.entries()) {
      // Obtener coordenadas de ruta ESPECÍFICAS para cada sentido (NO invertir)
      const routeCoords = tripDirection === 'outbound' 
        ? route.routeCoordsIda 
        : route.routeCoordsVuelta;
      
      // Validar que existen las coordenadas
      if (!routeCoords || routeCoords.length < 2) {
        console.warn(`Ruta ${route.line} ${tripDirection} no tiene suficientes puntos válidos`);
        continue;
      }
      
      // Origen y destino según el sentido
      const origin = tripDirection === 'outbound' ? route.origin : route.destination;
      const destination = tripDirection === 'outbound' ? route.destination : route.origin;
      
      // Paradas según el sentido (ya definidas por separado)
      const stops = tripDirection === 'outbound' ? route.stopsIda : route.stopsVuelta;
      
      for (let i = 0; i < busesPerDirection; i++) {
        // Distribuir guaguas uniformemente a lo largo de la ruta
        const totalBuses = busesPerDirection;
        const spacing = 1 / (totalBuses + 1);
        const routeProgress = spacing * (i + 1);
        
        // Calcular índice exacto en la ruta
        const exactIndex = routeProgress * (routeCoords.length - 1);
        const routeIndex = Math.floor(exactIndex);
        const nextRouteIndex = Math.min(routeIndex + 1, routeCoords.length - 1);
        
        const [lat1, lng1] = routeCoords[routeIndex];
        const [lat2, lng2] = routeCoords[nextRouteIndex];
        
        // Interpolar posición exacta entre dos puntos (SIN variación aleatoria)
        const segmentProgress = exactIndex - routeIndex;
        const finalLat = lat1 + (lat2 - lat1) * segmentProgress;
        const finalLng = lng1 + (lng2 - lng1) * segmentProgress;
        
        // Calcular ángulo de dirección para orientar el icono
        const angle = Math.atan2(lng2 - lng1, lat2 - lat1) * (180 / Math.PI);
        
        const delayed = Math.random() > 0.85; // 15% probabilidad de retraso
        
        // Calcular parada actual basada en el progreso
        const currentStopIndex = Math.floor(routeProgress * stops.length);
        const nextStop = stops[Math.min(currentStopIndex, stops.length - 1)];
        
        allBuses.push({
          id: `bus-${lineIndex}-${dirIndex}-${i}`,
          line: route.line,
          type: route.type,
          company: route.company,
          origin: origin,
          destination: destination,
          tripDirection: tripDirection, // 'outbound' (ida) o 'inbound' (vuelta)
          stops: stops, // Lista de paradas para esta dirección
          nextStop: nextStop,
          lat: finalLat,
          lng: finalLng,
          speed: route.type === 'interurban' ? 0.025 : 0.02, // Factor de velocidad
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

// Generar estructura de árbol para Element Plus
const treeData = computed(() => {
  const municipalesBuses = buses.value.filter(b => b.company === 'municipales' && b.type !== 'night');
  const globalBuses = buses.value.filter(b => b.company === 'global');
  const nightBusesList = buses.value.filter(b => b.type === 'night');
  
  // Agrupar por línea
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
      id: 'global',
      label: `Global (${globalBuses.length})`,
      type: 'company',
      children: Object.entries(globalGrouped).map(([line, busList]) => ({
        id: `global-line-${line}`,
        label: `Línea ${line} (${busList.length})`,
        type: 'line',
        line: line,
        company: 'global',
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
      label: `Líneas Nocturnas (${nightBusesList.length})`,
      type: 'company',
      children: Object.entries(nightGrouped).map(([line, busList]) => ({
        id: `night-line-${line}`,
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
    }
  ];
});

/**
 * Obtener ruta real usando OSRM (Open Source Routing Machine)
 * Convierte puntos simples en una ruta que sigue carreteras reales
 */
const getRouteFromOSRM = async (coordinates) => {
  try {
    // Las coordenadas de entrada (PARADAS) ya están verificadas
    if (coordinates.length < 2) {
      console.warn('No hay suficientes coordenadas para calcular ruta');
      return coordinates;
    }
    
    // OSRM espera formato: lon,lat;lon,lat;...
    const coordString = coordinates
      .map(([lat, lng]) => `${lng},${lat}`)
      .join(';');
    
    // API pública de OSRM - routing profile "driving"
    const url = `https://router.project-osrm.org/route/v1/driving/${coordString}?overview=full&geometries=geojson`;
    
    const response = await fetch(url);
    const data = await response.json();
    
    if (data.code === 'Ok' && data.routes && data.routes[0]) {
      // Convertir coordenadas GeoJSON [lng, lat] a Leaflet [lat, lng]
      const routeCoordinates = data.routes[0].geometry.coordinates.map(
        ([lng, lat]) => [lat, lng]
      );
      
      // Solo filtrar si hay puntos claramente en el océano Atlántico (muy al este)
      const filteredRoute = routeCoordinates.filter(([lat, lng]) => {
        // Solo excluir puntos muy claramente en el mar (lng > -15.35)
        return lng <= -15.35;
      });
      
      return filteredRoute.length > 1 ? filteredRoute : coordinates;
    }
    
    // Si falla OSRM, devolver coordenadas originales
    return coordinates;
  } catch (error) {
    console.warn('Error obteniendo ruta de OSRM:', error);
    // Si hay error, devolver coordenadas originales
    return coordinates;
  }
};

// Cache de rutas para evitar llamadas repetidas a OSRM
const routeCache = new Map();

// Manejar clicks en el árbol
const handleNodeClick = async (data, node) => {
  const now = Date.now();
  const DOUBLE_CLICK_THRESHOLD = 300; // ms
  
  if (data.type === 'bus') {
    // Click en una guagua individual - mostrar solo esta guagua y su ruta
    const bus = buses.value.find(b => b.id === data.id);
    if (bus) {
      // Ocultar todas las demás guaguas
      buses.value.forEach(b => {
        if (b.id !== data.id) {
          hiddenBusIds.value.add(b.id);
        } else {
          hiddenBusIds.value.delete(b.id);
        }
      });
      
      // Obtener ruta real de OSRM
      const cacheKey = `${bus.company}-${bus.line}-${bus.tripDirection}`;
      let realRoute;
      
      if (routeCache.has(cacheKey)) {
        realRoute = routeCache.get(cacheKey);
      } else {
        const numPoints = Math.min(10, bus.routeCoords.length);
        const step = Math.floor(bus.routeCoords.length / numPoints);
        const keyPoints = [];
        
        for (let i = 0; i < bus.routeCoords.length; i += step) {
          keyPoints.push(bus.routeCoords[i]);
        }
        if (keyPoints[keyPoints.length - 1] !== bus.routeCoords[bus.routeCoords.length - 1]) {
          keyPoints.push(bus.routeCoords[bus.routeCoords.length - 1]);
        }
        
        realRoute = await getRouteFromOSRM(keyPoints);
        routeCache.set(cacheKey, realRoute);
      }
      
      selectedRoute.value = realRoute;
      selectedRouteColor.value = bus.type === 'night' ? '#9933FF' : (bus.company === 'municipales' ? '#FDB913' : '#0066CC');
      selectedBusId.value = bus.id;
    }
    
    // Forzar reactividad
    hiddenBusIds.value = new Set(hiddenBusIds.value);
    return;
  }
  
  if (data.type === 'line') {
    const lineKey = `${data.company}-${data.line}`;
    const isDoubleClick = lastClickedLine.value === lineKey && (now - lastClickTime.value) < DOUBLE_CLICK_THRESHOLD;
    
    if (isDoubleClick) {
      // DOBLE CLICK - Modo exclusivo
      if (exclusiveMode.value && exclusiveLine.value?.line === data.line && exclusiveLine.value?.company === data.company) {
        // Ya estamos en modo exclusivo de esta línea - SALIR del modo exclusivo
        exclusiveMode.value = false;
        exclusiveLine.value = null;
        
        // Restaurar estado anterior
        selectedLines.value = new Set(previousSelectedLines.value);
        hiddenBusIds.value = new Set(previousHiddenBusIds.value);
        
        // Si no había seleccionadas antes, mostrar principales
        if (selectedLines.value.size === 0) {
          updateBusVisibility();
        }
      } else {
        // ENTRAR en modo exclusivo
        // Guardar estado actual antes de entrar
        previousSelectedLines.value = new Set(selectedLines.value);
        previousHiddenBusIds.value = new Set(hiddenBusIds.value);
        
        // Activar modo exclusivo
        exclusiveMode.value = true;
        exclusiveLine.value = { line: data.line, company: data.company };
        
        // Seleccionar SOLO esta línea
        selectedLines.value = new Set([lineKey]);
        
        // Ocultar todas las guaguas excepto las de esta línea
        const newHiddenBusIds = new Set();
        buses.value.forEach(bus => {
          if (bus.line !== data.line || bus.company !== data.company) {
            newHiddenBusIds.add(bus.id);
          }
        });
        hiddenBusIds.value = newHiddenBusIds;
      }
    } else {
      // CLICK SIMPLE - Agregar/quitar de la selección
      if (exclusiveMode.value) {
        // Si estamos en modo exclusivo, salir primero
        exclusiveMode.value = false;
        exclusiveLine.value = null;
        selectedLines.value = new Set(previousSelectedLines.value);
      }
      
      // Toggle la línea en la selección
      if (selectedLines.value.has(lineKey)) {
        selectedLines.value.delete(lineKey);
      } else {
        selectedLines.value.add(lineKey);
      }
      
      // Actualizar visibilidad de buses
      updateBusVisibility();
    }
    
    // Actualizar tracking de clicks
    lastClickTime.value = now;
    lastClickedLine.value = lineKey;
    
    // Limpiar ruta seleccionada
    selectedRoute.value = null;
    
    // Forzar reactividad
    selectedLines.value = new Set(selectedLines.value);
    return;
  }
  
  if (data.type === 'company') {
    // Click en una empresa - toggle todas las líneas de esa empresa
    const companyId = data.id;
    const companyBuses = buses.value.filter(b => {
      if (companyId === 'night') {
        return b.type === 'night';
      }
      return b.company === companyId && b.type !== 'night';
    });
    
    // Obtener todas las líneas únicas de esta empresa
    const companyLineKeys = new Set();
    companyBuses.forEach(bus => {
      companyLineKeys.add(`${bus.company}-${bus.line}`);
    });
    
    // Verificar si todas están seleccionadas
    const allSelected = [...companyLineKeys].every(key => selectedLines.value.has(key));
    
    if (allSelected) {
      // Deseleccionar todas
      companyLineKeys.forEach(key => selectedLines.value.delete(key));
    } else {
      // Seleccionar todas
      companyLineKeys.forEach(key => selectedLines.value.add(key));
    }
    
    // Salir del modo exclusivo si estaba activo
    if (exclusiveMode.value) {
      exclusiveMode.value = false;
      exclusiveLine.value = null;
    }
    
    updateBusVisibility();
    
    // Limpiar ruta seleccionada
    selectedRoute.value = null;
    
    // Forzar reactividad
    selectedLines.value = new Set(selectedLines.value);
  }
};

// Manejar expansión de nodos del árbol
const handleNodeExpand = (data, node) => {
  if (!expandedKeys.value.includes(node.key)) {
    expandedKeys.value.push(node.key);
  }
};

// Manejar colapso de nodos del árbol
const handleNodeCollapse = (data, node) => {
  expandedKeys.value = expandedKeys.value.filter(k => k !== node.key);
};

// Simular movimiento realista de guaguas siguiendo rutas reales
const updateBusPositions = () => {
  buses.value = buses.value.filter(bus => {
    // Verificar si el bus sigue en servicio según la hora
    if (!isInService(bus.type, bus.line)) {
      return false; // Eliminar buses fuera de servicio
    }
    return true;
  }).map(bus => {
    const currentIndex = bus.currentRouteIndex || 0;
    const progress = bus.routeProgress || 0;
    
    // Velocidad variable: interurbanas más rápidas
    const baseSpeed = bus.type === 'interurban' ? 0.035 : 0.025;
    // Añadir pequeña variación para que no se muevan todas igual
    const speedVariation = 1 + (Math.sin(Date.now() / 1000 + bus.id.charCodeAt(4)) * 0.1);
    const speedFactor = baseSpeed * speedVariation;
    
    let newProgress = progress + speedFactor;
    let newIndex = currentIndex;
    
    // Si completó el segmento actual, avanzar al siguiente punto
    if (newProgress >= 1.0) {
      newProgress = newProgress - 1.0; // Mantener el exceso para continuidad
      newIndex = currentIndex + 1;
      
      // Si llegó al final de la ruta, volver al inicio (ruta circular)
      if (newIndex >= bus.routeCoords.length - 1) {
        newIndex = 0;
        newProgress = 0;
      }
    }
    
    // Interpolar posición exacta entre dos puntos consecutivos
    const [lat1, lng1] = bus.routeCoords[newIndex];
    const nextIndex = Math.min(newIndex + 1, bus.routeCoords.length - 1);
    const [lat2, lng2] = bus.routeCoords[nextIndex];
    
    const newLat = lat1 + (lat2 - lat1) * newProgress;
    const newLng = lng1 + (lng2 - lng1) * newProgress;
    
    // Calcular ángulo de dirección para orientar el icono
    const angle = Math.atan2(lng2 - lng1, lat2 - lat1) * (180 / Math.PI);
    
    // Calcular progreso total en la ruta (0 a 1)
    const totalProgress = (newIndex + newProgress) / (bus.routeCoords.length - 1);
    
    // Calcular próxima parada basada en el progreso
    let nextStop = bus.nextStop;
    if (bus.stops && bus.stops.length > 0) {
      const stopIndex = Math.min(
        Math.floor(totalProgress * bus.stops.length),
        bus.stops.length - 1
      );
      nextStop = bus.stops[stopIndex];
    }
    
    // Actualizar tiempo a próxima parada (simulación)
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
  
  // Si no hay buses (todos fuera de servicio), regenerar para verificar horarios
  if (buses.value.length === 0) {
    buses.value = generateBuses();
  }
};

const filteredBuses = computed(() => {
  let filtered = buses.value;
  
  // Filtrar por línea si está seleccionada
  if (selectedLine.value) {
    filtered = filtered.filter(bus => bus.line === selectedLine.value);
  }
  
  // Excluir guaguas ocultas del árbol
  filtered = filtered.filter(bus => !hiddenBusIds.value.has(bus.id));
  
  return filtered;
});

const activeBuses = computed(() => buses.value);

const delayedBuses = computed(() => buses.value.filter(bus => bus.delayed));

const municipalesBuses = computed(() => buses.value.filter(bus => bus.company === 'municipales' && bus.type !== 'night'));

const globalBuses = computed(() => buses.value.filter(bus => bus.company === 'global'));

const nightBuses = computed(() => buses.value.filter(bus => bus.type === 'night'));

// Stats object for BusStatsPanel component
const busStats = computed(() => ({
  active: activeBuses.value.length,
  municipales: municipalesBuses.value.length,
  global: globalBuses.value.length,
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

onMounted(() => {
  buses.value = generateBuses();
  
  // Inicializar visibilidad: ocultar líneas que no son principales
  // Las líneas principales se muestran por defecto
  updateBusVisibility();
  
  // Update bus positions every 5 seconds
  updateInterval.value = setInterval(() => {
    updateBusPositions();
  }, 5000);
  
  // Agregar listener para resize
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  if (updateInterval.value) {
    clearInterval(updateInterval.value);
  }
  
  // Remover listener de resize
  window.removeEventListener('resize', handleResize);
});
</script>

<style scoped>
/* Estilo para iconos personalizados de bus sin fondo */
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

/* Transición del árbol lateral */
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

/* Estilos para el árbol de Element Plus */
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

/* Scrollbar personalizado para el árbol */
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
