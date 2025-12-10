/**
 * @file useBusSelection.js
 * @description Composable for managing bus line selection state and logic
 * Handles single click selection, double-click exclusive mode, and visibility updates
 * 
 * @example
 * const {
 *   selectedLines,
 *   exclusiveMode,
 *   isLineSelected,
 *   handleLineClick,
 *   updateBusVisibility
 * } = useBusSelection(buses, hiddenBusIds, mainLines);
 */
import { ref, computed, unref } from 'vue';

// Default main lines (fallback if API data not available)
const DEFAULT_MAIN_LINES = {
  municipales: ['1', '12', '17', '25', '26'],
  global: ['1', '5', '30', '60', '91'],
  night: []
};

/**
 * Bus selection composable
 * @param {Ref<Array>} buses - Reactive reference to all buses
 * @param {Ref<Set>} hiddenBusIds - Reactive reference to hidden bus IDs
 * @param {Ref<Object>|Object} mainLinesConfig - Main lines configuration (optional)
 * @returns {Object} Selection state and methods
 */
export function useBusSelection(buses, hiddenBusIds, mainLinesConfig = DEFAULT_MAIN_LINES) {
  // ============ STATE ============
  const selectedLines = ref(new Set());
  const exclusiveMode = ref(false);
  const exclusiveLine = ref(null);
  const previousSelectedLines = ref(new Set());
  const previousHiddenBusIds = ref(new Set());
  
  // Click tracking for double-click detection
  const lastClickTime = ref(0);
  const lastClickedLine = ref(null);
  
  // Constants
  const DOUBLE_CLICK_THRESHOLD = 300; // ms
  const NIGHT_LINES = ['L1', 'L2', 'L3', '64', '65'];

  // ============ COMPUTED ============
  
  /**
   * Get main lines (reactive or static)
   */
  const mainLines = computed(() => unref(mainLinesConfig) || DEFAULT_MAIN_LINES);
  
  /**
   * Number of selected lines
   */
  const selectedCount = computed(() => selectedLines.value.size);
  
  /**
   * Whether any line is selected
   */
  const hasSelection = computed(() => selectedLines.value.size > 0);

  // ============ HELPER FUNCTIONS ============
  
  /**
   * Generate unique key for a line
   * @param {string} line - Line number/name
   * @param {string} company - Company identifier
   * @returns {string} Unique line key
   */
  const getLineKey = (line, company) => `${company}-${line}`;
  
  /**
   * Check if a line is selected
   * @param {string} line - Line number/name
   * @param {string} company - Company identifier
   * @returns {boolean}
   */
  const isLineSelected = (line, company) => {
    const lineKey = getLineKey(line, company);
    return selectedLines.value.has(lineKey);
  };
  
  /**
   * Check if a line is a main (default visible) line
   * @param {string} line - Line number/name
   * @param {string} company - Company identifier
   * @returns {boolean}
   */
  const isMainLine = (line, company) => {
    const lines = mainLines.value;
    if (company === 'municipales') {
      if (NIGHT_LINES.includes(line)) {
        return (lines.night || []).includes(line);
      }
      return (lines.municipales || []).includes(line);
    }
    return (lines.global || []).includes(line);
  };

  // ============ VISIBILITY MANAGEMENT ============
  
  /**
   * Update bus visibility based on current selection
   * If lines are selected, show only those
   * If no selection, show main lines by default
   */
  const updateBusVisibility = () => {
    const newHiddenBusIds = new Set();
    
    buses.value.forEach(bus => {
      const lineKey = getLineKey(bus.line, bus.company);
      const isSelected = selectedLines.value.has(lineKey);
      const isMain = isMainLine(bus.line, bus.company);
      
      if (selectedLines.value.size > 0) {
        // Selection mode: show only selected lines
        if (!isSelected) {
          newHiddenBusIds.add(bus.id);
        }
      } else {
        // Default mode: show main lines only
        if (!isMain) {
          newHiddenBusIds.add(bus.id);
        }
      }
    });
    
    hiddenBusIds.value = newHiddenBusIds;
  };
  
  /**
   * Show only a specific bus, hiding all others
   * @param {string} busId - ID of the bus to show
   */
  const showOnlyBus = (busId) => {
    buses.value.forEach(bus => {
      if (bus.id !== busId) {
        hiddenBusIds.value.add(bus.id);
      } else {
        hiddenBusIds.value.delete(bus.id);
      }
    });
    // Force reactivity
    hiddenBusIds.value = new Set(hiddenBusIds.value);
  };

  // ============ EXCLUSIVE MODE ============
  
  /**
   * Enter exclusive mode for a specific line
   * @param {string} line - Line number/name
   * @param {string} company - Company identifier
   */
  const enterExclusiveMode = (line, company) => {
    const lineKey = getLineKey(line, company);
    
    // Save current state
    previousSelectedLines.value = new Set(selectedLines.value);
    previousHiddenBusIds.value = new Set(hiddenBusIds.value);
    
    // Activate exclusive mode
    exclusiveMode.value = true;
    exclusiveLine.value = { line, company };
    
    // Select ONLY this line
    selectedLines.value = new Set([lineKey]);
    
    // Hide all buses except those of this line
    const newHiddenBusIds = new Set();
    buses.value.forEach(bus => {
      if (bus.line !== line || bus.company !== company) {
        newHiddenBusIds.add(bus.id);
      }
    });
    hiddenBusIds.value = newHiddenBusIds;
  };
  
  /**
   * Exit exclusive mode and restore previous state
   */
  const exitExclusiveMode = () => {
    exclusiveMode.value = false;
    exclusiveLine.value = null;
    
    // Restore previous state
    selectedLines.value = new Set(previousSelectedLines.value);
    hiddenBusIds.value = new Set(previousHiddenBusIds.value);
    
    // If no lines were selected before, show main lines
    if (selectedLines.value.size === 0) {
      updateBusVisibility();
    }
  };

  // ============ SELECTION HANDLERS ============
  
  /**
   * Toggle a line's selection status
   * @param {string} line - Line number/name
   * @param {string} company - Company identifier
   */
  const toggleLineSelection = (line, company) => {
    const lineKey = getLineKey(line, company);
    
    // Exit exclusive mode if active
    if (exclusiveMode.value) {
      exclusiveMode.value = false;
      exclusiveLine.value = null;
      selectedLines.value = new Set(previousSelectedLines.value);
    }
    
    // Toggle the line
    if (selectedLines.value.has(lineKey)) {
      selectedLines.value.delete(lineKey);
    } else {
      selectedLines.value.add(lineKey);
    }
    
    updateBusVisibility();
    
    // Force reactivity
    selectedLines.value = new Set(selectedLines.value);
  };
  
  /**
   * Handle line click (single or double)
   * @param {string} line - Line number/name
   * @param {string} company - Company identifier
   * @returns {boolean} True if it was a double-click
   */
  const handleLineClick = (line, company) => {
    const now = Date.now();
    const lineKey = getLineKey(line, company);
    const isDoubleClick = lastClickedLine.value === lineKey && 
                          (now - lastClickTime.value) < DOUBLE_CLICK_THRESHOLD;
    
    if (isDoubleClick) {
      // Double click - toggle exclusive mode
      if (exclusiveMode.value && 
          exclusiveLine.value?.line === line && 
          exclusiveLine.value?.company === company) {
        exitExclusiveMode();
      } else {
        enterExclusiveMode(line, company);
      }
    } else {
      // Single click - toggle selection
      toggleLineSelection(line, company);
    }
    
    // Update click tracking
    lastClickTime.value = now;
    lastClickedLine.value = lineKey;
    
    return isDoubleClick;
  };
  
  /**
   * Select/deselect all lines of a company
   * @param {string} companyId - Company identifier or 'night' for night lines
   */
  const toggleCompanySelection = (companyId) => {
    const companyBuses = buses.value.filter(bus => {
      if (companyId === 'night') {
        return bus.type === 'night';
      }
      return bus.company === companyId && bus.type !== 'night';
    });
    
    // Get all unique line keys for this company
    const companyLineKeys = new Set();
    companyBuses.forEach(bus => {
      companyLineKeys.add(getLineKey(bus.line, bus.company));
    });
    
    // Check if all are selected
    const allSelected = [...companyLineKeys].every(key => selectedLines.value.has(key));
    
    if (allSelected) {
      // Deselect all
      companyLineKeys.forEach(key => selectedLines.value.delete(key));
    } else {
      // Select all
      companyLineKeys.forEach(key => selectedLines.value.add(key));
    }
    
    // Exit exclusive mode if active
    if (exclusiveMode.value) {
      exclusiveMode.value = false;
      exclusiveLine.value = null;
    }
    
    updateBusVisibility();
    
    // Force reactivity
    selectedLines.value = new Set(selectedLines.value);
  };
  
  /**
   * Clear all selections
   */
  const clearSelection = () => {
    selectedLines.value = new Set();
    exclusiveMode.value = false;
    exclusiveLine.value = null;
    updateBusVisibility();
  };
  
  /**
   * Select specific lines
   * @param {Array<{line: string, company: string}>} lines - Lines to select
   */
  const selectLines = (lines) => {
    lines.forEach(({ line, company }) => {
      selectedLines.value.add(getLineKey(line, company));
    });
    updateBusVisibility();
    selectedLines.value = new Set(selectedLines.value);
  };

  // ============ RETURN PUBLIC API ============
  return {
    // State
    selectedLines,
    exclusiveMode,
    exclusiveLine,
    selectedCount,
    hasSelection,
    
    // Helpers
    getLineKey,
    isLineSelected,
    isMainLine,
    
    // Visibility
    updateBusVisibility,
    showOnlyBus,
    
    // Selection handlers
    handleLineClick,
    toggleLineSelection,
    toggleCompanySelection,
    enterExclusiveMode,
    exitExclusiveMode,
    clearSelection,
    selectLines
  };
}

export default useBusSelection;
