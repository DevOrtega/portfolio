<template>
  <div class="relative">
    <span 
      class="absolute -left-[41px] top-1 h-5 w-5 rounded-full border-4 border-gray-900"
      :class="dotColorClass"
    ></span>
    <div 
      class="bg-gray-800 rounded-xl p-6 border border-gray-700 transition-colors"
      :class="hoverColorClass"
    >
      <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-2 mb-2">
        <h3 class="text-lg sm:text-xl font-bold text-white">{{ title }}</h3>
        <span 
          class="text-xs sm:text-sm font-medium px-2 sm:px-3 py-1 rounded-full self-start md:self-auto whitespace-nowrap"
          :class="badgeColorClass"
        >
          {{ dateRange }}
        </span>
      </div>
      <h4 v-if="subtitle" class="text-lg text-gray-400 mb-4">{{ subtitle }}</h4>
      <p class="text-gray-300 leading-relaxed whitespace-pre-line">{{ description }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  dateRange: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  color: {
    type: String,
    default: 'indigo',
    validator: (value) => ['indigo', 'cyan', 'purple', 'green'].includes(value)
  }
});

const dotColorClass = computed(() => {
  const colors = {
    indigo: 'bg-indigo-500',
    cyan: 'bg-cyan-500',
    purple: 'bg-purple-500',
    green: 'bg-green-500'
  };
  return colors[props.color];
});

const hoverColorClass = computed(() => {
  const colors = {
    indigo: 'hover:border-indigo-500',
    cyan: 'hover:border-cyan-500',
    purple: 'hover:border-purple-500',
    green: 'hover:border-green-500'
  };
  return colors[props.color];
});

const badgeColorClass = computed(() => {
  const colors = {
    indigo: 'text-indigo-300 bg-indigo-500/10',
    cyan: 'text-cyan-300 bg-cyan-500/10',
    purple: 'text-purple-300 bg-purple-500/10',
    green: 'text-green-300 bg-green-500/10'
  };
  return colors[props.color];
});
</script>
