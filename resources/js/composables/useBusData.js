/**
 * @file useBusData.js
 * @description Composable for loading and managing bus data from the API
 */

import { ref, computed, readonly } from 'vue';
import { fetchBusData } from '@/api/busApi';

// Shared state (singleton pattern for caching)
const busData = ref(null);
const isLoading = ref(false);
const error = ref(null);
const isLoaded = ref(false);

/**
 * Composable for managing bus data
 * Data is cached after first load
 */
export function useBusData() {
  /**
   * Load bus data from API (cached)
   */
  const loadBusData = async (force = false) => {
    if (isLoaded.value && !force) {
      return busData.value;
    }

    isLoading.value = true;
    error.value = null;

    try {
      const data = await fetchBusData();
      busData.value = data;
      isLoaded.value = true;
      return data;
    } catch (err) {
      error.value = err.message || 'Error loading bus data';
      console.error('Failed to load bus data:', err);
      throw err;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Clear cached data
   */
  const clearCache = () => {
    busData.value = null;
    isLoaded.value = false;
    error.value = null;
  };

  // Computed properties for easy access
  const companies = computed(() => busData.value?.companies ?? {});
  const busLines = computed(() => busData.value?.bus_lines ?? {});
  const mainLines = computed(() => busData.value?.main_lines ?? {});
  const routes = computed(() => busData.value?.routes ?? []);
  const stops = computed(() => busData.value?.stops ?? {});
  const mapConfig = computed(() => busData.value?.map_config ?? {});
  const simulationConfig = computed(() => busData.value?.simulation_config ?? {});

  /**
   * Get route by company and line number
   */
  const getRoute = (company, lineNumber) => {
    return routes.value.find(
      r => r.company === company && r.line === lineNumber
    );
  };

  /**
   * Get stop coordinates
   */
  const getStopCoords = (stopCode, direction = 'outbound') => {
    const stop = stops.value[stopCode];
    if (!stop) return null;
    return direction === 'outbound' ? stop.outbound : stop.inbound;
  };

  /**
   * Check if a line is a main line
   */
  const isMainLine = (lineNumber, company) => {
    const companyMainLines = mainLines.value[company] || [];
    return companyMainLines.includes(lineNumber);
  };

  return {
    // State
    busData: readonly(busData),
    isLoading: readonly(isLoading),
    error: readonly(error),
    isLoaded: readonly(isLoaded),
    
    // Computed
    companies,
    busLines,
    mainLines,
    routes,
    stops,
    mapConfig,
    simulationConfig,
    
    // Methods
    loadBusData,
    clearCache,
    getRoute,
    getStopCoords,
    isMainLine,
  };
}

export default useBusData;
