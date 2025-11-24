<template>
  <div 
    class="bg-gray-800 p-3 sm:p-4 rounded-lg text-center border border-gray-700 hover:border-gray-600 transition-all duration-300 hover:shadow-lg"
  >
    <div 
      class="text-2xl sm:text-3xl font-bold mb-1"
      :class="colorClass"
    >
      {{ value }}
    </div>
    <div class="text-[10px] sm:text-xs text-gray-400 uppercase tracking-wider font-medium">
      {{ title }}
    </div>
  </div>
</template>

<script setup>
/**
 * StatsCard Component
 * 
 * Displays a statistic card with a title, value, and optional color.
 * Used for showing metrics in dashboards.
 * 
 * @component
 * @example
 * <StatsCard 
 *   title="Active Users" 
 *   :value="150" 
 *   color-class="text-green-400" 
 * />
 */
defineProps({
  /**
   * The label/title of the stat
   */
  title: {
    type: String,
    required: true,
    validator: (value) => value.trim().length > 0
  },
  /**
   * The numeric or string value to display
   */
  value: {
    type: [String, Number],
    required: true
  },
  /**
   * Tailwind color class for the value
   * @example 'text-green-400', 'text-red-500'
   */
  colorClass: {
    type: String,
    default: 'text-white',
    validator: (value) => value.startsWith('text-')
  }
});
</script>
