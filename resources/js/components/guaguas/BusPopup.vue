<template>
  <div class="bus-popup min-w-[200px]">
    <h3 class="font-bold text-lg mb-2 text-gray-900">
      {{ $t('guaguas.line') }} {{ bus.line }}
    </h3>
    
    <div class="space-y-1 text-sm text-gray-700">
      <!-- Company -->
      <p>
        <strong>{{ $t('guaguas.company') }}:</strong> 
        {{ companyLabel }}
      </p>
      
      <!-- Type -->
      <p>
        <strong>{{ $t('guaguas.type') }}:</strong> 
        {{ typeLabel }}
      </p>
      
      <!-- Direction -->
      <p>
        <strong>{{ $t('guaguas.direction') }}:</strong> 
        <span :class="directionClass">
          {{ directionLabel }}
        </span>
      </p>
      
      <!-- Origin -->
      <p>
        <strong>{{ $t('guaguas.from') }}:</strong> 
        {{ bus.origin }}
      </p>
      
      <!-- Destination -->
      <p>
        <strong>{{ $t('guaguas.to') }}:</strong> 
        {{ bus.destination }}
      </p>
      
      <!-- Next stop -->
      <p>
        <strong>{{ $t('guaguas.nextStop') }}:</strong> 
        {{ bus.nextStop }}
      </p>
      
      <!-- Estimated time -->
      <p>
        <strong>{{ $t('guaguas.estimatedTime') }}:</strong> 
        <span :class="bus.delayed ? 'text-yellow-600' : 'text-green-600'">
          {{ bus.timeToNext }} {{ $t('guaguas.minutes') }}
        </span>
      </p>
      
      <!-- Delay warning -->
      <p v-if="bus.delayed" class="text-yellow-600 font-medium">
        ⚠️ {{ $t('guaguas.delay') }}: {{ bus.delayMinutes }} {{ $t('guaguas.minutes') }}
      </p>
      
      <!-- Last update -->
      <p class="text-gray-500 text-xs mt-2">
        {{ $t('guaguas.lastUpdate') }}: {{ bus.lastUpdate }}
      </p>
    </div>
  </div>
</template>

<script setup>
/**
 * @component BusPopup
 * @description Popup content for bus markers showing detailed bus information
 * 
 * @example
 * <l-popup>
 *   <BusPopup :bus="selectedBus" />
 * </l-popup>
 */
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
  /**
   * Bus object with all details
   */
  bus: {
    type: Object,
    required: true,
    validator: (value) => {
      return value && value.line && value.company;
    }
  }
});

/**
 * Company display label
 */
const companyLabel = computed(() => {
  const labels = {
    municipales: t('guaguas.municipalesFull'),
    guaguas_global: t('guaguas.global'),
    global: t('guaguas.global') // Keep for backward compatibility
  };
  return labels[props.bus.company] || props.bus.company || 'Unknown';
  return labels[props.bus.company] || props.bus.company;
});

/**
 * Bus type display label
 */
const typeLabel = computed(() => {
  const labels = {
    municipales: t('guaguas.municipalesFull'),
    guaguas_global: t('guaguas.global'),
    global: t('guaguas.global') // Keep for backward compatibility
  };
  return labels[props.bus.company] || props.bus.company || 'Unknown';
  return labels[props.bus.type] || props.bus.type;
});

/**
 * Direction label based on trip direction
 */
const directionLabel = computed(() => {
  return props.bus.tripDirection === 'outbound' 
    ? t('guaguas.outboundArrow') 
    : t('guaguas.inboundArrow');
});

/**
 * Direction CSS class based on trip direction
 */
const directionClass = computed(() => {
  return props.bus.tripDirection === 'outbound' 
    ? 'text-green-600 font-medium' 
    : 'text-orange-600 font-medium';
});
</script>

<style scoped>
.bus-popup {
  font-family: inherit;
}

.bus-popup strong {
  color: #374151;
}
</style>
