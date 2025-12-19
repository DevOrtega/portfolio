<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    route: {
        type: Object,
        required: true
    },
    googleMapsUrl: {
        type: String,
        default: '#'
    }
});

const { t } = useI18n();

const formatDistance = (meters) => {
    if (meters < 1000) return `${Math.round(meters)} m`;
    return `${(meters / 1000).toFixed(1)} km`;
};

const getInstructionText = (step) => {
    const m = step.maneuver;
    const name = step.name || t('hiking.unnamed_road', 'Path'); 
    
    let textKey = '';
    
    if (m.type === 'turn') {
        const mod = m.modifier ? m.modifier.replace(' ', '_') : 'straight';
        textKey = `hiking.directions.turn.${mod}`;
    } else if (m.type === 'new name') {
        textKey = 'hiking.directions.new_name';
    } else if (m.type === 'depart') {
        textKey = 'hiking.directions.depart';
    } else if (m.type === 'arrive') {
        textKey = 'hiking.directions.arrive';
    } else if (m.type === 'roundabout') {
        textKey = 'hiking.directions.roundabout';
    } else {
        return m.type; // Fallback
    }

    let text = t(textKey);

    if (m.type === 'roundabout') {
        text += ' ' + t('hiking.directions.roundabout_exit', { exit: m.exit || 1 });
    }

    if (name && name !== 'Path' && name !== 'Camino') {
        text += ' ' + t('hiking.directions.on_road', { name: name });
    }

    return text;
};
</script>

<template>
    <div class="mt-4 bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-lg">
        <div class="p-4 border-b border-gray-700 bg-gray-750 flex justify-between items-center">
            <h3 class="font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.806-.98l-6-3.344M15 7v13M9 7l0 0m6 0l0 0"/></svg>
                {{ $t('hiking.instructions', 'Indicaciones') }}
            </h3>
            <a :href="googleMapsUrl" target="_blank" class="text-[10px] bg-white/10 hover:bg-white/20 text-white px-2 py-1 rounded flex items-center gap-1 transition-colors border border-white/20">
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                {{ $t('hiking.googleMaps') }}
            </a>
        </div>
        <div class="max-h-64 overflow-y-auto p-0">
            <div v-for="(leg, legIndex) in route.properties.legs" :key="legIndex" class="border-b border-gray-700/50 last:border-0">
                <!-- Leg Header if multiple legs -->
                <div v-if="route.properties.legs.length > 1" class="bg-gray-700/30 px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    {{ $t('hiking.leg', 'Tramo') }} {{ legIndex + 1 }}
                </div>
                
                <!-- Steps -->
                <ul class="divide-y divide-gray-700/30">
                    <li v-for="(step, stepIndex) in leg.steps" :key="stepIndex" class="px-4 py-3 flex gap-3 hover:bg-gray-700/30 transition-colors text-sm">
                        <div class="mt-0.5 min-w-[20px]">
                            <!-- Simple Icons based on maneuver -->
                            <svg v-if="step.maneuver.type === 'arrive'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg v-else-if="step.maneuver.modifier && step.maneuver.modifier.includes('left')" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <svg v-else-if="step.maneuver.modifier && step.maneuver.modifier.includes('right')" class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-200">{{ getInstructionText(step) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ formatDistance(step.distance) }}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
