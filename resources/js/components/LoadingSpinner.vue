<template>
  <div class="flex justify-center items-center" :style="{ minHeight: size === 'large' ? '16rem' : 'auto' }">
    <div 
      class="animate-spin rounded-full border-t-2 border-b-2 border-indigo-500"
      :class="sizeClass"
      role="status"
      aria-label="Loading"
    ></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

/**
 * LoadingSpinner Component
 * 
 * Displays an animated loading spinner with configurable size.
 * 
 * @component
 * @example
 * <LoadingSpinner size="medium" />
 */
const props = defineProps({
  /**
   * Size of the spinner
   * @values 'small', 'medium', 'large'
   */
  size: {
    type: String,
    default: 'medium',
    validator: (value) => ['small', 'medium', 'large'].includes(value)
  }
});

const sizeClass = computed(() => {
  const sizes = {
    small: 'h-6 w-6',
    medium: 'h-12 w-12',
    large: 'h-16 w-16'
  };
  return sizes[props.size];
});
</script>
