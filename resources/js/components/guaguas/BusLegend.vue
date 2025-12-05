<template>
  <div class="bg-gray-800 p-4 rounded-lg">
    <h3 class="font-semibold mb-3">{{ $t('guaguas.legend') }}</h3>
    
    <!-- Colores de guaguas -->
    <div class="flex flex-wrap gap-4 text-sm mb-4">
      <div class="flex items-center gap-2">
        <div class="size-6 rounded bg-yellow-500"></div>
        <span>{{ $t('guaguas.municipales', 'Guaguas Municipales') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="size-6 rounded bg-blue-600"></div>
        <span>{{ $t('guaguas.global', 'Global') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="size-6 rounded bg-purple-500"></div>
        <span>{{ $t('guaguas.nightLines') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="size-5 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold">→</div>
        <span>{{ $t('guaguas.outbound', 'Ida') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="size-5 rounded-full bg-orange-500 flex items-center justify-center text-white text-xs font-bold">←</div>
        <span>{{ $t('guaguas.inbound', 'Vuelta') }}</span>
      </div>
      <div class="flex items-center gap-2">
        <svg class="size-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span>{{ $t('guaguas.withDelay') }}</span>
      </div>
    </div>

    <!-- Separador -->
    <div class="border-t border-gray-700 my-3"></div>

    <!-- Instrucciones de interacción -->
    <h4 class="font-semibold mb-2 text-sm text-gray-300">🎮 {{ $t('guaguas.howToUsePanel', 'Cómo usar el panel de líneas') }}</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
      <!-- Click instruction -->
      <div class="bg-gray-700/50 p-2 rounded">
        <div class="flex items-center gap-2 mb-1">
          <span class="bg-indigo-500 text-white px-2 py-0.5 rounded text-xs font-bold">CLICK</span>
          <span class="text-gray-300">{{ $t('guaguas.onALine', 'en una línea') }}</span>
        </div>
        <p class="text-gray-400">
          {{ $t('guaguas.clickInstruction', 'Añade/quita la línea a tu selección.') }}
          <span class="text-white font-bold">{{ $t('guaguas.selectedInBold', 'negrita') }}</span> 
          {{ $t('guaguas.with', 'con') }} ✓
        </p>
      </div>
      
      <!-- Double click instruction -->
      <div class="bg-gray-700/50 p-2 rounded">
        <div class="flex items-center gap-2 mb-1">
          <span class="bg-purple-500 text-white px-2 py-0.5 rounded text-xs font-bold">DOBLE CLICK</span>
          <span class="text-gray-300">{{ $t('guaguas.onALine', 'en una línea') }}</span>
        </div>
        <p class="text-gray-400">
          {{ $t('guaguas.exclusiveMode', 'Modo exclusivo: muestra') }} 
          <strong class="text-white">{{ $t('guaguas.onlyThatLine', 'solo esa línea') }}</strong>. 
          {{ $t('guaguas.doubleClickToExit', 'Doble click de nuevo para volver al estado anterior.') }}
        </p>
      </div>
      
      <!-- Selected line example -->
      <div class="bg-gray-700/50 p-2 rounded">
        <div class="flex items-center gap-2 mb-1">
          <span class="font-bold text-white">Línea 1</span>
          <span class="text-green-400">✓</span>
        </div>
        <p class="text-gray-400">
          {{ $t('guaguas.boldLinesVisible', 'Las líneas en') }} 
          <strong class="text-white">{{ $t('guaguas.bold', 'negrita') }}</strong> 
          {{ $t('guaguas.areSelected', 'están seleccionadas y sus guaguas son visibles en el mapa.') }}
        </p>
      </div>
      
      <!-- Hidden line example -->
      <div class="bg-gray-700/50 p-2 rounded">
        <div class="flex items-center gap-2 mb-1">
          <span class="text-gray-400 opacity-60">Línea 50</span>
        </div>
        <p class="text-gray-400">
          {{ $t('guaguas.dimmedLines', 'Las líneas') }} 
          <strong class="text-gray-400">{{ $t('guaguas.dimmed', 'atenuadas') }}</strong> 
          {{ $t('guaguas.areHidden', 'están ocultas. Click para mostrarlas.') }}
        </p>
      </div>
    </div>

    <!-- Estado actual -->
    <div class="mt-3 p-2 bg-gray-700/30 rounded text-xs">
      <span class="text-gray-400">{{ $t('guaguas.currentState', 'Estado actual:') }} </span>
      <span v-if="exclusiveMode" class="text-purple-400 font-semibold">
        🔒 {{ $t('guaguas.exclusiveModeActive', 'Modo exclusivo') }} - Línea {{ exclusiveLine?.line }} 
        ({{ $t('guaguas.doubleClickExit', 'doble click para salir') }})
      </span>
      <span v-else-if="selectedCount > 0" class="text-green-400">
        {{ selectedCount }} {{ $t('guaguas.linesSelected', 'línea(s) seleccionada(s)') }}
      </span>
      <span v-else class="text-gray-500">
        {{ $t('guaguas.noLinesSelected', 'Ninguna línea seleccionada - mostrando líneas principales') }}
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
