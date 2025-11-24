<template>
  <div class="guaguas-tracker min-h-screen bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
      <div class="mb-6">
        <h1 class="text-3xl font-bold mb-2">Seguimiento de Guaguas en Tiempo Real</h1>
        <p class="text-gray-400">Transporte público de Gran Canaria</p>
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
          <h3 class="text-sm font-semibold mb-2">Con retrasos</h3>
          <p class="text-2xl font-bold text-yellow-400">{{ delayedBuses.length }}</p>
        </div>

        <div class="bg-gray-800 p-4 rounded-lg">
          <h3 class="text-sm font-semibold mb-2">Actualización</h3>
          <p class="text-sm text-gray-400">Cada 5 segundos</p>
        </div>
      </div>

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
              <div class="bus-marker" :class="getBusTypeClass(bus.type)">
                <div class="bus-number">{{ bus.line }}</div>
                <svg v-if="bus.delayed" class="delay-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
            </l-icon>
            
            <l-popup v-if="selectedBusId === bus.id">
              <div class="bus-popup">
                <h3 class="font-bold text-lg mb-2">Línea {{ bus.line }}</h3>
                <div class="space-y-1 text-sm">
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

      <div class="mt-4 bg-gray-800 p-4 rounded-lg">
        <h3 class="font-semibold mb-2">Leyenda</h3>
        <div class="flex flex-wrap gap-4 text-sm">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-blue-500"></div>
            <span>Guagua Urbana</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-green-500"></div>
            <span>Guagua Interurbana</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-purple-500"></div>
            <span>Guagua Nocturna</span>
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
import 'leaflet/dist/leaflet.css';

// Centro de Gran Canaria (Las Palmas)
const center = ref([28.1235, -15.4362]);
const zoom = ref(11);
const mapOptions = {
  zoomControl: true,
  attributionControl: true
};

const selectedLine = ref('');
const selectedBusId = ref(null);
const buses = ref([]);
const updateInterval = ref(null);

// Líneas de guaguas disponibles
const busLines = ['1', '2', '12', '17', '30', '41', '80', '91', 'N1'];

// Generar datos simulados de guaguas
const generateBuses = () => {
  const busTypes = ['urban', 'interurban', 'night'];
  const routes = [
    { line: '1', origin: 'Santa Catalina', destination: 'San Telmo', stops: ['Parque San Telmo', 'Teatro Pérez Galdós', 'Santa Catalina'] },
    { line: '2', origin: 'Puerto', destination: 'Escaleritas', stops: ['Teatro', 'Vegueta', 'San José'] },
    { line: '12', origin: 'Las Palmas', destination: 'Arucas', stops: ['Tamaraceite', 'Tenoya', 'Bañaderos'] },
    { line: '17', origin: 'Las Palmas', destination: 'Telde', stops: ['Jinámar', 'Cruz de Piedra', 'San Juan'] },
    { line: '30', origin: 'Intercambiador', destination: 'Maspalomas', stops: ['Vecindario', 'Arguineguín', 'Puerto Rico'] },
    { line: '41', origin: 'Las Palmas', destination: 'Gáldar', stops: ['Arucas', 'Moya', 'Guía'] },
    { line: '80', origin: 'Las Palmas', destination: 'Mogán', stops: ['Arguineguín', 'Puerto Rico', 'Amadores'] },
    { line: '91', origin: 'Las Palmas', destination: 'Agaete', stops: ['Santa María', 'San Lorenzo', 'Gáldar'] },
    { line: 'N1', origin: 'Santa Catalina', destination: 'San Telmo', stops: ['Parque', 'Teatro', 'Puerto'] }
  ];

  return routes.map((route, index) => ({
    id: `bus-${index}`,
    line: route.line,
    type: route.line.startsWith('N') ? 'night' : (parseInt(route.line) > 20 ? 'interurban' : 'urban'),
    origin: route.origin,
    destination: route.destination,
    nextStop: route.stops[Math.floor(Math.random() * route.stops.length)],
    lat: 28.1235 + (Math.random() - 0.5) * 0.2,
    lng: -15.4362 + (Math.random() - 0.5) * 0.2,
    timeToNext: Math.floor(Math.random() * 10) + 1,
    delayed: Math.random() > 0.7,
    delayMinutes: Math.random() > 0.7 ? Math.floor(Math.random() * 10) + 2 : 0,
    lastUpdate: new Date().toLocaleTimeString('es-ES')
  }));
};

// Simular movimiento de guaguas
const updateBusPositions = () => {
  buses.value = buses.value.map(bus => ({
    ...bus,
    lat: bus.lat + (Math.random() - 0.5) * 0.002,
    lng: bus.lng + (Math.random() - 0.5) * 0.002,
    timeToNext: Math.max(1, bus.timeToNext - 1 + Math.floor(Math.random() * 2)),
    lastUpdate: new Date().toLocaleTimeString('es-ES')
  }));
};

const filteredBuses = computed(() => {
  if (!selectedLine.value) return buses.value;
  return buses.value.filter(bus => bus.line === selectedLine.value);
});

const activeBuses = computed(() => buses.value);

const delayedBuses = computed(() => buses.value.filter(bus => bus.delayed));

const getBusTypeClass = (type) => {
  const classes = {
    urban: 'bg-blue-500',
    interurban: 'bg-green-500',
    night: 'bg-purple-500'
  };
  return classes[type] || 'bg-gray-500';
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
.bus-marker {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  border: 3px solid white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  animation: pulse 2s infinite;
}

.bus-number {
  font-weight: bold;
  font-size: 14px;
  color: white;
  z-index: 1;
}

.delay-icon {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 18px;
  height: 18px;
  color: #FCD34D;
  background: white;
  border-radius: 50%;
  padding: 2px;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
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
</style>
