<template>
  <div class="h-[400px] sm:h-[500px] md:h-[600px] w-full rounded-xl overflow-hidden shadow-2xl border border-gray-700 relative flex flex-col md:flex-row">
    <!-- Map Container -->
    <div class="flex-grow h-full relative z-0">
      <l-map ref="map" v-model:zoom="zoom" :center="center" :use-global-leaflet="false">
        <l-tile-layer
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
          layer-type="base"
          name="OpenStreetMap"
        ></l-tile-layer>

        <l-marker 
          v-for="atm in atms" 
          :key="atm.id" 
          :lat-lng="atm.coords"
        >
          <l-icon
            :icon-url="getIconUrl(atm.status)"
            :icon-size="[40, 40]"
            :icon-anchor="[20, 40]"
            :popup-anchor="[0, -40]"
          />
          <l-popup>
            <div class="text-center p-2">
              <h3 class="font-bold text-gray-900 text-lg mb-1">ATM #{{ atm.id }}</h3>
              <div class="flex items-center justify-center gap-2 mb-2">
                <span :class="getStatusColor(atm.status)" class="w-3 h-3 rounded-full"></span>
                <span class="text-sm font-medium uppercase">{{ $t('atm.status.' + atm.status) }}</span>
              </div>
              <div class="text-xs text-gray-500 border-t pt-2 mt-2">
                {{ $t('atm.lastPing') }}: {{ atm.lastPing }}
              </div>
            </div>
          </l-popup>
        </l-marker>
      </l-map>
    </div>
    
    <!-- Sidebar / Legend -->
    <div class="w-full md:w-80 bg-gray-900 border-t md:border-t-0 md:border-l border-gray-700 p-4 sm:p-6 flex flex-col z-10 overflow-y-auto max-h-[200px] md:max-h-none">
      <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
        <span class="animate-pulse text-green-500">●</span> {{ $t('atm.monitor') }}
      </h3>
      
      <!-- Stats -->
      <div class="grid grid-cols-3 gap-2 mb-6">
        <StatsCard :title="$t('atm.active')" :value="stats.active" color-class="text-green-400" />
        <StatsCard :title="$t('atm.maintenance')" :value="stats.maintenance" color-class="text-orange-400" />
        <StatsCard :title="$t('atm.down')" :value="stats.inactive" color-class="text-red-400" />
      </div>

      <!-- Activity Log -->
      <div class="flex-grow">
        <h4 class="text-sm font-bold text-gray-400 mb-3 uppercase tracking-wider">{{ $t('atm.recentEvents') }}</h4>
        <div class="space-y-3">
          <transition-group name="list">
            <div v-for="log in logs" :key="log.id" class="bg-gray-800/50 p-3 rounded border border-gray-700 text-sm flex items-start gap-3">
              <span :class="getStatusColor(log.status)" class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0"></span>
              <div>
                <span class="text-gray-300 font-medium">ATM #{{ log.atmId }}</span>
                <span class="text-gray-500"> {{ $t('atm.changedTo') }} </span>
                <span :class="getStatusTextColor(log.status)" class="font-bold">{{ $t('atm.status.' + log.status) }}</span>
                <div class="text-xs text-gray-600 mt-1">{{ log.time }}</div>
              </div>
            </div>
          </transition-group>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import "leaflet/dist/leaflet.css";
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { LMap, LTileLayer, LMarker, LPopup, LIcon } from "@vue-leaflet/vue-leaflet";
import StatsCard from './StatsCard.vue';

const { t } = useI18n();
const zoom = ref(11);
const center = ref([28.0000, -15.5500]); // Center of Gran Canaria

// Simulation Data
const atms = ref([]);
const logs = ref([]);
const maxLogs = 5;
let simulationInterval = null;

const statuses = ['active', 'inactive', 'maintenance'];

// Generate random ATMs across Gran Canaria
const generateATMs = () => {
  const count = 15;
  const baseLat = 28.1235;
  const baseLng = -15.4363;
  
  for (let i = 1; i <= count; i++) {
    // Random spread around the island
    const lat = 27.75 + Math.random() * (28.15 - 27.75);
    const lng = -15.80 + Math.random() * (-15.40 - -15.80);
    
    atms.value.push({
      id: i,
      coords: [lat, lng],
      status: statuses[Math.floor(Math.random() * statuses.length)],
      lastPing: new Date().toLocaleTimeString()
    });
  }
};

const getIconUrl = (status) => {
  return `/images/marker-${status}.svg`;
};

const getStatusColor = (status) => {
  switch(status) {
    case 'active': return 'bg-green-500';
    case 'inactive': return 'bg-red-500';
    case 'maintenance': return 'bg-orange-500';
    default: return 'bg-gray-500';
  }
};

const getStatusTextColor = (status) => {
  switch(status) {
    case 'active': return 'text-green-400';
    case 'inactive': return 'text-red-400';
    case 'maintenance': return 'text-orange-400';
    default: return 'text-gray-400';
  }
};

const stats = computed(() => {
  return {
    active: atms.value.filter(a => a.status === 'active').length,
    inactive: atms.value.filter(a => a.status === 'inactive').length,
    maintenance: atms.value.filter(a => a.status === 'maintenance').length
  };
});

const simulateChanges = () => {
  // Pick a random ATM
  const index = Math.floor(Math.random() * atms.value.length);
  const atm = atms.value[index];
  
  // Change status
  const newStatus = statuses[Math.floor(Math.random() * statuses.length)];
  
  if (atm.status !== newStatus) {
    atm.status = newStatus;
    atm.lastPing = new Date().toLocaleTimeString();
    
    // Add log
    logs.value.unshift({
      id: Date.now(),
      atmId: atm.id,
      status: newStatus,
      time: new Date().toLocaleTimeString()
    });
    
    if (logs.value.length > maxLogs) logs.value.pop();
  }
};

onMounted(() => {
  generateATMs();
  simulationInterval = setInterval(simulateChanges, 2000); // Change every 2 seconds
});

onUnmounted(() => {
  if (simulationInterval) clearInterval(simulationInterval);
});
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(-30px);
}
</style>
