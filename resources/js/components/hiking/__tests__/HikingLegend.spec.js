import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';
import HikingLegend from '../HikingLegend.vue';

describe('HikingLegend', () => {
  it('renders legend title and items', () => {
    const wrapper = mount(HikingLegend, {
      global: {
        mocks: {
          $t: (msg) => msg
        }
      }
    });
    
    expect(wrapper.text()).toContain('hiking.legend.title');
    expect(wrapper.text()).toContain('hiking.legend.start');
    expect(wrapper.text()).toContain('hiking.legend.food');
    expect(wrapper.text()).toContain('hiking.legend.peak');
    expect(wrapper.text()).toContain('hiking.legend.health');
    expect(wrapper.text()).toContain('hiking.legend.accommodation');
    expect(wrapper.text()).toContain('hiking.legend.camping');
  });

  it('renders color indicators', () => {
    const wrapper = mount(HikingLegend, {
        global: {
          mocks: {
            $t: (msg) => msg
          }
        }
      });
      
    // Check for Start (Emerald)
    expect(wrapper.html()).toContain('bg-[#10b981]');
    // Check for End (Red)
    expect(wrapper.html()).toContain('bg-[#ef4444]');
    // Check for Camping (Forest Green)
    expect(wrapper.html()).toContain('bg-[#16a34a]');
    // Check for Health (Pink)
    expect(wrapper.html()).toContain('bg-[#db2777]');
  });
});
