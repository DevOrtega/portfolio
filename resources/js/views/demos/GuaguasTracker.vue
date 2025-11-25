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
const center = ref([28.050, -15.450]); // Centrado en zona media de Gran Canaria
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
    // Línea 1: Teatro - Puerto (por León y Castillo)
    { 
      line: '1', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Puerto', 
      stops: ['San Telmo', 'Usos Múltiples', 'León y Castillo', 'Mesa y López', 'Santa Catalina'], color: '#FDB913',
      routeCoords: [
        [28.1094, -15.4131], // Teatro Pérez Galdós
        [28.1080, -15.4155],
        [28.1066, -15.4191], // San Telmo
        [28.1072, -15.4200],
        [28.1083, -15.4221], // Venegas (Usos Múltiples)
        [28.1090, -15.4235],
        [28.1102, -15.4251], // León y Castillo, 22
        [28.1108, -15.4259],
        [28.1115, -15.4268], // León y Castillo, 50
        [28.1123, -15.4275],
        [28.1131, -15.4282], // León y Castillo (Metropole)
        [28.1145, -15.4295],
        [28.1158, -15.4308],
        [28.1170, -15.4318], // León y Castillo (Oficinas Municipales)
        [28.1182, -15.4330],
        [28.1195, -15.4343],
        [28.1209, -15.4355], // León y Castillo (Torre Las Palmas)
        [28.1222, -15.4367],
        [28.1235, -15.4378], // León y Castillo (Club Náutico)
        [28.1246, -15.4390],
        [28.1258, -15.4401], // Mesa y López
        [28.1270, -15.4390],
        [28.1284, -15.4378],
        [28.1298, -15.4365], // Juan Manuel Durán (Base Naval)
        [28.1310, -15.4348],
        [28.1320, -15.4335],
        [28.1330, -15.4320], // Juan Manuel Durán (Galicia)
        [28.1342, -15.4308],
        [28.1353, -15.4297],
        [28.1365, -15.4285], // Tomás Miller (Canteras)
        [28.1375, -15.4275],
        [28.1383, -15.4268],
        [28.1390, -15.4260], // Alfredo L. Jones
        [28.1397, -15.4248],
        [28.1405, -15.4235], // Woermann
        [28.1410, -15.4240],
        [28.1416, -15.4246], // Santa Catalina
        [28.1423, -15.4230],
        [28.1432, -15.4210], // Castillo de La Luz
        [28.1440, -15.4198],
        [28.1447, -15.4188],
        [28.1454, -15.4180]  // Manuel Becerra (Puerto)
      ]
    },
    // Línea 2: Guiniguada - Puerto (por Tomás Morales)
    { 
      line: '2', type: 'urban', company: 'municipales', origin: 'Guiniguada', destination: 'Puerto', 
      stops: ['Primero de Mayo', 'Obelisco', 'Tomás Morales', 'Pío XII', 'Mercado Central', 'Santa Catalina'], color: '#FDB913',
      routeCoords: [
        [28.0970, -15.4130], // Guiniguada
        [28.0975, -15.4135],
        [28.0980, -15.4140],
        [28.0985, -15.4145], // Primero de Mayo, 6
        [28.0990, -15.4148],
        [28.0995, -15.4151],
        [28.1000, -15.4155], // Primero de Mayo (Correos)
        [28.1010, -15.4160],
        [28.1020, -15.4165],
        [28.1030, -15.4170],
        [28.1040, -15.4175],
        [28.1050, -15.4180],
        [28.1055, -15.4185], // Plaza Constitución (Obelisco)
        [28.1062, -15.4195],
        [28.1070, -15.4205],
        [28.1078, -15.4215],
        [28.1085, -15.4225],
        [28.1090, -15.4230], // Tomás Morales, 69
        [28.1098, -15.4240],
        [28.1105, -15.4250],
        [28.1113, -15.4260],
        [28.1120, -15.4268],
        [28.1125, -15.4275], // Tomás Morales, 120
        [28.1135, -15.4285],
        [28.1145, -15.4295],
        [28.1155, -15.4305],
        [28.1165, -15.4315], // Emilio Ley (Piscinas Julio Navarro)
        [28.1175, -15.4325],
        [28.1185, -15.4335],
        [28.1195, -15.4345],
        [28.1205, -15.4355], // Pío XII (Colegio Teresiano)
        [28.1218, -15.4365],
        [28.1230, -15.4375],
        [28.1242, -15.4383],
        [28.1250, -15.4390], // Pío XII (Estadio Insular)
        [28.1262, -15.4385],
        [28.1274, -15.4378],
        [28.1284, -15.4370],
        [28.1290, -15.4365], // Galicia (Mercado Central)
        [28.1298, -15.4358],
        [28.1306, -15.4350],
        [28.1314, -15.4343],
        [28.1320, -15.4335], // Galicia, 32
        [28.1330, -15.4320],
        [28.1340, -15.4308],
        [28.1350, -15.4297],
        [28.1358, -15.4290],
        [28.1365, -15.4285], // Tomás Miller (Canteras)
        [28.1373, -15.4278],
        [28.1380, -15.4270],
        [28.1386, -15.4264],
        [28.1390, -15.4260], // Alfredo L. Jones
        [28.1396, -15.4252],
        [28.1401, -15.4244],
        [28.1405, -15.4235], // Woermann
        [28.1409, -15.4239],
        [28.1412, -15.4243],
        [28.1416, -15.4246], // Santa Catalina
        [28.1423, -15.4238],
        [28.1430, -15.4228],
        [28.1437, -15.4218],
        [28.1444, -15.4208],
        [28.1450, -15.4194],
        [28.1454, -15.4180]  // Manuel Becerra (Puerto)
      ]
    },
    // Línea 11: Teatro - Hospital Dr. Negrín (por La Feria)
    { 
      line: '11', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Hospital Dr. Negrín', 
      stops: ['Barranquillo Don Zoilo', 'Altavista', 'La Feria', 'El Pilar'], color: '#FDB913',
      routeCoords: [
        [28.1094, -15.4131], // Teatro
        [28.1088, -15.4155],
        [28.1082, -15.4178],
        [28.1076, -15.4200],
        [28.1070, -15.4220],
        [28.1064, -15.4238],
        [28.1058, -15.4245],
        [28.1050, -15.4250], // Barranquillo Don Zoilo
        [28.1052, -15.4265],
        [28.1054, -15.4280],
        [28.1056, -15.4295],
        [28.1058, -15.4308],
        [28.1060, -15.4320], // Altavista
        [28.1064, -15.4338],
        [28.1068, -15.4355],
        [28.1072, -15.4370],
        [28.1076, -15.4385],
        [28.1080, -15.4400], // Las Chumberas
        [28.1086, -15.4418],
        [28.1092, -15.4435],
        [28.1098, -15.4450],
        [28.1104, -15.4465],
        [28.1110, -15.4480], // La Feria
        [28.1117, -15.4498],
        [28.1124, -15.4515],
        [28.1130, -15.4530],
        [28.1136, -15.4542],
        [28.1140, -15.4550], // El Pilar
        [28.1150, -15.4568],
        [28.1160, -15.4585],
        [28.1170, -15.4600],
        [28.1180, -15.4615],
        [28.1190, -15.4628],
        [28.1200, -15.4640],
        [28.1210, -15.4652],
        [28.1220, -15.4664],
        [28.1230, -15.4675],
        [28.1240, -15.4685],
        [28.1250, -15.4695],
        [28.1260, -15.4703],
        [28.1270, -15.4710]  // Hospital Dr. Negrín
      ]
    },
    // Línea 12: Puerto - Hoya de La Plata (Cono Sur)
    { 
      line: '12', type: 'urban', company: 'municipales', origin: 'Puerto', destination: 'Hoya de La Plata', 
      stops: ['León y Castillo', 'Oficinas Municipales', 'Ciudad Justicia', 'Blas Cabrera'], color: '#FDB913',
      routeCoords: [
        [28.1454, -15.4180], // Puerto
        [28.1447, -15.4188],
        [28.1440, -15.4198],
        [28.1435, -15.4205],
        [28.1432, -15.4210],
        [28.1425, -15.4222],
        [28.1420, -15.4232],
        [28.1416, -15.4246], // Santa Catalina
        [28.1405, -15.4260],
        [28.1395, -15.4272],
        [28.1385, -15.4282],
        [28.1375, -15.4292],
        [28.1365, -15.4300],
        [28.1355, -15.4308],
        [28.1345, -15.4314],
        [28.1335, -15.4318],
        [28.1330, -15.4320],
        [28.1310, -15.4340],
        [28.1290, -15.4360],
        [28.1275, -15.4378],
        [28.1258, -15.4401], // Mesa y López
        [28.1240, -15.4385],
        [28.1225, -15.4370],
        [28.1209, -15.4355],
        [28.1192, -15.4340],
        [28.1180, -15.4328],
        [28.1170, -15.4318], // León y Castillo (Oficinas Municipales)
        [28.1155, -15.4305],
        [28.1140, -15.4290],
        [28.1125, -15.4275],
        [28.1110, -15.4262],
        [28.1100, -15.4250],
        [28.1080, -15.4243],
        [28.1060, -15.4238],
        [28.1040, -15.4232],
        [28.1020, -15.4228],
        [28.1000, -15.4224],
        [28.0990, -15.4220], // San José
        [28.0970, -15.4216],
        [28.0950, -15.4212],
        [28.0930, -15.4208],
        [28.0910, -15.4203],
        [28.0900, -15.4205],
        [28.0880, -15.4195],
        [28.0860, -15.4192],
        [28.0840, -15.4190],
        [28.0820, -15.4190], // Blas Cabrera
        [28.0800, -15.4189],
        [28.0780, -15.4188],
        [28.0760, -15.4188],
        [28.0740, -15.4189],
        [28.0720, -15.4191],
        [28.0705, -15.4192]  // Hoya de La Plata
      ]
    },
    // Línea 13: Mercado de Vegueta - Tres Palmas (Cono Sur)
    { 
      line: '13', type: 'urban', company: 'municipales', origin: 'Mercado Vegueta', destination: 'Tres Palmas', 
      stops: ['Dr. Hernán Pérez', 'Paseo San José', 'Blas Cabrera Felipe', 'Hoya de La Plata'], color: '#FDB913',
      routeCoords: [
        [28.0980, -15.4140], // Mercado de Vegueta
        [28.0975, -15.4145],
        [28.0970, -15.4150],
        [28.0965, -15.4155], // Alcalde Díaz Saavedra
        [28.0960, -15.4158],
        [28.0955, -15.4162],
        [28.0950, -15.4166],
        [28.0945, -15.4170], // Dr. Hernán Pérez de Grado, 6
        [28.0940, -15.4173],
        [28.0935, -15.4176],
        [28.0930, -15.4178],
        [28.0925, -15.4180], // Dr. Hernán Pérez (La Portadilla)
        [28.0920, -15.4183],
        [28.0915, -15.4186],
        [28.0910, -15.4188],
        [28.0905, -15.4190], // Paseo San José (Iglesia San José)
        [28.0900, -15.4191],
        [28.0895, -15.4193],
        [28.0890, -15.4194],
        [28.0885, -15.4195], // Paseo San José, 170
        [28.0880, -15.4196],
        [28.0875, -15.4198],
        [28.0870, -15.4199],
        [28.0865, -15.4200], // Paseo San José, 214
        [28.0860, -15.4201],
        [28.0855, -15.4202],
        [28.0850, -15.4202],
        [28.0845, -15.4202], // Paseo San José, 264
        [28.0840, -15.4201],
        [28.0835, -15.4201],
        [28.0830, -15.4200],
        [28.0825, -15.4200], // Paseo San José, 290
        [28.0820, -15.4199],
        [28.0815, -15.4197],
        [28.0810, -15.4196],
        [28.0805, -15.4195], // Blas Cabrera Felipe (Iglesia Sta. Clara)
        [28.0800, -15.4193],
        [28.0795, -15.4192],
        [28.0790, -15.4191],
        [28.0785, -15.4190], // Blas Cabrera Felipe (parking hosp. Materno)
        [28.0780, -15.4188],
        [28.0775, -15.4187],
        [28.0770, -15.4186],
        [28.0765, -15.4185], // Ciudad Deportiva Gran Canaria
        [28.0760, -15.4184],
        [28.0755, -15.4183],
        [28.0750, -15.4183],
        [28.0745, -15.4182], // Blas Cabrera Felipe, 14
        [28.0740, -15.4183],
        [28.0735, -15.4184],
        [28.0730, -15.4185],
        [28.0725, -15.4185], // Blas Cabrera Felipe (Carrefour)
        [28.0720, -15.4187],
        [28.0715, -15.4189],
        [28.0710, -15.4191],
        [28.0705, -15.4192], // Hoya de la Plata
        [28.0700, -15.4194],
        [28.0695, -15.4196],
        [28.0690, -15.4197],
        [28.0685, -15.4198]  // Tres Palmas
      ]
    },
    // Línea 17: Teatro - Auditorio (Zona Comercial)
    { 
      line: '17', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Auditorio', 
      stops: ['Triana', 'Mesa y López', 'El Rincón'], color: '#FDB913',
      routeCoords: [
        [28.1094, -15.4131], // Teatro Pérez Galdós
        [28.1088, -15.4145],
        [28.1082, -15.4158],
        [28.1076, -15.4172],
        [28.1070, -15.4182],
        [28.1066, -15.4191], // Triana (San Telmo)
        [28.1072, -15.4205],
        [28.1078, -15.4218],
        [28.1085, -15.4230],
        [28.1092, -15.4241],
        [28.1102, -15.4251], // León y Castillo
        [28.1112, -15.4262],
        [28.1122, -15.4273],
        [28.1132, -15.4283],
        [28.1142, -15.4292],
        [28.1150, -15.4300],
        [28.1162, -15.4312],
        [28.1174, -15.4324],
        [28.1186, -15.4336],
        [28.1198, -15.4347],
        [28.1209, -15.4355],
        [28.1221, -15.4366],
        [28.1233, -15.4377],
        [28.1245, -15.4389],
        [28.1258, -15.4401], // Mesa y López
        [28.1266, -15.4393],
        [28.1274, -15.4386],
        [28.1282, -15.4378],
        [28.1285, -15.4375], // Juan Rejón
        [28.1295, -15.4364],
        [28.1305, -15.4353],
        [28.1315, -15.4342],
        [28.1325, -15.4331],
        [28.1330, -15.4320],
        [28.1340, -15.4307],
        [28.1350, -15.4295],
        [28.1360, -15.4283],
        [28.1370, -15.4276],
        [28.1380, -15.4270], // Las Canteras
        [28.1390, -15.4260],
        [28.1400, -15.4248],
        [28.1408, -15.4236],
        [28.1416, -15.4228],
        [28.1420, -15.4220],
        [28.1423, -15.4225],
        [28.1426, -15.4232],
        [28.1430, -15.4240], // Alcaravaneras
        [28.1436, -15.4230],
        [28.1442, -15.4220],
        [28.1448, -15.4210],
        [28.1454, -15.4205],
        [28.1460, -15.4200]  // Auditorio Alfredo Kraus
      ]
    },
    // Línea 25: Auditorio - Campus Universitario (Tafira)
    { 
      line: '25', type: 'urban', company: 'municipales', origin: 'Auditorio', destination: 'Campus Universitario', 
      stops: ['Pío XII', 'Tomás Morales', 'Guanarteme', 'Tafira'], color: '#FDB913',
      routeCoords: [
        [28.1460, -15.4200], // Auditorio Alfredo Kraus
        [28.1454, -15.4215],
        [28.1448, -15.4228],
        [28.1442, -15.4240],
        [28.1436, -15.4252],
        [28.1430, -15.4263],
        [28.1424, -15.4274],
        [28.1420, -15.4280], // Guanarteme
        [28.1412, -15.4292],
        [28.1405, -15.4302],
        [28.1398, -15.4312],
        [28.1391, -15.4322],
        [28.1385, -15.4328],
        [28.1380, -15.4330],
        [28.1370, -15.4338],
        [28.1360, -15.4346],
        [28.1350, -15.4354],
        [28.1340, -15.4361],
        [28.1330, -15.4365],
        [28.1320, -15.4366],
        [28.1310, -15.4366],
        [28.1300, -15.4365],
        [28.1290, -15.4365],
        [28.1280, -15.4368],
        [28.1270, -15.4372],
        [28.1260, -15.4378],
        [28.1250, -15.4390], // Pío XII
        [28.1240, -15.4385],
        [28.1230, -15.4378],
        [28.1220, -15.4368],
        [28.1210, -15.4360],
        [28.1205, -15.4355],
        [28.1195, -15.4345],
        [28.1185, -15.4335],
        [28.1175, -15.4325],
        [28.1165, -15.4315],
        [28.1155, -15.4305],
        [28.1145, -15.4293],
        [28.1135, -15.4283],
        [28.1125, -15.4275], // Tomás Morales
        [28.1110, -15.4295],
        [28.1095, -15.4318],
        [28.1080, -15.4340],
        [28.1065, -15.4355],
        [28.1050, -15.4368],
        [28.1040, -15.4360],
        [28.1020, -15.4385],
        [28.1000, -15.4408],
        [28.0980, -15.4428],
        [28.0960, -15.4450],
        [28.0940, -15.4468],
        [28.0920, -15.4485],
        [28.0900, -15.4500],
        [28.0880, -15.4510],
        [28.0860, -15.4520],
        [28.0840, -15.4530],
        [28.0820, -15.4540],
        [28.0800, -15.4548],
        [28.0780, -15.4556],
        [28.0760, -15.4550],
        [28.0740, -15.4565],
        [28.0720, -15.4568],
        [28.0700, -15.4570],
        [28.0680, -15.4570],
        [28.0660, -15.4570],
        [28.0640, -15.4570]  // Campus Universitario
      ]
    },
    
    // ========== GLOBAL (Interurbanas - Azules) ==========
    // Línea 1: Las Palmas - Maspalomas (GC-1 por la costa este)
    { 
      line: '1', type: 'interurban', company: 'global', origin: 'Las Palmas', destination: 'Maspalomas', 
      stops: ['Telde', 'Vecindario', 'Maspalomas'], color: '#0066CC',
      routeCoords: [
        [28.1094, -15.4131], // Las Palmas (Teatro)
        [28.1000, -15.4140],
        [28.0900, -15.4145],
        [28.0800, -15.4148],
        [28.0700, -15.4150],
        [28.0600, -15.4152],
        [28.0500, -15.4155],
        [28.0400, -15.4160],
        [28.0300, -15.4168],
        [28.0200, -15.4178],
        [28.0100, -15.4188],
        [28.0000, -15.4195],
        [27.9920, -15.4198], // Telde
        [27.9840, -15.4200],
        [27.9760, -15.4205],
        [27.9680, -15.4212],
        [27.9600, -15.4222],
        [27.9520, -15.4235],
        [27.9440, -15.4250],
        [27.9360, -15.4268],
        [27.9280, -15.4288],
        [27.9200, -15.4310],
        [27.9120, -15.4335],
        [27.9040, -15.4362],
        [27.8960, -15.4392],
        [27.8880, -15.4424],
        [27.8800, -15.4458],
        [27.8720, -15.4488],
        [27.8697, -15.4500], // Vecindario
        [27.8620, -15.4530],
        [27.8540, -15.4565],
        [27.8460, -15.4605],
        [27.8380, -15.4650],
        [27.8300, -15.4700],
        [27.8220, -15.4755],
        [27.8140, -15.4815],
        [27.8060, -15.4880],
        [27.7980, -15.4950],
        [27.7900, -15.5025],
        [27.7820, -15.5105],
        [27.7750, -15.5190],
        [27.7690, -15.5280],
        [27.7640, -15.5375],
        [27.7600, -15.5475],
        [27.7570, -15.5580],
        [27.7550, -15.5685],
        [27.7530, -15.5750],
        [27.7500, -15.5800],
        [27.7470, -15.5830],
        [27.7437, -15.5860]  // Maspalomas/Faro
      ]
    },
    // Línea 30: Las Palmas - Maspalomas Directo (Superfaro)
    { 
      line: '30', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Maspalomas', 
      stops: ['Aeropuerto', 'Vecindario', 'Maspalomas'], color: '#0066CC',
      routeCoords: [
        [28.1416, -15.4246], // Santa Catalina
        [28.1350, -15.4260],
        [28.1280, -15.4272],
        [28.1210, -15.4285],
        [28.1140, -15.4300],
        [28.1094, -15.4131],
        [28.1020, -15.4145],
        [28.0950, -15.4152],
        [28.0880, -15.4158],
        [28.0810, -15.4163],
        [28.0750, -15.4150],
        [28.0680, -15.4155],
        [28.0610, -15.4160],
        [28.0540, -15.4163],
        [28.0470, -15.4165],
        [28.0400, -15.4165],
        [28.0330, -15.4170],
        [28.0260, -15.4175],
        [28.0190, -15.4178],
        [28.0120, -15.4180],
        [28.0050, -15.4180],
        [27.9985, -15.4188],
        [27.9920, -15.4198], // Telde
        [27.9850, -15.4185],
        [27.9780, -15.4170],
        [27.9710, -15.4158],
        [27.9650, -15.4150],
        [27.9590, -15.4130],
        [27.9530, -15.4105],
        [27.9480, -15.4075],
        [27.9450, -15.4050],
        [27.9410, -15.4020],
        [27.9370, -15.3990],
        [27.9340, -15.3950],
        [27.9320, -15.3910],
        [27.9318, -15.3863], // Aeropuerto
        [27.9300, -15.3920],
        [27.9270, -15.3990],
        [27.9230, -15.4060],
        [27.9180, -15.4140],
        [27.9150, -15.4100],
        [27.9100, -15.4180],
        [27.9050, -15.4250],
        [27.9000, -15.4310],
        [27.8950, -15.4360],
        [27.8900, -15.4300],
        [27.8840, -15.4380],
        [27.8780, -15.4445],
        [27.8720, -15.4500],
        [27.8697, -15.4500], // Vecindario
        [27.8630, -15.4570],
        [27.8560, -15.4650],
        [27.8490, -15.4730],
        [27.8420, -15.4810],
        [27.8350, -15.4880],
        [27.8280, -15.4950],
        [27.8210, -15.5020],
        [27.8140, -15.5090],
        [27.8070, -15.5160],
        [27.8000, -15.5230],
        [27.7930, -15.5295],
        [27.7860, -15.5350],
        [27.7800, -15.5350],
        [27.7730, -15.5420],
        [27.7660, -15.5495],
        [27.7600, -15.5600],
        [27.7550, -15.5680],
        [27.7500, -15.5750],
        [27.7470, -15.5800],
        [27.7450, -15.5830],
        [27.7437, -15.5860]  // Maspalomas
      ]
    },
    // Línea 60: Las Palmas - Aeropuerto (Directo)
    { 
      line: '60', type: 'interurban', company: 'global', origin: 'Las Palmas', destination: 'Aeropuerto', 
      stops: ['Santa Catalina', 'Telde', 'Terminal Sur'], color: '#0066CC',
      routeCoords: [
        [28.1094, -15.4131], // Las Palmas (Teatro)
        [28.1150, -15.4155],
        [28.1205, -15.4178],
        [28.1250, -15.4180],
        [28.1298, -15.4198],
        [28.1345, -15.4216],
        [28.1380, -15.4232],
        [28.1416, -15.4246], // Santa Catalina
        [28.1350, -15.4250],
        [28.1280, -15.4240],
        [28.1210, -15.4228],
        [28.1140, -15.4215],
        [28.1070, -15.4205],
        [28.1000, -15.4195],
        [28.0930, -15.4188],
        [28.0860, -15.4182],
        [28.0790, -15.4178],
        [28.0720, -15.4175],
        [28.0650, -15.4172],
        [28.0580, -15.4170],
        [28.0510, -15.4170],
        [28.0440, -15.4170],
        [28.0370, -15.4172],
        [28.0300, -15.4175],
        [28.0230, -15.4178],
        [28.0160, -15.4182],
        [28.0090, -15.4188],
        [28.0020, -15.4192],
        [27.9950, -15.4195],
        [27.9920, -15.4198], // Telde
        [27.9880, -15.4195],
        [27.9840, -15.4190],
        [27.9800, -15.4182],
        [27.9760, -15.4172],
        [27.9720, -15.4160],
        [27.9680, -15.4145],
        [27.9640, -15.4128],
        [27.9600, -15.4110],
        [27.9560, -15.4090],
        [27.9520, -15.4068],
        [27.9480, -15.4044],
        [27.9440, -15.4018],
        [27.9400, -15.3990],
        [27.9360, -15.3960],
        [27.9330, -15.3930],
        [27.9318, -15.3863]  // Aeropuerto
      ]
    },
    // Línea 80: Las Palmas - Telde (Directo)
    { 
      line: '80', type: 'interurban', company: 'global', origin: 'Las Palmas', destination: 'Telde', 
      stops: ['Santa Catalina', 'Jinámar', 'Telde Centro'], color: '#0066CC',
      routeCoords: [
        [28.1094, -15.4131], // Las Palmas (Teatro)
        [28.1120, -15.4140],
        [28.1145, -15.4150],
        [28.1170, -15.4160],
        [28.1195, -15.4170],
        [28.1200, -15.4180],
        [28.1230, -15.4195],
        [28.1260, -15.4208],
        [28.1285, -15.4220],
        [28.1300, -15.4220],
        [28.1330, -15.4228],
        [28.1360, -15.4236],
        [28.1390, -15.4242],
        [28.1416, -15.4246], // Santa Catalina
        [28.1350, -15.4245],
        [28.1280, -15.4238],
        [28.1210, -15.4225],
        [28.1140, -15.4210],
        [28.1070, -15.4198],
        [28.1000, -15.4188],
        [28.0930, -15.4180],
        [28.0860, -15.4172],
        [28.0790, -15.4165],
        [28.0720, -15.4158],
        [28.0650, -15.4152],
        [28.0580, -15.4148],
        [28.0520, -15.4115],
        [28.0507, -15.4050], // Jinámar
        [28.0460, -15.4080],
        [28.0410, -15.4105],
        [28.0360, -15.4125],
        [28.0310, -15.4140],
        [28.0260, -15.4152],
        [28.0210, -15.4162],
        [28.0160, -15.4170],
        [28.0110, -15.4178],
        [28.0060, -15.4185],
        [28.0010, -15.4192],
        [27.9960, -15.4196],
        [27.9920, -15.4198]  // Telde
      ]
    },
    // Línea 91: Santa Catalina - Maspalomas (Directo)
    { 
      line: '91', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Maspalomas', 
      stops: ['Telde', 'Vecindario', 'Maspalomas'], color: '#0066CC',
      routeCoords: [
        [28.1416, -15.4246], // Santa Catalina
        [28.1350, -15.4255],
        [28.1280, -15.4260],
        [28.1210, -15.4252],
        [28.1140, -15.4238],
        [28.1094, -15.4131],
        [28.1020, -15.4140],
        [28.0950, -15.4145],
        [28.0880, -15.4148],
        [28.0810, -15.4150],
        [28.0750, -15.4150],
        [28.0680, -15.4152],
        [28.0610, -15.4155],
        [28.0540, -15.4158],
        [28.0470, -15.4162],
        [28.0400, -15.4165],
        [28.0330, -15.4168],
        [28.0260, -15.4172],
        [28.0190, -15.4176],
        [28.0120, -15.4178],
        [28.0050, -15.4180],
        [27.9985, -15.4188],
        [27.9920, -15.4198], // Telde
        [27.9850, -15.4205],
        [27.9780, -15.4212],
        [27.9710, -15.4218],
        [27.9650, -15.4220],
        [27.9580, -15.4230],
        [27.9510, -15.4242],
        [27.9445, -15.4254],
        [27.9380, -15.4260],
        [27.9310, -15.4275],
        [27.9240, -15.4292],
        [27.9170, -15.4310],
        [27.9110, -15.4320],
        [27.9040, -15.4340],
        [27.8970, -15.4365],
        [27.8900, -15.4385],
        [27.8840, -15.4400],
        [27.8770, -15.4430],
        [27.8730, -15.4465],
        [27.8697, -15.4500], // Vecindario
        [27.8620, -15.4560],
        [27.8550, -15.4630],
        [27.8480, -15.4700],
        [27.8400, -15.4700],
        [27.8320, -15.4780],
        [27.8240, -15.4865],
        [27.8160, -15.4940],
        [27.8100, -15.4950],
        [27.8020, -15.5030],
        [27.7940, -15.5115],
        [27.7860, -15.5195],
        [27.7800, -15.5250],
        [27.7720, -15.5340],
        [27.7640, -15.5430],
        [27.7600, -15.5550],
        [27.7550, -15.5640],
        [27.7500, -15.5720],
        [27.7470, -15.5780],
        [27.7450, -15.5820],
        [27.7437, -15.5860]  // Maspalomas
      ]
    },
    
    // ========== LÍNEAS NOCTURNAS - Guaguas Municipales ==========
    { 
      line: 'N1', type: 'night', company: 'municipales', origin: 'Teatro', destination: 'Cono Sur', 
      stops: ['San Telmo', 'Vegueta', 'San José', 'Hoya de La Plata'], color: '#9933FF',
      routeCoords: [
        [28.109, -15.413], // Teatro
        [28.106, -15.419], // San Telmo
        [28.100, -15.415], // Vegueta
        [28.094, -15.418], // Paseo San José
        [28.088, -15.419],
        [28.082, -15.419],
        [28.076, -15.418],
        [28.072, -15.419]  // Hoya de La Plata
      ]
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

/**
 * Obtener ruta real usando OSRM (Open Source Routing Machine)
 * Convierte puntos simples en una ruta que sigue carreteras reales
 */
const getRouteFromOSRM = async (coordinates) => {
  try {
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
      return routeCoordinates;
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
      const cacheKey = `${bus.company}-${bus.line}`;
      let realRoute;
      
      if (routeCache.has(cacheKey)) {
        // Usar ruta cacheada
        realRoute = routeCache.get(cacheKey);
      } else {
        // Obtener ruta real usando solo puntos clave (inicio y fin)
        const keyPoints = [
          bus.routeCoords[0], // Origen
          bus.routeCoords[Math.floor(bus.routeCoords.length / 2)], // Punto medio
          bus.routeCoords[bus.routeCoords.length - 1] // Destino
        ];
        
        realRoute = await getRouteFromOSRM(keyPoints);
        routeCache.set(cacheKey, realRoute);
      }
      
      // Mostrar la ruta real de esta guagua
      selectedRoute.value = realRoute;
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
