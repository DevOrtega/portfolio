<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import L from 'leaflet';
import axios from 'axios';
import 'leaflet/dist/leaflet.css';
import LoadingSpinner from '@/components/LoadingSpinner.vue';
import { debounce } from 'lodash';

// State
const { t } = useI18n();
const mapContainer = ref(null);
const map = ref(null);
const loading = ref(false);
const error = ref(null);
const sidebarOpen = ref(true);

const startQuery = ref('');
const intermediateQuery = ref('');
const endQuery = ref('');
const startResults = ref([]);
const intermediateResults = ref([]);
const endResults = ref([]);
const startLocation = ref(null); // { lat, lng, display_name }
const intermediateLocation = ref(null);
const endLocation = ref(null);

const routes = ref([]); // Array of GeoJSON features
const selectedRouteIndex = ref(0);

// Markers and Layers
const markersLayer = ref(null);
const routesLayer = ref(null);

// Icons
const createIcon = (color) => {
    return L.divIcon({
        className: 'custom-div-icon',
        html: `<div style="background-color: ${color}; width: 16px; height: 16px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 8px rgba(0,0,0,0.5);"></div>`,
        iconSize: [16, 16],
        iconAnchor: [8, 8]
    });
};

// --- Initialization ---

onMounted(() => {
    if (!mapContainer.value) return;

    map.value = L.map(mapContainer.value).setView([27.96, -15.55], 11); 

    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map.value);

    markersLayer.value = L.layerGroup().addTo(map.value);
    routesLayer.value = L.layerGroup().addTo(map.value);

    // Allow clicking on map to set points if inputs are focused
    map.value.on('click', onMapClick);
});

onUnmounted(() => {
    if (map.value) map.value.remove();
});

watch(sidebarOpen, () => {
    // Wait for transition to finish
    setTimeout(() => {
        if (map.value) map.value.invalidateSize();
    }, 305);
});

// --- Map Interaction ---

const onMapClick = async (e) => {
    // If user clicks map, we can fill empty inputs intelligently
    // For simplicity in this demo, let's keep it form-driven or simple toggle
    // Maybe later.
};

// --- Geocoding ---

const searchPlace = async (query, type) => {
    if (!query || query.length < 3) return;
    
    try {
        // Limited to Gran Canaria bounding box approx
        const viewbox = '-15.85,27.70,-15.35,28.20';
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&viewbox=${viewbox}&bounded=1&limit=5`;
        
        const res = await axios.get(url);
        if (type === 'start') startResults.value = res.data;
        else if (type === 'intermediate') intermediateResults.value = res.data;
        else endResults.value = res.data;
    } catch (e) {
        console.error("Geocoding error", e);
    }
};

const debouncedSearchStart = debounce(() => searchPlace(startQuery.value, 'start'), 500);
const debouncedSearchIntermediate = debounce(() => searchPlace(intermediateQuery.value, 'intermediate'), 500);
const debouncedSearchEnd = debounce(() => searchPlace(endQuery.value, 'end'), 500);

const selectLocation = (place, type) => {
    const loc = {
        lat: parseFloat(place.lat),
        lng: parseFloat(place.lon),
        display_name: place.display_name.split(',')[0] // Short name
    };

    if (type === 'start') {
        startLocation.value = loc;
        startQuery.value = loc.display_name;
        startResults.value = [];
    } else if (type === 'intermediate') {
        intermediateLocation.value = loc;
        intermediateQuery.value = loc.display_name;
        intermediateResults.value = [];
    } else {
        endLocation.value = loc;
        endQuery.value = loc.display_name;
        endResults.value = [];
    }

    updateMarkers();
    if (startLocation.value && endLocation.value) {
        calculateRoutes();
    }
};

const updateMarkers = () => {
    markersLayer.value.clearLayers();
    
    const bounds = L.latLngBounds();
    let hasPoints = false;

    if (startLocation.value) {
        L.marker([startLocation.value.lat, startLocation.value.lng], { icon: createIcon('#10b981') })
         .bindPopup(t('hiking.popup.start'))
         .addTo(markersLayer.value);
        bounds.extend([startLocation.value.lat, startLocation.value.lng]);
        hasPoints = true;
    }
    
    if (intermediateLocation.value) {
        L.marker([intermediateLocation.value.lat, intermediateLocation.value.lng], { icon: createIcon('#f59e0b') })
         .bindPopup(t('hiking.popup.intermediate'))
         .addTo(markersLayer.value);
        bounds.extend([intermediateLocation.value.lat, intermediateLocation.value.lng]);
        hasPoints = true;
    }
    
    if (endLocation.value) {
        L.marker([endLocation.value.lat, endLocation.value.lng], { icon: createIcon('#ef4444') })
         .bindPopup(t('hiking.popup.destination'))
         .addTo(markersLayer.value);
        bounds.extend([endLocation.value.lat, endLocation.value.lng]);
        hasPoints = true;
    }

    // Fit bounds if points exist
    if (hasPoints) {
        map.value.fitBounds(bounds, { padding: [50, 50], maxZoom: 13 });
    }
};

// --- Routing ---

const calculateRoutes = async () => {
    loading.value = true;
    error.value = null;
    routes.value = [];
    selectedRouteIndex.value = 0;
    routesLayer.value.clearLayers();

    try {
        const params = {
            start: `${startLocation.value.lat},${startLocation.value.lng}`,
            end: `${endLocation.value.lat},${endLocation.value.lng}`
        };

        if (intermediateLocation.value) {
            params.intermediate = `${intermediateLocation.value.lat},${intermediateLocation.value.lng}`;
        }

        const response = await axios.get('/api/hiking/route', { params });

        const geoJson = response.data; // FeatureCollection
        if (geoJson.type === 'FeatureCollection') {
            routes.value = geoJson.features;
        } else {
            // Backwards compatibility if API returns single feature
            routes.value = [geoJson];
        }

        renderRoutes();

    } catch (e) {
        console.error(e);
        error.value = t('common.error');
    } finally {
        loading.value = false;
    }
};

const renderRoutes = () => {
    routesLayer.value.clearLayers();

    // Render all routes (non-selected as gray)
    routes.value.forEach((feature, index) => {
        const isSelected = index === selectedRouteIndex.value;
        const color = isSelected ? getDifficultyColor(feature.properties.difficulty) : '#4b5563';
        const opacity = isSelected ? 1 : 0.4;
        const weight = isSelected ? 5 : 3;
        const zIndex = isSelected ? 1000 : 1;

        const layer = L.geoJSON(feature, {
            style: { color, weight, opacity }
        }).addTo(routesLayer.value);

        if (isSelected) {
            map.value.fitBounds(layer.getBounds(), { padding: [50, 50] });
        }
    });
};

const selectRoute = (index) => {
    selectedRouteIndex.value = index;
    renderRoutes();
};

const getDifficultyColor = (diff) => {
    switch (diff) {
        case 'Fácil': return '#10b981'; // Green
        case 'Moderada': return '#f59e0b'; // Amber
        case 'Difícil': return '#ef4444'; // Red
        default: return '#3b82f6';
    }
};

const getTranslatedDifficulty = (diff) => {
    const map = {
        'Fácil': 'easy',
        'Moderada': 'moderate',
        'Difícil': 'hard'
    };
    const key = map[diff] || 'moderate';
    return t(`hiking.difficulty.${key}`);
};

</script>

<template>
    <div class="flex h-dvh bg-gray-900 text-white overflow-hidden relative">
        
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 h-full w-full sm:w-96 bg-gray-800 border-r border-gray-700 flex flex-col shadow-2xl z-30 transition-transform duration-300 transform"
             :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Header -->
            <div class="p-6 border-b border-gray-700 bg-gray-900 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-blue-500">
                        {{ $t('hiking.title') }}
                    </h1>
                    <p class="text-gray-400 text-sm mt-1">{{ $t('hiking.subtitle') }}</p>
                </div>
                <!-- Close Button (All devices) -->
                <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white p-1 rounded hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
            </div>

            <!-- Form -->
            <div class="p-4 space-y-4 overflow-y-auto flex-1">
                
                <!-- Origin Input -->
                <div class="relative">
                    <label class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1 block">{{ $t('hiking.origin') }}</label>
                    <div class="flex items-center bg-gray-700 rounded-lg border border-gray-600 focus-within:border-green-500 focus-within:ring-1 focus-within:ring-green-500">
                        <span class="pl-3 text-green-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <input 
                            v-model="startQuery"
                            @input="debouncedSearchStart"
                            type="text" 
                            class="w-full bg-transparent border-none focus:ring-0 text-white placeholder-gray-500 py-3 px-2"
                            :placeholder="$t('hiking.originPlaceholder')"
                        />
                        <button v-if="startQuery" @click="startQuery=''; startLocation=null; updateMarkers()" class="pr-3 text-gray-500 hover:text-white">✕</button>
                    </div>
                    <!-- Results Dropdown -->
                    <ul v-if="startResults.length > 0" class="absolute w-full bg-gray-700 mt-1 rounded-lg shadow-xl border border-gray-600 z-50 max-h-48 overflow-y-auto">
                        <li v-for="place in startResults" :key="place.place_id" 
                            @click="selectLocation(place, 'start')"
                            class="px-4 py-2 hover:bg-gray-600 cursor-pointer text-sm border-b border-gray-600/50 last:border-0">
                            {{ place.display_name }}
                        </li>
                    </ul>
                </div>

                <!-- Intermediate Input -->
                <div class="relative">
                    <label class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1 block">{{ $t('hiking.intermediate') }}</label>
                    <div class="flex items-center bg-gray-700 rounded-lg border border-gray-600 focus-within:border-amber-500 focus-within:ring-1 focus-within:ring-amber-500">
                        <span class="pl-3 text-amber-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </span>
                        <input 
                            v-model="intermediateQuery"
                            @input="debouncedSearchIntermediate"
                            type="text" 
                            class="w-full bg-transparent border-none focus:ring-0 text-white placeholder-gray-500 py-3 px-2"
                            :placeholder="$t('hiking.intermediatePlaceholder')"
                        />
                        <button v-if="intermediateQuery" @click="intermediateQuery=''; intermediateLocation=null; updateMarkers(); if(startLocation && endLocation) calculateRoutes()" class="pr-3 text-gray-500 hover:text-white">✕</button>
                    </div>
                    <!-- Results Dropdown -->
                    <ul v-if="intermediateResults.length > 0" class="absolute w-full bg-gray-700 mt-1 rounded-lg shadow-xl border border-gray-600 z-50 max-h-48 overflow-y-auto">
                        <li v-for="place in intermediateResults" :key="place.place_id" 
                            @click="selectLocation(place, 'intermediate')"
                            class="px-4 py-2 hover:bg-gray-600 cursor-pointer text-sm border-b border-gray-600/50 last:border-0">
                            {{ place.display_name }}
                        </li>
                    </ul>
                </div>

                <!-- Destination Input -->
                <div class="relative">
                    <label class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1 block">{{ $t('hiking.destination') }}</label>
                    <div class="flex items-center bg-gray-700 rounded-lg border border-gray-600 focus-within:border-red-500 focus-within:ring-1 focus-within:ring-red-500">
                        <span class="pl-3 text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-8a2 2 0 012-2h14a2 2 0 012 2v8M10 9H8a2 2 0 00-2 2v2m8-4h2a2 2 0 012 2v2m-8-2v6m0 0v6m0-6h6m-6 0H4"/></svg>
                        </span>
                        <input 
                            v-model="endQuery"
                            @input="debouncedSearchEnd"
                            type="text" 
                            class="w-full bg-transparent border-none focus:ring-0 text-white placeholder-gray-500 py-3 px-2"
                            :placeholder="$t('hiking.destinationPlaceholder')"
                        />
                        <button v-if="endQuery" @click="endQuery=''; endLocation=null; updateMarkers()" class="pr-3 text-gray-500 hover:text-white">✕</button>
                    </div>
                    <!-- Results Dropdown -->
                    <ul v-if="endResults.length > 0" class="absolute w-full bg-gray-700 mt-1 rounded-lg shadow-xl border border-gray-600 z-50 max-h-48 overflow-y-auto">
                        <li v-for="place in endResults" :key="place.place_id" 
                            @click="selectLocation(place, 'end')"
                            class="px-4 py-2 hover:bg-gray-600 cursor-pointer text-sm border-b border-gray-600/50 last:border-0">
                            {{ place.display_name }}
                        </li>
                    </ul>
                </div>

                <!-- Error Message -->
                <div v-if="error" class="bg-red-900/50 border border-red-800 text-red-200 p-3 rounded-lg text-sm">
                    {{ error }}
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="flex justify-center py-8">
                    <LoadingSpinner />
                </div>

                <!-- Routes List -->
                <div v-if="routes.length > 0 && !loading" class="space-y-4 mt-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">{{ $t('hiking.routesTitle') }}</h3>
                        <span class="text-xs bg-gray-700 px-2 py-1 rounded text-gray-300">{{ $t('hiking.routesFound', { count: routes.length }) }}</span>
                    </div>

                    <div v-if="routes.length === 1" class="bg-blue-900/20 border border-blue-800 p-3 rounded-lg text-xs text-blue-200 mb-2">
                        <span class="font-bold">{{ $t('hiking.noteTitle') }}</span> {{ $t('hiking.noteText') }}
                    </div>
                    
                    <div v-for="(route, index) in routes" :key="index"
                         @click="selectRoute(index)"
                         class="bg-gray-700 rounded-xl p-4 cursor-pointer transition-all border-2 relative overflow-hidden group"
                         :class="selectedRouteIndex === index ? 'border-blue-500 bg-gray-750 shadow-lg' : 'border-transparent hover:bg-gray-650'">
                        
                        <!-- Difficulty Badge -->
                        <div class="absolute top-3 right-3 px-2 py-0.5 rounded text-xs font-bold"
                             :class="{
                                'bg-green-900 text-green-300': route.properties.difficulty === 'Fácil',
                                'bg-yellow-900 text-yellow-300': route.properties.difficulty === 'Moderada',
                                'bg-red-900 text-red-300': route.properties.difficulty === 'Difícil',
                             }">
                            {{ getTranslatedDifficulty(route.properties.difficulty) }}
                        </div>

                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-2xl font-bold text-white">{{ route.properties.distance_km }}</span>
                            <span class="text-sm text-gray-400">km</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-300">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                <span>+{{ route.properties.elevation_gain_m }}m</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                <span>-{{ route.properties.elevation_loss_m }}m</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>~{{ route.properties.osrm_time_min }} min</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else-if="!loading && !startLocation && !endLocation" class="text-center py-10 text-gray-500 text-sm">
                    {{ $t('hiking.selectPrompt') }}
                </div>

            </div>
            
            <!-- Footer -->
            <div class="p-4 bg-gray-900 border-t border-gray-700 text-xs text-center text-gray-500">
                {{ $t('hiking.footer') }}
            </div>
        </div>

        <!-- Map Container -->
        <div class="flex-1 relative z-10 w-full h-full transition-all duration-300"
             :class="sidebarOpen ? 'md:ml-96' : 'ml-0'">
            <div ref="mapContainer" class="w-full h-full"></div>

            <!-- Toggle Button (Visible when sidebar is closed) -->
            <button @click="sidebarOpen = true" 
                    class="absolute bottom-6 right-6 z-[2000] bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 transform"
                    :class="sidebarOpen ? 'translate-y-24 opacity-0 pointer-events-none' : 'translate-y-0 opacity-100'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            </button>
        </div>

    </div>
</template>

<style scoped>
/* Scrollbar Styling */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #1f2937; 
}
::-webkit-scrollbar-thumb {
  background: #374151; 
  border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
  background: #4b5563; 
}
</style>