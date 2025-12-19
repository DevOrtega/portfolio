<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import L from 'leaflet';
import axios from 'axios';
import 'leaflet/dist/leaflet.css';
import 'leaflet-polylinedecorator';
import LoadingSpinner from '@/components/LoadingSpinner.vue';
import HikingLegend from '@/components/hiking/HikingLegend.vue';
import RouteInstructions from '@/components/hiking/RouteInstructions.vue';

// State
const { t } = useI18n();
const mapContainer = ref(null);
const map = ref(null);
const loading = ref(false);
const error = ref(null);
const sidebarOpen = ref(true);

const startQuery = ref('');
const endQuery = ref('');
const startResults = ref([]);
const endResults = ref([]);
const startLocation = ref(null); // { lat, lng, display_name }
const endLocation = ref(null);

const waypoints = ref([]); // Array of { id: number, query: string, location: null|object, results: [] }
const nextWaypointId = ref(1);
let searchTimeout = null;
let poiAbortController = null;

const routes = ref([]); // Array of GeoJSON features
const selectedRouteIndex = ref(0);
const pois = ref([]);
const showPois = ref(true);
const poisLoading = ref(false);

// Computed
const googleMapsUrl = computed(() => {
    if (!startLocation.value || !endLocation.value) return '#';
    
    const origin = `${startLocation.value.lat},${startLocation.value.lng}`;
    const destination = `${endLocation.value.lat},${endLocation.value.lng}`;
    
    const validWps = waypoints.value
        .filter(wp => wp.location)
        .map(wp => `${wp.location.lat},${wp.location.lng}`);
    
    const waypointsParam = validWps.length > 0 ? `&waypoints=${validWps.join('|')}` : '';
    
    return `https://www.google.com/maps/dir/?api=1&origin=${origin}&destination=${destination}${waypointsParam}&travelmode=walking`;
});

// Markers and Layers
const markersLayer = ref(null);
const routesLayer = ref(null);
const decoratorsLayer = ref(null);
const poisLayer = ref(null);

// Icons
const createIcon = (color, label = '') => {
    return L.divIcon({
        className: 'custom-div-icon',
        html: `<div style="background-color: ${color}; width: 24px; height: 24px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 8px rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 10px;">${label}</div>`,
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
};

const createPoiIcon = (category) => {
    let color = '#6b7280';
    let label = '•';
    
    switch(category) {
        case 'food': color = '#ef4444'; label = 'R'; break;
        case 'water': color = '#06b6d4'; label = 'W'; break;
        case 'viewpoint': color = '#8b5cf6'; label = 'V'; break;
        case 'picnic': color = '#fb7185'; label = 'X'; break;
        case 'camping': color = '#16a34a'; label = 'C'; break;
        case 'culture': color = '#78350f'; label = 'M'; break;
        case 'health': color = '#db2777'; label = 'H'; break;
        case 'shelter': color = '#d97706'; label = 'S'; break;
        case 'accommodation': color = '#4f46e5'; label = 'A'; break;
        case 'parking': color = '#374151'; label = 'P'; break;
        case 'peak': color = '#10b981'; label = '^'; break;
    }
    
    return L.divIcon({
        className: 'poi-div-icon',
        html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 1.5px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 9px;">${label}</div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
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
    decoratorsLayer.value = L.layerGroup().addTo(map.value);
    poisLayer.value = L.layerGroup().addTo(map.value);

    // Allow clicking on map to set points if inputs are focused
    map.value.on('click', onMapClick);
    
    // Initialize with one empty waypoint if desired, or none. Let's start with 0.
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

watch(showPois, () => {
    renderPois();
});

// --- Map Interaction ---

// Expose function for popup buttons
window.addPoiToRoute = (poiId) => {
    const poi = pois.value.find(p => p.id === poiId);
    if (!poi) return;
    
    addLocationToRoute(poi);
};

onUnmounted(() => {
    if (map.value) map.value.remove();
    delete window.addPoiToRoute;
});

const addLocationToRoute = (location) => {
    // Determine best index to insert
    // We have Start -> WP[0] -> WP[1] ... -> End
    // We want to minimize distance increase.
    
    // Points list including start/end
    const points = [];
    if (startLocation.value) points.push({ coords: startLocation.value, type: 'start' });
    waypoints.value.forEach((wp, i) => {
        if (wp.location) points.push({ coords: wp.location, type: 'wp', index: i });
    });
    if (endLocation.value) points.push({ coords: endLocation.value, type: 'end' });
    
    if (points.length < 2) {
        // Just add as waypoint
        addWaypointWithLocation(location);
        return;
    }
    
    let bestIndex = 0; // Insert before waypoints[0]
    let minAddedDist = Infinity;
    
    // Iterate segments
    for (let i = 0; i < points.length - 1; i++) {
        const p1 = points[i].coords;
        const p2 = points[i+1].coords;
        
        const d12 = getDist(p1, p2);
        const d1P = getDist(p1, location);
        const dP2 = getDist(location, p2);
        
        const added = d1P + dP2 - d12;
        
        if (added < minAddedDist) {
            minAddedDist = added;
            // logic to map segment index to waypoint index
            // If i=0 (Start->WP0), insert at index 0.
            // If i=1 (WP0->WP1), insert at index 1.
            // If last segment (WPn->End), insert at index n (push).
            // Basically index represents the waypoint index to splice at.
            bestIndex = i; 
        }
    }
    
    // Insert
    if (waypoints.value.length >= 10) return; // Limit
    
    waypoints.value.splice(bestIndex, 0, {
        id: nextWaypointId.value++,
        query: location.name || location.display_name,
        location: {
            lat: location.lat,
            lng: location.lon || location.lng,
            display_name: location.name || location.display_name
        },
        results: []
    });
    
    // Hide markers temporarily during recalculation to prevent floating artifacts
    markersLayer.value.clearLayers();
    calculateRoutes();
};

const addWaypointWithLocation = (loc) => {
    waypoints.value.push({
        id: nextWaypointId.value++,
        query: loc.name,
        location: { lat: loc.lat, lng: loc.lon, display_name: loc.name },
        results: []
    });
    // Hide markers temporarily
    markersLayer.value.clearLayers();
    calculateRoutes();
};

const getDist = (p1, p2) => {
    const R = 6371; 
    const dLat = (p2.lat - p1.lat) * Math.PI / 180;
    const dLon = ((p2.lng || p2.lon) - (p1.lng || p1.lon)) * Math.PI / 180;
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(p1.lat * Math.PI / 180) * Math.cos(p2.lat * Math.PI / 180) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c; 
};

const onMapClick = async (e) => {
    // If user clicks map, we can fill empty inputs intelligently
    // For simplicity in this demo, let's keep it form-driven or simple toggle
    // Maybe later.
};

// --- Geocoding ---

const searchPlace = async (query, type, index = null) => {
    if (!query || query.length < 3) return;
    
    try {
        // Limited to Gran Canaria bounding box approx
        const viewbox = '-15.85,27.70,-15.35,28.20';
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&viewbox=${viewbox}&bounded=1&limit=5`;
        
        const res = await axios.get(url);
        if (type === 'start') startResults.value = res.data;
        else if (type === 'end') endResults.value = res.data;
        else if (type === 'waypoint' && index !== null && waypoints.value[index]) {
            waypoints.value[index].results = res.data;
        }
    } catch (e) {
        console.error("Geocoding error", e);
    }
};

const handleInput = (query, type, index = null) => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => searchPlace(query, type, index), 500);
};

const addWaypoint = () => {
    if (waypoints.value.length >= 10) return;
    waypoints.value.push({
        id: nextWaypointId.value++,
        query: '',
        location: null,
        results: []
    });
};

const removeWaypoint = (index) => {
    waypoints.value.splice(index, 1);
    // Hide markers temporarily to avoid floating artifacts while recalculating
    markersLayer.value.clearLayers();
    if (startLocation.value && endLocation.value) {
        calculateRoutes();
    } else {
        updateMarkers(); // If not calculating, just update
    }
};

const selectLocation = (place, type, index = null) => {
    const loc = {
        lat: parseFloat(place.lat),
        lng: parseFloat(place.lon),
        display_name: place.display_name.split(',')[0] // Short name
    };

    if (type === 'start') {
        startLocation.value = loc;
        startQuery.value = loc.display_name;
        startResults.value = [];
    } else if (type === 'end') {
        endLocation.value = loc;
        endQuery.value = loc.display_name;
        endResults.value = [];
    } else if (type === 'waypoint' && index !== null) {
        waypoints.value[index].location = loc;
        waypoints.value[index].query = loc.display_name;
        waypoints.value[index].results = [];
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
        L.marker([startLocation.value.lat, startLocation.value.lng], { icon: createIcon('#10b981', 'S') })
         .bindPopup(`<b>${t('hiking.popup.start')}</b><br/>${startLocation.value.display_name}`)
         .addTo(markersLayer.value);
        bounds.extend([startLocation.value.lat, startLocation.value.lng]);
        hasPoints = true;
    }
    
    waypoints.value.forEach((wp, i) => {
        if (wp.location) {
             L.marker([wp.location.lat, wp.location.lng], { icon: createIcon('#f59e0b', i + 1) })
             .bindPopup(`<b>${t('hiking.popup.intermediate')} ${i+1}</b><br/>${wp.location.display_name}`)
             .addTo(markersLayer.value);
            bounds.extend([wp.location.lat, wp.location.lng]);
            hasPoints = true;
        }
    });
    
    if (endLocation.value) {
        L.marker([endLocation.value.lat, endLocation.value.lng], { icon: createIcon('#ef4444', 'D') })
         .bindPopup(`<b>${t('hiking.popup.destination')}</b><br/>${endLocation.value.display_name}`)
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
    pois.value = []; // Clear POIs
    selectedRouteIndex.value = 0;
    routesLayer.value.clearLayers();
    poisLayer.value.clearLayers();

    try {
        const params = {
            start: `${startLocation.value.lat},${startLocation.value.lng}`,
            end: `${endLocation.value.lat},${endLocation.value.lng}`
        };

        // Collect valid waypoints
        const validWaypoints = waypoints.value
            .filter(wp => wp.location)
            .map(wp => `${wp.location.lat},${wp.location.lng}`);

        if (validWaypoints.length > 0) {
            params.waypoints = validWaypoints;
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
        updateMarkers(); // Ensure markers are on top and visible

        // Fetch POIs for the first route found
        if (routes.value.length > 0) {
            fetchPois(routes.value[0].geometry.coordinates);
        }

    } catch (e) {
        console.error(e);
        error.value = t('common.error');
    } finally {
        loading.value = false;
    }
};

const fetchPois = async (coordinates) => {
    // Cancel previous request
    if (poiAbortController) {
        poiAbortController.abort();
    }
    poiAbortController = new AbortController();

    poisLoading.value = true;

    try {
        const response = await axios.post('/api/hiking/pois', {
            route: coordinates,
            radius: 500
        }, {
            signal: poiAbortController.signal
        });
        pois.value = response.data;
        renderPois();
    } catch (e) {
        if (axios.isCancel(e)) {
            console.log('POI fetch aborted');
        } else {
            console.error("Error fetching POIs", e);
        }
    } finally {
        // Only turn off loading if this wasn't aborted (or if we track IDs)
        // If aborted, the new request will set it to true anyway.
        // But if we finish here, we should set false.
        // Wait, if aborted, `finally` runs.
        if (!poiAbortController.signal.aborted) {
            poisLoading.value = false;
        }
    }
};

const renderPois = () => {
    poisLayer.value.clearLayers();
    if (!showPois.value) return;

    pois.value.forEach(poi => {
        const categoryLabel = t('hiking.legend.' + poi.category);
        const hasProperName = poi.name.toLowerCase() !== poi.category.toLowerCase() && 
                             poi.name.toLowerCase() !== categoryLabel.toLowerCase();

        L.marker([poi.lat, poi.lon], { icon: createPoiIcon(poi.category) })
         .bindPopup(`
            <div class="text-center min-w-[120px]">
                <b class="block text-sm mb-0.5">${poi.name}</b>
                ${hasProperName ? `<span class="text-[10px] uppercase text-gray-500 block mb-2">${categoryLabel}</span>` : '<div class="mb-2"></div>'}
                <button onclick="window.addPoiToRoute(${poi.id})" 
                        class="w-full bg-blue-600 text-white text-[10px] py-1 px-2 rounded hover:bg-blue-700 transition-colors font-bold uppercase tracking-tight">
                    ${t('hiking.addToRoute')}
                </button>
            </div>
         `)
         .addTo(poisLayer.value);
    });
};

const renderRoutes = () => {
    routesLayer.value.clearLayers();
    decoratorsLayer.value.clearLayers();

    // Render all routes (non-selected as gray)
    routes.value.forEach((feature, index) => {
        const isSelected = index === selectedRouteIndex.value;
        const color = isSelected ? getDifficultyColor(feature.properties.difficulty) : '#4b5563';
        const opacity = isSelected ? 1 : 0.4;
        const weight = isSelected ? 5 : 3;
        
        const layer = L.geoJSON(feature, {
            style: { color, weight, opacity }
        }).addTo(routesLayer.value);

        if (isSelected) {
            map.value.fitBounds(layer.getBounds(), { padding: [50, 50] });
            
            // Add directional arrows
            // Coordinates in GeoJSON are [lon, lat, ele], Leaflet needs [lat, lon]
            const coords = feature.geometry.coordinates.map(c => [c[1], c[0]]);
            
            const decorator = L.polylineDecorator(coords, {
                patterns: [
                    {
                        offset: 25,
                        repeat: 80, 
                        symbol: L.Symbol.arrowHead({
                            pixelSize: 12,
                            polygon: false,
                            pathOptions: { 
                                stroke: true, 
                                color: '#ffffff',
                                opacity: 1,
                                weight: 2
                            }
                        })
                    }
                ]
            }).addTo(decoratorsLayer.value);
        }
    });
};

const selectRoute = (index) => {
    selectedRouteIndex.value = index;
    renderRoutes();
    // Re-fetch POIs for the new selected route geometry
    fetchPois(routes.value[index].geometry.coordinates);
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
    <div class="fixed inset-0 top-16 bg-gray-900 text-white overflow-hidden flex z-40">
        
        <!-- Sidebar -->
        <div class="absolute inset-y-0 left-0 h-full w-full sm:w-96 bg-gray-800 border-r border-gray-700 flex flex-col shadow-2xl z-30 transition-transform duration-300 transform"
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
                            @input="handleInput(startQuery, 'start')"
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

                <!-- Dynamic Waypoints -->
                <div v-for="(wp, index) in waypoints" :key="wp.id" class="relative">
                    <label class="text-xs text-gray-400 uppercase font-bold tracking-wider mb-1 block flex justify-between">
                        <span>{{ $t('hiking.intermediate') }} {{ index + 1 }}</span>
                        <button @click="removeWaypoint(index)" class="text-red-400 hover:text-red-300 text-xs lowercase hover:underline">{{ $t('common.delete') }}</button>
                    </label>
                    <div class="flex items-center bg-gray-700 rounded-lg border border-gray-600 focus-within:border-amber-500 focus-within:ring-1 focus-within:ring-amber-500">
                        <span class="pl-3 text-amber-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </span>
                        <input 
                            v-model="wp.query"
                            @input="handleInput(wp.query, 'waypoint', index)"
                            type="text" 
                            class="w-full bg-transparent border-none focus:ring-0 text-white placeholder-gray-500 py-3 px-2"
                            :placeholder="$t('hiking.intermediatePlaceholder')"
                        />
                        <button v-if="wp.query" @click="wp.query=''; wp.location=null; updateMarkers(); if(startLocation && endLocation) calculateRoutes()" class="pr-3 text-gray-500 hover:text-white">✕</button>
                    </div>
                    <!-- Results Dropdown -->
                    <ul v-if="wp.results.length > 0" class="absolute w-full bg-gray-700 mt-1 rounded-lg shadow-xl border border-gray-600 z-50 max-h-48 overflow-y-auto">
                        <li v-for="place in wp.results" :key="place.place_id" 
                            @click="selectLocation(place, 'waypoint', index)"
                            class="px-4 py-2 hover:bg-gray-600 cursor-pointer text-sm border-b border-gray-600/50 last:border-0">
                            {{ place.display_name }}
                        </li>
                    </ul>
                </div>

                <!-- Add Waypoint Button -->
                <div v-if="waypoints.length < 10" class="flex justify-center">
                    <button @click="addWaypoint" class="text-xs flex items-center gap-1 text-gray-400 hover:text-white transition-colors border border-dashed border-gray-600 rounded px-3 py-1 hover:border-gray-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ $t('common.add') }} {{ $t('hiking.intermediate') }}
                    </button>
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
                            @input="handleInput(endQuery, 'end')"
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

                <!-- POIs Toggle -->
                <div v-if="routes.length > 0 && !loading" class="flex items-center gap-2 mb-2 px-1 justify-between">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="showPois" v-model="showPois" class="rounded bg-gray-700 border-gray-600 text-blue-500 focus:ring-blue-500">
                        <label for="showPois" class="text-sm text-gray-300 select-none cursor-pointer">
                            {{ $t('hiking.showPois', 'Show Points of Interest (0.5km)') }}
                        </label>
                    </div>
                    <div v-if="poisLoading" class="flex items-center gap-1 text-xs text-blue-400">
                        <svg class="animate-spin h-3 w-3 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ $t('hiking.loadingPois') }}</span>
                    </div>
                </div>

                <!-- Routes List -->
                <div v-if="routes.length > 0 && !loading" class="space-y-4 mt-2">
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

                <!-- Route Instructions (Selected Route) -->
                <RouteInstructions 
                    v-if="routes.length > 0 && !loading"
                    :route="routes[selectedRouteIndex]" 
                    :google-maps-url="googleMapsUrl" 
                />

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
        <div class="flex-1 relative z-10 w-full h-full transition-all duration-300 flex flex-col"
             :class="sidebarOpen ? 'md:ml-96' : 'ml-0'">
            <div ref="mapContainer" class="w-full flex-1"></div>

            <!-- Legend -->
            <HikingLegend />

            <!-- Toggle Button (Visible when sidebar is closed) -->
            <button @click="sidebarOpen = true" 
                    class="absolute bottom-16 right-6 z-[2000] bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-lg flex items-center justify-center transition-all duration-300 transform"
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