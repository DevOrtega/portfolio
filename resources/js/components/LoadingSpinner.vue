<template>
  <div 
    class="flex justify-center items-center"
    :class="containerClass"
  >
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
    small: 'size-6',
    medium: 'size-12',
    large: 'size-16'
  };
  return sizes[props.size];
});

const containerClass = computed(() => {
  return props.size === 'large' ? 'min-h-64' : '';
});
</script>
