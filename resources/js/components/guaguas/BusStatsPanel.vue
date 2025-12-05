<template>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
    <!-- Filter dropdown -->
    <div class="bg-gray-800 p-3 sm:p-4 rounded-lg col-span-2 md:col-span-3 lg:col-span-1">
      <h3 class="text-xs sm:text-sm font-semibold mb-2">{{ $t('guaguas.filterByLine') }}</h3>
      <select 
        :value="selectedLine" 
        @change="onLineChange"
        class="w-full bg-gray-700 text-white px-2 sm:px-3 py-1.5 sm:py-2 rounded text-sm"
      >
        <option value="">{{ $t('guaguas.allLines') }}</option>
        <option v-for="line in availableLines" :key="line" :value="line">
          {{ $t('guaguas.line') }} {{ line }}
        </option>
      </select>
    </div>
    
    <!-- Stats cards -->
    <StatsCard 
      :title="$t('guaguas.activeBuses')" 
      :value="stats.active" 
      color-class="text-green-400" 
    />
    <StatsCard 
      :title="$t('guaguas.municipales')" 
      :value="stats.municipales" 
      color-class="text-yellow-400" 
    />
    <StatsCard 
      :title="$t('guaguas.global')" 
      :value="stats.global" 
      color-class="text-blue-400" 
    />
    <StatsCard 
      :title="$t('guaguas.nightLines')" 
      :value="stats.night" 
      color-class="text-purple-400" 
    />
    <StatsCard 
      :title="$t('guaguas.delayed')" 
      :value="stats.delayed" 
      color-class="text-red-400" 
    />
  </div>
</template>

<script setup>
/**
 * @component BusStatsPanel
 * @description Stats panel showing bus counts by category with line filter
 * 
 * @example
 * <BusStatsPanel
 *   v-model:selected-line="selectedLine"
 *   :stats="busStats"
 *   :available-lines="busLines"
 * />
 */
import StatsCard from '@/components/StatsCard.vue';

defineProps({
  /**
   * Currently selected line filter
   */
  selectedLine: {
    type: String,
    default: ''
  },
  /**
   * Statistics object with bus counts
   */
  stats: {
    type: Object,
    required: true,
    validator: (value) => {
      return ['active', 'municipales', 'global', 'night', 'delayed'].every(
        key => typeof value[key] === 'number'
      );
    }
  },
  /**
   * List of available bus lines for the filter dropdown
   */
  availableLines: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['update:selectedLine']);

/**
 * Handle line selection change
 */
const onLineChange = (event) => {
  emit('update:selectedLine', event.target.value);
};
</script>

<style scoped>
/* Component-specific styles if needed */
</style>
