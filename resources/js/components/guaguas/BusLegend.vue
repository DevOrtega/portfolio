<template>
  <div class="bg-gray-800 p-4 rounded-lg">
    <h3 class="font-semibold mb-3">{{ $t('guaguas.legend') }}</h3>
    
    <!-- Bus colors -->
    <div class="flex flex-wrap gap-4 text-sm mb-4">
      <div class="flex items-center gap-2">
        <div class="size-6 rounded bg-yellow-500"></div>
        <span>{{ $t('guaguas.municipalesFull') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="size-6 rounded bg-blue-600"></div>
        <span>{{ $t('guaguas.global') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="size-6 rounded bg-purple-500"></div>
        <span>{{ $t('guaguas.nightLines') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="size-5 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold">→</div>
        <span>{{ $t('guaguas.outbound') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="size-5 rounded-full bg-orange-500 flex items-center justify-center text-white text-xs font-bold">←</div>
        <span>{{ $t('guaguas.inbound') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <svg class="size-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span>{{ $t('guaguas.withDelay') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="size-3 rounded-full bg-white border-2 border-gray-500 shadow-sm"></div>
        <span>{{ $t('guaguas.stops') }}</span>
      </div>
    </div>

    <!-- Separator -->
    <div class="border-t border-gray-700 my-3"></div>

    <!-- Interaction instructions -->
    <h4 class="font-semibold mb-2 text-sm text-gray-300">{{ $t('guaguas.howToUsePanel') }}</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
      <!-- Click instruction -->
      <div class="bg-gray-700/50 p-2 rounded">
        <div class="flex items-center gap-2 mb-1">
          <span class="bg-indigo-500 text-white px-2 py-0.5 rounded text-xs font-bold">{{ $t('guaguas.click') }}</span>
          <span class="text-gray-300">{{ $t('guaguas.onALine') }}</span>
        </div>
        <p class="text-gray-400">
          {{ $t('guaguas.clickInstruction') }}
          <span class="text-white font-bold">{{ $t('guaguas.selectedInBold') }}</span> 
          {{ $t('guaguas.with') }} ✓
        </p>
      </div>
      
      <!-- Double click instruction -->
      <div class="bg-gray-700/50 p-2 rounded">
        <div class="flex items-center gap-2 mb-1">
          <span class="bg-purple-500 text-white px-2 py-0.5 rounded text-xs font-bold">{{ $t('guaguas.doubleClick') }}</span>
          <span class="text-gray-300">{{ $t('guaguas.onALine') }}</span>
        </div>
        <p class="text-gray-400">
          {{ $t('guaguas.exclusiveMode') }} 
          <strong class="text-white">{{ $t('guaguas.onlyThatLine') }}</strong>. 
          {{ $t('guaguas.doubleClickToExit') }}
        </p>
      </div>
      
      <!-- Selected line example -->
      <div class="bg-gray-700/50 p-2 rounded">
        <div class="flex items-center gap-2 mb-1">
          <span class="font-bold text-white">{{ $t('guaguas.line') }} 1</span>
          <span class="text-green-400">✓</span>
        </div>
        <p class="text-gray-400">
          {{ $t('guaguas.boldLinesVisible') }} 
          <strong class="text-white">{{ $t('guaguas.bold') }}</strong> 
          {{ $t('guaguas.areSelected') }}
        </p>
      </div>
      
      <!-- Hidden line example -->
      <div class="bg-gray-700/50 p-2 rounded">
        <div class="flex items-center gap-2 mb-1">
          <span class="text-gray-400 opacity-60">{{ $t('guaguas.line') }} 50</span>
        </div>
        <p class="text-gray-400">
          {{ $t('guaguas.dimmedLines') }} 
          <strong class="text-gray-400">{{ $t('guaguas.dimmed') }}</strong> 
          {{ $t('guaguas.areHidden') }}
        </p>
      </div>
    </div>

    <!-- Current state -->
    <div class="mt-3 p-2 bg-gray-700/30 rounded text-xs">
      <span class="text-gray-400">{{ $t('guaguas.currentState') }} </span>
      <span v-if="exclusiveMode" class="text-purple-400 font-semibold">
        🔒 {{ $t('guaguas.exclusiveModeActive') }} - {{ $t('guaguas.line') }} {{ exclusiveLine?.line }} 
        ({{ $t('guaguas.doubleClickExit') }})
      </span>
      <span v-else-if="selectedCount > 0" class="text-green-400">
        {{ selectedCount }} {{ $t('guaguas.linesSelected') }}
      </span>
      <span v-else class="text-gray-500">
        {{ $t('guaguas.noLinesSelected') }}
      </span>
    </div>
  </div>
</template>

<script setup>
/**
 * @component BusLegend
 * @description Legend component showing bus colors, directions, and interaction instructions
 * 
 * @example
 * <BusLegend
 *   :exclusive-mode="exclusiveMode"
 *   :exclusive-line="exclusiveLine"
 *   :selected-count="selectedLines.size"
 * />
 */

defineProps({
  /**
   * Whether exclusive mode is active
   */
  exclusiveMode: {
    type: Boolean,
    default: false
  },
  /**
   * Currently selected exclusive line (if in exclusive mode)
   */
  exclusiveLine: {
    type: Object,
    default: null
  },
  /**
   * Number of selected lines
   */
  selectedCount: {
    type: Number,
    default: 0
  }
});
</script>

<style scoped>
/* Component-specific styles if needed */
</style>
