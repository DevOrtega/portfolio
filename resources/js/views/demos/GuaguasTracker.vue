<template>
  <div class="guaguas-tracker min-h-screen bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
      <div class="mb-6">
        <h1 class="text-3xl font-bold mb-2">Seguimiento de Guaguas en Tiempo Real</h1>
        <p class="text-gray-400">Transporte público de Gran Canaria</p>
        <div class="mt-2 bg-yellow-900/30 border border-yellow-700 text-yellow-200 px-4 py-2 rounded text-sm">
          ℹ️ Demo con simulación basada en rutas y horarios reales de <strong>Guaguas Municipales</strong> (amarillo - urbanas) y <strong>Global</strong> (azul - interurbanas). 
          Las guaguas solo aparecen dentro de sus horarios operativos y se mueven por rutas geográficamente precisas de Gran Canaria.
          <br><small class="text-yellow-300/80">Nota: Gran Canaria no dispone de feeds GTFS públicos. Los datos son simulados con máxima fidelidad a la realidad.</small>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-4">
        <div class="bg-gray-800 p-4 rounded-lg">
          <h3 class="text-sm font-semibold mb-2">Filtrar por línea</h3>
          <select v-model="selectedLine" class="w-full bg-gray-700 text-white px-3 py-2 rounded">
            <option value="">Todas las líneas</option>
            <option v-for="line in busLines" :key="line" :value="line">Línea {{ line }}</option>
          </select>
        </div>
        
        <div class="bg-gray-800 p-4 rounded-lg">
          <h3 class="text-sm font-semibold mb-2">Guaguas activas</h3>
          <p class="text-2xl font-bold text-green-400">{{ activeBuses.length }}</p>
        </div>

        <div class="bg-gray-800 p-4 rounded-lg">
          <h3 class="text-sm font-semibold mb-2">Municipales</h3>
          <p class="text-2xl font-bold text-yellow-400">{{ municipalesBuses.length }}</p>
        </div>

        <div class="bg-gray-800 p-4 rounded-lg">
          <h3 class="text-sm font-semibold mb-2">Global</h3>
          <p class="text-2xl font-bold text-blue-400">{{ globalBuses.length }}</p>
        </div>

        <div class="bg-gray-800 p-4 rounded-lg">
          <h3 class="text-sm font-semibold mb-2">Con retrasos</h3>
          <p class="text-2xl font-bold text-red-400">{{ delayedBuses.length }}</p>
        </div>
      </div>

      <!-- Mensaje cuando no hay servicio -->
      <div v-if="activeBuses.length === 0" class="mb-4 bg-orange-900/30 border border-orange-700 text-orange-200 px-4 py-3 rounded text-sm">
        🌙 No hay guaguas en servicio en este momento. 
        <span v-if="isNightTime()">Las líneas urbanas operan de 06:00-23:30 y las interurbanas de 05:30-22:00.</span>
        <span v-else>Las líneas nocturnas (N1) solo operan viernes y sábados de 00:00-06:00.</span>
      </div>

      <!-- Contenedor del mapa y árbol lateral -->
      <div class="relative">
        <!-- Botón para toggle del árbol -->
        <button 
          @click="treeVisible = !treeVisible"
          class="absolute top-4 right-4 z-[1000] bg-gray-800 hover:bg-gray-700 text-white p-2 rounded-lg shadow-lg transition-all"
          :class="{ 'right-[320px]': treeVisible }"
        >
          <el-icon v-if="treeVisible"><ArrowRight /></el-icon>
          <el-icon v-else><ArrowLeft /></el-icon>
        </button>

        <!-- Árbol lateral -->
        <transition name="slide">
          <div 
            v-if="treeVisible"
            class="absolute top-0 right-0 w-80 h-[600px] bg-gray-800 border-l border-gray-700 z-[999] overflow-y-auto rounded-r-lg shadow-xl"
          >
            <div class="p-4 border-b border-gray-700 sticky top-0 bg-gray-800 z-10">
              <h3 class="font-bold text-lg mb-1">Filtro de Guaguas</h3>
              <p class="text-xs text-gray-400">Click para mostrar/ocultar en el mapa</p>
            </div>
            <div class="p-4">
              <el-tree
                ref="treeRef"
                :data="treeData"
                :props="{ children: 'children', label: 'label' }"
                node-key="id"
                default-expand-all
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

        <div class="bg-gray-800 rounded-lg overflow-hidden" style="height: 600px;">
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
          
          <!-- Marcadores de guaguas -->
          <l-marker
            v-for="bus in filteredBuses"
            :key="bus.id"
            :lat-lng="[bus.lat, bus.lng]"
            @click="selectBus(bus)"
          >
            <l-icon
              :icon-size="[40, 40]"
              :icon-anchor="[20, 20]"
            >
              <div class="bus-icon-container">
                <!-- SVG del autobús -->
                <svg 
                  v-if="bus.company === 'municipales'" 
                  xmlns="http://www.w3.org/2000/svg" 
                  viewBox="0 0 64 64" 
                  width="40" 
                  height="40"
                  class="bus-svg"
                >
                  <!-- Autobús amarillo para Municipales -->
                  <g>
                    <!-- Cuerpo del autobús -->
                    <rect x="8" y="16" width="48" height="32" rx="4" fill="#FDB913" stroke="#D49400" stroke-width="2"/>
                    <!-- Ventanas -->
                    <rect x="12" y="20" width="10" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <rect x="24" y="20" width="10" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <rect x="36" y="20" width="10" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <rect x="48" y="20" width="6" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <!-- Parabrisas delantero -->
                    <path d="M 48 20 L 54 16 L 54 32 L 48 32 Z" fill="#87CEEB" opacity="0.6"/>
                    <!-- Ruedas -->
                    <circle cx="16" cy="48" r="5" fill="#2C2C2C" stroke="#000" stroke-width="1"/>
                    <circle cx="48" cy="48" r="5" fill="#2C2C2C" stroke="#000" stroke-width="1"/>
                    <circle cx="16" cy="48" r="2" fill="#555"/>
                    <circle cx="48" cy="48" r="2" fill="#555"/>
                    <!-- Detalles -->
                    <rect x="10" y="34" width="44" height="2" fill="#D49400"/>
                    <!-- Faros -->
                    <circle cx="54" cy="22" r="2" fill="#FFE4B5"/>
                    <circle cx="54" cy="28" r="2" fill="#FFE4B5"/>
                    <!-- Número de línea -->
                    <rect x="20" y="38" width="24" height="8" rx="2" fill="white"/>
                    <text x="32" y="44" font-family="Arial" font-size="8" font-weight="bold" fill="#333" text-anchor="middle">{{ bus.line }}</text>
                  </g>
                </svg>
                <svg 
                  v-else-if="bus.company === 'global'" 
                  xmlns="http://www.w3.org/2000/svg" 
                  viewBox="0 0 64 64" 
                  width="40" 
                  height="40"
                  class="bus-svg"
                >
                  <!-- Autobús azul para Global -->
                  <g>
                    <!-- Cuerpo del autobús -->
                    <rect x="8" y="16" width="48" height="32" rx="4" fill="#0066CC" stroke="#004C99" stroke-width="2"/>
                    <!-- Ventanas -->
                    <rect x="12" y="20" width="10" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <rect x="24" y="20" width="10" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <rect x="36" y="20" width="10" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <rect x="48" y="20" width="6" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <!-- Parabrisas delantero -->
                    <path d="M 48 20 L 54 16 L 54 32 L 48 32 Z" fill="#87CEEB" opacity="0.6"/>
                    <!-- Ruedas -->
                    <circle cx="16" cy="48" r="5" fill="#2C2C2C" stroke="#000" stroke-width="1"/>
                    <circle cx="48" cy="48" r="5" fill="#2C2C2C" stroke="#000" stroke-width="1"/>
                    <circle cx="16" cy="48" r="2" fill="#555"/>
                    <circle cx="48" cy="48" r="2" fill="#555"/>
                    <!-- Detalles -->
                    <rect x="10" y="34" width="44" height="2" fill="#004C99"/>
                    <!-- Faros -->
                    <circle cx="54" cy="22" r="2" fill="#FFE4B5"/>
                    <circle cx="54" cy="28" r="2" fill="#FFE4B5"/>
                    <!-- Número de línea -->
                    <rect x="20" y="38" width="24" height="8" rx="2" fill="white"/>
                    <text x="32" y="44" font-family="Arial" font-size="8" font-weight="bold" fill="#333" text-anchor="middle">{{ bus.line }}</text>
                  </g>
                </svg>
                <svg 
                  v-else
                  xmlns="http://www.w3.org/2000/svg" 
                  viewBox="0 0 64 64" 
                  width="40" 
                  height="40"
                  class="bus-svg"
                >
                  <!-- Autobús morado para nocturnas -->
                  <g>
                    <!-- Cuerpo del autobús -->
                    <rect x="8" y="16" width="48" height="32" rx="4" fill="#9933FF" stroke="#7722CC" stroke-width="2"/>
                    <!-- Ventanas -->
                    <rect x="12" y="20" width="10" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <rect x="24" y="20" width="10" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <rect x="36" y="20" width="10" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <rect x="48" y="20" width="6" height="12" rx="2" fill="#87CEEB" opacity="0.8"/>
                    <!-- Parabrisas delantero -->
                    <path d="M 48 20 L 54 16 L 54 32 L 48 32 Z" fill="#87CEEB" opacity="0.6"/>
                    <!-- Ruedas -->
                    <circle cx="16" cy="48" r="5" fill="#2C2C2C" stroke="#000" stroke-width="1"/>
                    <circle cx="48" cy="48" r="5" fill="#2C2C2C" stroke="#000" stroke-width="1"/>
                    <circle cx="16" cy="48" r="2" fill="#555"/>
                    <circle cx="48" cy="48" r="2" fill="#555"/>
                    <!-- Detalles -->
                    <rect x="10" y="34" width="44" height="2" fill="#7722CC"/>
                    <!-- Faros -->
                    <circle cx="54" cy="22" r="2" fill="#FFE4B5"/>
                    <circle cx="54" cy="28" r="2" fill="#FFE4B5"/>
                    <!-- Número de línea -->
                    <rect x="20" y="38" width="24" height="8" rx="2" fill="white"/>
                    <text x="32" y="44" font-family="Arial" font-size="8" font-weight="bold" fill="#333" text-anchor="middle">{{ bus.line }}</text>
                  </g>
                </svg>
                <!-- Indicador de retraso -->
                <svg 
                  v-if="bus.delayed" 
                  class="delay-indicator" 
                  xmlns="http://www.w3.org/2000/svg" 
                  viewBox="0 0 20 20" 
                  fill="currentColor"
                  width="16"
                  height="16"
                >
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
            </l-icon>
            
            <l-popup v-if="selectedBusId === bus.id">
              <div class="bus-popup">
                <h3 class="font-bold text-lg mb-2">Línea {{ bus.line }}</h3>
                <div class="space-y-1 text-sm">
                  <p><strong>Empresa:</strong> {{ getBusCompanyLabel(bus.company) }}</p>
                  <p><strong>Tipo:</strong> {{ getBusTypeLabel(bus.type) }}</p>
                  <p><strong>Desde:</strong> {{ bus.origin }}</p>
                  <p><strong>Hasta:</strong> {{ bus.destination }}</p>
                  <p><strong>Próxima parada:</strong> {{ bus.nextStop }}</p>
                  <p><strong>Tiempo estimado:</strong> 
                    <span :class="bus.delayed ? 'text-yellow-500' : 'text-green-500'">
                      {{ bus.timeToNext }} min
                    </span>
                  </p>
                  <p v-if="bus.delayed" class="text-yellow-500">
                    ⚠️ Retraso: {{ bus.delayMinutes }} minutos
                  </p>
                  <p class="text-gray-500">Última actualización: {{ bus.lastUpdate }}</p>
                </div>
              </div>
            </l-popup>
          </l-marker>
        </l-map>
      </div>
      </div><!-- Fin contenedor relativo -->

      <div class="mt-4 bg-gray-800 p-4 rounded-lg">
        <h3 class="font-semibold mb-2">Leyenda</h3>
        <div class="flex flex-wrap gap-4 text-sm">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-yellow-500"></div>
            <span>Guaguas Municipales (Urbanas)</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-blue-600"></div>
            <span>Global (Interurbanas)</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-purple-500"></div>
            <span>Líneas Nocturnas</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>Con retraso</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { LMap, LTileLayer, LMarker, LPopup, LIcon } from '@vue-leaflet/vue-leaflet';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { ElTree, ElButton, ElIcon } from 'element-plus';
import { ArrowRight, ArrowLeft } from '@element-plus/icons-vue';
import 'element-plus/dist/index.css';

// Configurar los íconos de Leaflet
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
});

// Centro de Gran Canaria (punto medio de la isla)
const center = ref([27.965, -15.60]);
const zoom = ref(10);
const mapOptions = {
  zoomControl: true,
  attributionControl: true,
  maxBounds: [[27.70, -15.90], [28.20, -15.30]], // Límites para evitar scroll fuera de GC
  maxBoundsViscosity: 0.8, // Hacer los límites flexibles pero con resistencia
  minZoom: 9 // Zoom mínimo para mantener la isla visible
};

// Límites geográficos de Gran Canaria para mantener guaguas dentro del mapa
const GRAN_CANARIA_BOUNDS = {
  north: 28.18,    // Norte de Las Palmas
  south: 27.75,    // Sur (Maspalomas/Mogán)
  east: -15.35,    // Este (Telde)
  west: -15.85     // Oeste (Agaete/La Aldea)
};

// Verificar si una coordenada está dentro de Gran Canaria
const isWithinBounds = (lat, lng) => {
  return lat >= GRAN_CANARIA_BOUNDS.south && 
         lat <= GRAN_CANARIA_BOUNDS.north &&
         lng >= GRAN_CANARIA_BOUNDS.west && 
         lng <= GRAN_CANARIA_BOUNDS.east;
};

// Verificar si una guagua debe estar en servicio según la hora actual
const isInService = (busType, lineNumber) => {
  const now = new Date();
  const hour = now.getHours();
  const dayOfWeek = now.getDay(); // 0 = Domingo, 6 = Sábado
  
  // Líneas nocturnas (solo viernes, sábado y vísperas de festivo, 00:00-06:00)
  if (busType === 'night') {
    const isWeekendNight = dayOfWeek === 0 || dayOfWeek === 6 || dayOfWeek === 5;
    return isWeekendNight && (hour >= 0 && hour < 6);
  }
  
  // Líneas urbanas: 06:00 - 23:30
  if (busType === 'urban') {
    return hour >= 6 && hour < 24;
  }
  
  // Líneas interurbanas: 05:30 - 22:00 (menos frecuencia fines de semana)
  if (busType === 'interurban') {
    if (dayOfWeek === 0) { // Domingo - servicio reducido
      return hour >= 7 && hour < 22;
    }
    return hour >= 5 && hour < 23;
  }
  
  return false;
};

const selectedLine = ref('');
const selectedBusId = ref(null);
const buses = ref([]);
const updateInterval = ref(null);

// Control del árbol lateral
const treeVisible = ref(true);
const treeRef = ref(null);
const selectedBusIds = ref(new Set()); // IDs de guaguas seleccionadas
const hiddenBusIds = ref(new Set()); // IDs de guaguas ocultas

// Líneas disponibles - Municipales y Global
const busLines = [
  // Guaguas Municipales (urbanas)
  '1', '2', '11', '12', '13', '17', '25',
  // Global (interurbanas) - usando sus números reales
  '1', '5', '30', '32', '60', '80', '91',
  // Nocturnas
  'N1'
];

// Generar datos simulados basados en rutas reales
// GUAGUAS MUNICIPALES: Líneas urbanas de Las Palmas (amarillas)
// GLOBAL: Líneas interurbanas que conectan toda Gran Canaria (azules)
// Datos obtenidos de https://www.guaguas.com y http://globalsu.net - Noviembre 2025
// Nota: No existe GTFS público disponible para Gran Canaria
const generateBuses = () => {
  const routes = [
    // ========== GUAGUAS MUNICIPALES (Urbanas - Amarillas) ==========
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
  routes.forEach((route, routeIndex) => {
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
      finalLat = Math.max(GRAN_CANARIA_BOUNDS.south, Math.min(GRAN_CANARIA_BOUNDS.north, finalLat));
      finalLng = Math.max(GRAN_CANARIA_BOUNDS.west, Math.min(GRAN_CANARIA_BOUNDS.east, finalLng));
      
      // Calcular dirección basada en la ruta
      const direction = Math.atan2(lng2 - lng1, lat2 - lat1) * (180 / Math.PI);
      
      const delayed = Math.random() > 0.75;
      
      allBuses.push({
        id: `bus-${routeIndex}-${i}`,
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
    // Click en una guagua individual
    if (hiddenBusIds.value.has(data.id)) {
      hiddenBusIds.value.delete(data.id);
    } else {
      hiddenBusIds.value.add(data.id);
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
  }
  
  // Forzar reactividad
  hiddenBusIds.value = new Set(hiddenBusIds.value);
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

const getBusCompanyLabel = (company) => {
  const labels = {
    municipales: 'Guaguas Municipales',
    global: 'Global'
  };
  return labels[company] || 'Desconocido';
};

const getBusTypeLabel = (type) => {
  const labels = {
    urban: 'Urbana',
    interurban: 'Interurbana',
    night: 'Nocturna'
  };
  return labels[type] || 'Desconocido';
};

const selectBus = (bus) => {
  selectedBusId.value = bus.id;
};

const isNightTime = () => {
  const hour = new Date().getHours();
  return hour >= 0 && hour < 6;
};

const onMapReady = () => {
  console.log('Map is ready');
};

onMounted(() => {
  buses.value = generateBuses();
  
  // Actualizar posiciones cada 5 segundos
  updateInterval.value = setInterval(() => {
    updateBusPositions();
  }, 5000);
});

onUnmounted(() => {
  if (updateInterval.value) {
    clearInterval(updateInterval.value);
  }
});
</script>

<style scoped>
.bus-icon-container {
  position: relative;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.bus-svg {
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
  transition: transform 0.2s ease;
}

.bus-svg:hover {
  transform: scale(1.1);
}

.delay-indicator {
  position: absolute;
  top: -6px;
  right: -6px;
  color: #FCD34D;
  background: white;
  border-radius: 50%;
  padding: 2px;
  filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.3));
  animation: pulse-warning 2s infinite;
}

@keyframes pulse-warning {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
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
