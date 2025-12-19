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
  });
});
