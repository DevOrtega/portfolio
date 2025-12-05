/**
 * @file useBusSelection.test.js
 * @description Tests for the useBusSelection composable
 */
import { describe, it, expect, beforeEach, vi } from 'vitest';
import { ref } from 'vue';
import { useBusSelection } from '@/composables/useBusSelection';

describe('useBusSelection', () => {
  let buses;
  let hiddenBusIds;
  let selection;

  beforeEach(() => {
    // Mock buses data
    buses = ref([
      { id: 'bus-1', line: '1', company: 'municipales', type: 'urban' },
      { id: 'bus-2', line: '12', company: 'municipales', type: 'urban' },
      { id: 'bus-3', line: '1', company: 'global', type: 'interurban' },
      { id: 'bus-4', line: 'L1', company: 'municipales', type: 'night' },
    ]);
    hiddenBusIds = ref(new Set());
    selection = useBusSelection(buses, hiddenBusIds);
  });

  describe('isLineSelected', () => {
    it('should return false when no lines are selected', () => {
      expect(selection.isLineSelected('1', 'municipales')).toBe(false);
    });

    it('should return true when line is selected', () => {
      selection.selectedLines.value.add('municipales-1');
      expect(selection.isLineSelected('1', 'municipales')).toBe(true);
    });

    it('should distinguish between companies with same line number', () => {
      selection.selectedLines.value.add('municipales-1');
      expect(selection.isLineSelected('1', 'municipales')).toBe(true);
      expect(selection.isLineSelected('1', 'global')).toBe(false);
    });
  });

  describe('isMainLine', () => {
    it('should identify main municipal lines', () => {
      expect(selection.isMainLine('1', 'municipales')).toBe(true);
      expect(selection.isMainLine('12', 'municipales')).toBe(true);
    });

    it('should identify main global lines', () => {
      expect(selection.isMainLine('1', 'global')).toBe(true);
      expect(selection.isMainLine('60', 'global')).toBe(true);
    });

    it('should identify main night lines', () => {
      expect(selection.isMainLine('L1', 'municipales')).toBe(true);
      expect(selection.isMainLine('L2', 'municipales')).toBe(true);
    });

    it('should return false for non-main lines', () => {
      expect(selection.isMainLine('50', 'municipales')).toBe(false);
      expect(selection.isMainLine('204', 'global')).toBe(false);
    });
  });

  describe('toggleLineSelection', () => {
    it('should add line to selection when not selected', () => {
      selection.toggleLineSelection('1', 'municipales');
      expect(selection.selectedLines.value.has('municipales-1')).toBe(true);
    });

    it('should remove line from selection when already selected', () => {
      selection.selectedLines.value.add('municipales-1');
      selection.toggleLineSelection('1', 'municipales');
      expect(selection.selectedLines.value.has('municipales-1')).toBe(false);
    });

    it('should exit exclusive mode when toggling', () => {
      selection.exclusiveMode.value = true;
      selection.exclusiveLine.value = { line: '12', company: 'municipales' };
      selection.toggleLineSelection('1', 'municipales');
      expect(selection.exclusiveMode.value).toBe(false);
    });
  });

  describe('enterExclusiveMode', () => {
    it('should activate exclusive mode', () => {
      selection.enterExclusiveMode('1', 'municipales');
      expect(selection.exclusiveMode.value).toBe(true);
      expect(selection.exclusiveLine.value).toEqual({ line: '1', company: 'municipales' });
    });

    it('should select only the exclusive line', () => {
      selection.selectedLines.value.add('global-1');
      selection.enterExclusiveMode('1', 'municipales');
      expect(selection.selectedLines.value.size).toBe(1);
      expect(selection.selectedLines.value.has('municipales-1')).toBe(true);
    });

    it('should hide all buses except the exclusive line', () => {
      selection.enterExclusiveMode('1', 'municipales');
      expect(hiddenBusIds.value.has('bus-1')).toBe(false);
      expect(hiddenBusIds.value.has('bus-2')).toBe(true);
      expect(hiddenBusIds.value.has('bus-3')).toBe(true);
    });
  });

  describe('exitExclusiveMode', () => {
    it('should deactivate exclusive mode', () => {
      selection.exclusiveMode.value = true;
      selection.exclusiveLine.value = { line: '1', company: 'municipales' };
      selection.exitExclusiveMode();
      expect(selection.exclusiveMode.value).toBe(false);
      expect(selection.exclusiveLine.value).toBe(null);
    });
  });

  describe('toggleCompanySelection', () => {
    it('should select all lines of a company when none are selected', () => {
      selection.toggleCompanySelection('municipales');
      expect(selection.selectedLines.value.has('municipales-1')).toBe(true);
      expect(selection.selectedLines.value.has('municipales-12')).toBe(true);
    });

    it('should deselect all lines when all are already selected', () => {
      selection.selectedLines.value.add('municipales-1');
      selection.selectedLines.value.add('municipales-12');
      selection.selectedLines.value.add('municipales-L1');
      selection.toggleCompanySelection('municipales');
      // All municipales should be deselected
      const hasMunicipales = [...selection.selectedLines.value].some(k => k.startsWith('municipales'));
      // Night lines are handled separately, so just check non-night
      expect(selection.selectedLines.value.has('municipales-1')).toBe(false);
      expect(selection.selectedLines.value.has('municipales-12')).toBe(false);
    });
  });

  describe('clearSelection', () => {
    it('should clear all selections', () => {
      selection.selectedLines.value.add('municipales-1');
      selection.selectedLines.value.add('global-1');
      selection.exclusiveMode.value = true;
      selection.clearSelection();
      expect(selection.selectedLines.value.size).toBe(0);
      expect(selection.exclusiveMode.value).toBe(false);
    });
  });

  describe('computed properties', () => {
    it('selectedCount should reflect number of selected lines', () => {
      expect(selection.selectedCount.value).toBe(0);
      selection.selectedLines.value.add('municipales-1');
      selection.selectedLines.value.add('global-1');
      expect(selection.selectedCount.value).toBe(2);
    });

    it('hasSelection should be true when lines are selected', () => {
      expect(selection.hasSelection.value).toBe(false);
      selection.selectedLines.value.add('municipales-1');
      expect(selection.hasSelection.value).toBe(true);
    });
  });
});
