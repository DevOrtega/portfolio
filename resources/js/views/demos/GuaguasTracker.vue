<template>
  <div class="guaguas-tracker min-h-screen bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
      <div class="mb-6">
        <h1 class="text-3xl font-bold mb-2">{{ $t('guaguas.title') }}</h1>
        <p class="text-gray-400">{{ $t('guaguas.subtitle') }}</p>
        <InfoBanner type="info" class="mt-2">
          ℹ️ Demo con simulación basada en rutas y horarios reales de <strong>Guaguas Municipales</strong> (amarillo - urbanas) y <strong>Global</strong> (azul - interurbanas). 
          Las guaguas solo aparecen dentro de sus horarios operativos y se mueven por rutas geográficamente precisas de Gran Canaria.
          <br><small class="opacity-80">Nota: Gran Canaria no dispone de feeds GTFS públicos. Los datos son simulados con máxima fidelidad a la realidad.</small>
        </InfoBanner>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-4">
        <div class="bg-gray-800 p-3 sm:p-4 rounded-lg col-span-2 md:col-span-3 lg:col-span-1">
          <h3 class="text-xs sm:text-sm font-semibold mb-2">{{ $t('guaguas.filterByLine') }}</h3>
          <select v-model="selectedLine" class="w-full bg-gray-700 text-white px-2 sm:px-3 py-1.5 sm:py-2 rounded text-sm">
            <option value="">{{ $t('guaguas.allLines') }}</option>
            <option v-for="line in busLines" :key="line" :value="line">{{ $t('guaguas.line') }} {{ line }}</option>
          </select>
        </div>
        
        <StatsCard :title="$t('guaguas.activeBuses')" :value="activeBuses.length" color-class="text-green-400" />
        <StatsCard :title="$t('guaguas.municipales')" :value="municipalesBuses.length" color-class="text-yellow-400" />
        <StatsCard :title="$t('guaguas.global')" :value="globalBuses.length" color-class="text-blue-400" />
        <StatsCard :title="$t('guaguas.delayed')" :value="delayedBuses.length" color-class="text-red-400" />
      </div>

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

        <!-- Árbol lateral -->
        <transition name="slide">
          <div 
            v-if="treeVisible"
            class="absolute top-0 right-0 w-full sm:w-72 md:w-80 h-[400px] sm:h-[500px] md:h-[600px] lg:h-[700px] xl:h-[800px] bg-gray-800 border-l border-gray-700 z-[999] overflow-y-auto rounded-r-lg shadow-xl"
          >
            <div class="p-3 sm:p-4 border-b border-gray-700 sticky top-0 bg-gray-800 z-10">
              <h3 class="font-bold text-base sm:text-lg mb-1">{{ $t('guaguas.filterTitle') }}</h3>
              <p class="text-xs text-gray-400">{{ $t('guaguas.filterDescription') }}</p>
            </div>
            <div class="p-4">
              <el-tree
                ref="treeRef"
                :data="treeData"
                :props="{ children: 'children', label: 'label' }"
                node-key="id"
                :default-expanded-keys="expandedKeys"
                @node-expand="handleNodeExpand"
                @node-collapse="handleNodeCollapse"
                @node-click="handleNodeClick"
                class="bg-transparent text-white"
              >
                <template #default="{ node, data }">
                  <span class="flex items-center gap-2 w-full">
                    <span 
                      v-if="data.type === 'bus'"
                      class="w-3 h-3 rounded-full flex-shrink-0"
                      :class="{
                        'bg-gray-500': hiddenBusIds.has(data.id),
                        'bg-yellow-400': !hiddenBusIds.has(data.id) && data.bus.company === 'municipales',
                        'bg-blue-500': !hiddenBusIds.has(data.id) && data.bus.company === 'global'
                      }"
                    ></span>
                    <span 
                      v-else-if="data.type === 'line'"
                      class="w-3 h-3 rounded-full flex-shrink-0"
                      :class="{
                        'bg-yellow-400': data.company === 'municipales',
                        'bg-blue-500': data.company === 'global'
                      }"
                    ></span>
                    <span class="text-sm truncate" :class="{ 'opacity-50': data.type === 'bus' && hiddenBusIds.has(data.id) }">
                      {{ node.label }}
                    </span>
                  </span>
                </template>
              </el-tree>
            </div>
          </div>
        </transition>

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
          
          <!-- Ruta seleccionada -->
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
              <div class="bus-popup">
                <h3 class="font-bold text-lg mb-2">{{ $t('guaguas.line') }} {{ bus.line }}</h3>
                <div class="space-y-1 text-sm">
                  <p><strong>{{ $t('guaguas.company') }}:</strong> {{ getCompanyLabel(bus.company) }}</p>
                  <p><strong>{{ $t('guaguas.type') }}:</strong> {{ getBusTypeLabel(bus.type) }}</p>
                  <p><strong>{{ $t('guaguas.from') }}:</strong> {{ bus.origin }}</p>
                  <p><strong>{{ $t('guaguas.to') }}:</strong> {{ bus.destination }}</p>
                  <p><strong>{{ $t('guaguas.nextStop') }}:</strong> {{ bus.nextStop }}</p>
                  <p><strong>{{ $t('guaguas.estimatedTime') }}:</strong> 
                    <span :class="bus.delayed ? 'text-yellow-500' : 'text-green-500'">
                      {{ bus.timeToNext }} {{ $t('guaguas.minutes') }}
                    </span>
                  </p>
                  <p v-if="bus.delayed" class="text-yellow-500">
                    ⚠️ {{ $t('guaguas.delay') }}: {{ bus.delayMinutes }} {{ $t('guaguas.minutes') }}
                  </p>
                  <p class="text-gray-500">{{ $t('guaguas.lastUpdate') }}: {{ bus.lastUpdate }}</p>
                </div>
              </div>
            </l-popup>
          </l-marker>
        </l-map>
      </div>
      </div><!-- Fin contenedor relativo -->

      <div class="mt-4 bg-gray-800 p-4 rounded-lg">
        <h3 class="font-semibold mb-2">{{ $t('guaguas.legend') }}</h3>
        <div class="flex flex-wrap gap-4 text-sm">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-yellow-500"></div>
            <span>{{ $t('guaguas.municipalesUrban') }}</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-blue-600"></div>
            <span>{{ $t('guaguas.globalInterurban') }}</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-purple-500"></div>
            <span>{{ $t('guaguas.nightLines') }}</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ $t('guaguas.withDelay') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { LMap, LTileLayer, LMarker, LPopup, LPolyline } from '@vue-leaflet/vue-leaflet';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { ElTree, ElIcon } from 'element-plus';
import { ArrowRight, ArrowLeft } from '@element-plus/icons-vue';
import 'element-plus/dist/index.css';
import StatsCard from '../../components/StatsCard.vue';
import InfoBanner from '../../components/InfoBanner.vue';
import { useBusMap } from '../../composables/useBusMap';
import { useBusSchedule } from '../../composables/useBusSchedule';

    // Composables
    const { mapOptions, getResponsiveZoom, isWithinBounds, createBusIcon, getCompanyLabel, getBusTypeLabel, BOUNDS } = useBusMap();
    const { isNightTime, isInService } = useBusSchedule();// Importar íconos de Leaflet localmente
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

// Map state
const center = ref([27.965, -15.60]);
const zoom = ref(getResponsiveZoom());

// Handle window resize
const handleResize = () => {
  zoom.value = getResponsiveZoom();
};

// UI State
const selectedLine = ref('');
const selectedBusId = ref(null);
const buses = ref([]);
const updateInterval = ref(null);

// Sidebar tree control
const treeVisible = ref(true);
const treeRef = ref(null);
const selectedBusIds = ref(new Set());
const hiddenBusIds = ref(new Set());
const expandedKeys = ref(['municipales', 'global']);

// Selected route visualization
const selectedRoute = ref(null);
const selectedRouteColor = ref('#FDB913');

// Available bus lines
const busLines = [
  '1', '2', '11', '12', '13', '17', '25',  // Municipales (urban)
  '1', '5', '30', '32', '60', '80', '91',  // Global (interurban)
  'N1'                                      // Night line
];

/**
 * Generate simulated buses based on real routes
 * Data sourced from https://www.guaguas.com and http://globalsu.net
 * Note: No public GTFS feed available for Gran Canaria
 */
const generateBuses = () => {
  const routes = [
    // ========== GUAGUAS MUNICIPALES (Urban - Yellow) ==========
    { 
      line: '1', type: 'urban', company: 'municipales', origin: 'Santa Catalina', destination: 'San Telmo', 
      stops: ['Teatro', 'Parque San Telmo', 'Puerto'], color: '#FDB913',
      routeCoords: [[28.135, -15.431], [28.124, -15.430], [28.109, -15.416]]
    },
    { 
      line: '2', type: 'urban', company: 'municipales', origin: 'Puerto', destination: 'Escaleritas', 
      stops: ['Vegueta', 'San José', 'Escaleritas'], color: '#FDB913',
      routeCoords: [[28.109, -15.416], [28.100, -15.415], [28.089, -15.442]]
    },
    { 
      line: '12', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Tamaraceite', 
      stops: ['León y Castillo', 'Cruz del Señor', 'Tamaraceite Alto'], color: '#FDB913',
      routeCoords: [[28.109, -15.413], [28.125, -15.455], [28.145, -15.480]]
    },
    { 
      line: '17', type: 'urban', company: 'municipales', origin: 'Santa Catalina', destination: 'Jinámar', 
      stops: ['Miller Bajo', 'Cruz de Piedra', 'Jinámar Centro'], color: '#FDB913',
      routeCoords: [[28.135, -15.431], [28.121, -15.395], [28.105, -15.375]]
    },
    { 
      line: '25', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Ciudad del Campo', 
      stops: ['Schamann', 'San Cristóbal', 'Dragonal'], color: '#FDB913',
      routeCoords: [[28.109, -15.413], [28.130, -15.440], [28.155, -15.460]]
    },
    { 
      line: '11', type: 'urban', company: 'municipales', origin: 'Puerto', destination: 'Guanarteme', 
      stops: ['Alcaravaneras', 'Rehoyas', 'Escaleritas'], color: '#FDB913',
      routeCoords: [[28.109, -15.416], [28.120, -15.440], [28.132, -15.456]]
    },
    { 
      line: '13', type: 'urban', company: 'municipales', origin: 'San Telmo', destination: 'Las Rehoyas', 
      stops: ['Schamann', 'Vegueta', 'Lugo'], color: '#FDB913',
      routeCoords: [[28.109, -15.416], [28.100, -15.415], [28.125, -15.448]]
    },
    
    // ========== GLOBAL (Interurbanas - Azules) ==========
    { 
      line: '1', type: 'interurban', company: 'global', origin: 'Las Palmas', destination: 'Maspalomas', 
      stops: ['Ingenio', 'Vecindario', 'Playa del Inglés'], color: '#0066CC',
      routeCoords: [[28.109, -15.416], [27.958, -15.452], [27.845, -15.565], [27.760, -15.586]]
    },
    { 
      line: '5', type: 'interurban', company: 'global', origin: 'Las Palmas', destination: 'Gáldar', 
      stops: ['Arucas', 'Moya', 'Guía'], color: '#0066CC',
      routeCoords: [[28.109, -15.416], [28.115, -15.515], [28.135, -15.595], [28.148, -15.655]]
    },
    { 
      line: '30', type: 'interurban', company: 'global', origin: 'Las Palmas', destination: 'Puerto Rico', 
      stops: ['Arguineguín', 'Patalavaca', 'Amadores'], color: '#0066CC',
      routeCoords: [[28.109, -15.416], [27.920, -15.560], [27.790, -15.685]]
    },
    { 
      line: '32', type: 'interurban', company: 'global', origin: 'Las Palmas', destination: 'Mogán', 
      stops: ['Puerto Rico', 'Tauro', 'Puerto de Mogán'], color: '#0066CC',
      routeCoords: [[28.109, -15.416], [27.790, -15.685], [27.815, -15.765]]
    },
    { 
      line: '60', type: 'interurban', company: 'global', origin: 'Las Palmas', destination: 'Agaete', 
      stops: ['Gáldar', 'San Pedro', 'Puerto de las Nieves'], color: '#0066CC',
      routeCoords: [[28.109, -15.416], [28.148, -15.655], [28.095, -15.695]]
    },
    { 
      line: '80', type: 'interurban', company: 'global', origin: 'Las Palmas', destination: 'Aeropuerto', 
      stops: ['Telde', 'Ingenio', 'Terminal Sur'], color: '#0066CC',
      routeCoords: [[28.109, -15.416], [27.991, -15.412], [27.932, -15.386]]
    },
    { 
      line: '91', type: 'interurban', company: 'global', origin: 'Maspalomas', destination: 'Puerto Rico', 
      stops: ['San Agustín', 'Arguineguín', 'Patalavaca'], color: '#0066CC',
      routeCoords: [[27.760, -15.586], [27.845, -15.565], [27.790, -15.685]]
    },
    
    // ========== LÍNEAS NOCTURNAS - Guaguas Municipales ==========
    { 
      line: 'N1', type: 'night', company: 'municipales', origin: 'Santa Catalina', destination: 'Escaleritas', 
      stops: ['Teatro', 'Vegueta', 'Alcaravaneras'], color: '#9933FF',
      routeCoords: [[28.135, -15.431], [28.109, -15.413], [28.100, -15.415], [28.089, -15.442]]
    }
  ];

  // Generar múltiples guaguas por línea solo si están en servicio
  const allBuses = [];
  routes.forEach((route, lineIndex) => {
    // Verificar si la línea debe estar en servicio
    if (!isInService(route.type, route.line)) {
      return; // Saltar esta línea si no está en servicio
    }
    
    const busesPerLine = route.type === 'interurban' ? 2 : (route.type === 'night' ? 1 : 3);
    
    for (let i = 0; i < busesPerLine; i++) {
      // Posicionar guaguas en puntos de la ruta real
      const routeProgress = i / busesPerLine; // 0.0 a 1.0
      const routeIndex = Math.floor(routeProgress * (route.routeCoords.length - 1));
      const nextRouteIndex = Math.min(routeIndex + 1, route.routeCoords.length - 1);
      
      const [lat1, lng1] = route.routeCoords[routeIndex];
      const [lat2, lng2] = route.routeCoords[nextRouteIndex];
      
      // Interpolar entre dos puntos de la ruta
      const segmentProgress = (routeProgress * (route.routeCoords.length - 1)) - routeIndex;
      const baseLat = lat1 + (lat2 - lat1) * segmentProgress;
      const baseLng = lng1 + (lng2 - lng1) * segmentProgress;
      
      // Añadir pequeña variación aleatoria pero manteniendo dentro de límites
      let finalLat = baseLat + (Math.random() - 0.5) * 0.002;
      let finalLng = baseLng + (Math.random() - 0.5) * 0.002;
      
      // Asegurar que está dentro de los límites de Gran Canaria
      finalLat = Math.max(BOUNDS.south, Math.min(BOUNDS.north, finalLat));
      finalLng = Math.max(BOUNDS.west, Math.min(BOUNDS.east, finalLng));
      
      // Calcular dirección basada en la ruta
      const direction = Math.atan2(lng2 - lng1, lat2 - lat1) * (180 / Math.PI);
      
      const delayed = Math.random() > 0.75;
      
      allBuses.push({
        id: `bus-${lineIndex}-${i}`,
        line: route.line,
        type: route.type,
        company: route.company, // 'municipales' o 'global'
        origin: route.origin,
        destination: route.destination,
        nextStop: route.stops[Math.floor(Math.random() * route.stops.length)],
        lat: finalLat,
        lng: finalLng,
        speed: route.type === 'interurban' ? 0.0004 : 0.0002, // Interurbanas más rápidas
        direction: direction,
        routeCoords: route.routeCoords,
        currentRouteIndex: routeIndex,
        timeToNext: Math.floor(Math.random() * 8) + 2,
        delayed: delayed,
        delayMinutes: delayed ? Math.floor(Math.random() * 12) + 3 : 0,
        lastUpdate: new Date().toLocaleTimeString('es-ES'),
        color: route.color
      });
    }
  });
  
  return allBuses;
};

// Generar estructura de árbol para Element Plus
const treeData = computed(() => {
  const municipalesBuses = buses.value.filter(b => b.company === 'municipales');
  const globalBuses = buses.value.filter(b => b.company === 'global');
  
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
        children: busList.map((bus, idx) => ({
          id: bus.id,
          label: `Guagua ${line}-${idx + 1} → ${bus.destination}`,
          type: 'bus',
          bus: bus
        }))
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
        children: busList.map((bus, idx) => ({
          id: bus.id,
          label: `Guagua ${line}-${idx + 1} → ${bus.destination}`,
          type: 'bus',
          bus: bus
        }))
      }))
    }
  ];
});

// Manejar clicks en el árbol
const handleNodeClick = (data, node) => {
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
      
      // Mostrar la ruta de esta guagua
      selectedRoute.value = bus.routeCoords;
      selectedRouteColor.value = bus.company === 'municipales' ? '#FDB913' : '#0066CC';
      selectedBusId.value = bus.id;
    }
  } else if (data.type === 'line') {
    // Click en una línea - toggle todas las guaguas de esa línea
    const lineBuses = buses.value.filter(b => b.line === data.line && b.company === data.company);
    const allHidden = lineBuses.every(b => hiddenBusIds.value.has(b.id));
    
    lineBuses.forEach(bus => {
      if (allHidden) {
        hiddenBusIds.value.delete(bus.id);
      } else {
        hiddenBusIds.value.add(bus.id);
      }
    });
    
    // Limpiar ruta seleccionada
    selectedRoute.value = null;
  } else if (data.type === 'company') {
    // Click en una empresa - toggle todas las guaguas de esa empresa
    const companyBuses = buses.value.filter(b => b.company === data.id);
    const allHidden = companyBuses.every(b => hiddenBusIds.value.has(b.id));
    
    companyBuses.forEach(bus => {
      if (allHidden) {
        hiddenBusIds.value.delete(bus.id);
      } else {
        hiddenBusIds.value.add(bus.id);
      }
    });
    
    // Limpiar ruta seleccionada
    selectedRoute.value = null;
  }
  
  // Forzar reactividad
  hiddenBusIds.value = new Set(hiddenBusIds.value);
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
    // Movimiento a lo largo de la ruta predefinida
    const radians = (bus.direction * Math.PI) / 180;
    let newLat = bus.lat + Math.cos(radians) * bus.speed;
    let newLng = bus.lng + Math.sin(radians) * bus.speed;
    
    // Si sale de los límites de Gran Canaria, rebotar o reiniciar en la ruta
    if (!isWithinBounds(newLat, newLng)) {
      // Volver al inicio de la ruta
      const [startLat, startLng] = bus.routeCoords[0];
      newLat = startLat;
      newLng = startLng;
      
      // Recalcular dirección hacia el siguiente punto
      const [nextLat, nextLng] = bus.routeCoords[1] || bus.routeCoords[0];
      bus.direction = Math.atan2(nextLng - startLng, nextLat - startLat) * (180 / Math.PI);
      bus.currentRouteIndex = 0;
    } else {
      // Verificar si llegó cerca del siguiente punto de ruta
      const nextIndex = Math.min(bus.currentRouteIndex + 1, bus.routeCoords.length - 1);
      const [targetLat, targetLng] = bus.routeCoords[nextIndex];
      const distance = Math.sqrt(Math.pow(newLat - targetLat, 2) + Math.pow(newLng - targetLng, 2));
      
      if (distance < 0.005 && nextIndex < bus.routeCoords.length - 1) {
        // Avanzar al siguiente segmento de ruta
        bus.currentRouteIndex = nextIndex;
        const [nextNextLat, nextNextLng] = bus.routeCoords[nextIndex + 1] || bus.routeCoords[nextIndex];
        bus.direction = Math.atan2(nextNextLng - targetLng, nextNextLat - targetLat) * (180 / Math.PI);
      } else if (nextIndex === bus.routeCoords.length - 1 && distance < 0.005) {
        // Llegó al final, volver al inicio
        bus.currentRouteIndex = 0;
        const [startLat, startLng] = bus.routeCoords[0];
        newLat = startLat;
        newLng = startLng;
        const [nextLat, nextLng] = bus.routeCoords[1];
        bus.direction = Math.atan2(nextLng - startLng, nextLat - startLat) * (180 / Math.PI);
      }
    }
    
    // Actualizar tiempo a próxima parada
    let newTime = bus.timeToNext;
    if (Math.random() > 0.7) {
      newTime = Math.max(0, newTime - 1);
      if (newTime === 0) {
        newTime = Math.floor(Math.random() * 8) + 2;
      }
    }
    
    return {
      ...bus,
      lat: newLat,
      lng: newLng,
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

const municipalesBuses = computed(() => buses.value.filter(bus => bus.company === 'municipales'));

const globalBuses = computed(() => buses.value.filter(bus => bus.company === 'global'));

// Cambiar a usar company en lugar de type para el color
const getBusTypeClass = (company, type) => {
  if (type === 'night') return 'bg-purple-500';
  if (company === 'municipales') return 'bg-yellow-500'; // Amarillo para Guaguas Municipales
  if (company === 'global') return 'bg-blue-600'; // Azul para Global
  return 'bg-gray-500';
};

/**
 * Select a bus to view its details
 * @param {Object} bus - Bus object
 */
const selectBus = (bus) => {
  selectedBusId.value = bus.id;
};

const onMapReady = () => {
  console.log('Map is ready');
};

onMounted(() => {
  buses.value = generateBuses();
  
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
