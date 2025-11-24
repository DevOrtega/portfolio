<template>
  <div 
    class="bg-gray-800 p-3 sm:p-4 rounded-lg text-center border border-gray-700 hover:border-gray-600 transition-all duration-300 hover:shadow-lg"
  >
    <div 
      class="text-2xl sm:text-3xl font-bold mb-1"
      :class="colorClass || 'text-white'"
    >
      {{ value }}
    </div>
    <div class="text-[10px] sm:text-xs text-gray-400 uppercase tracking-wider font-medium">
      {{ title }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  colorClass: {
    type: String,
    default: 'text-white'
  }
});
</script>
