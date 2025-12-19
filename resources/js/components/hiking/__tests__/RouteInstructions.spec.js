import { mount } from '@vue/test-utils';
import { describe, it, expect, vi } from 'vitest';
import RouteInstructions from '../RouteInstructions.vue';

vi.mock('vue-i18n', () => ({
    useI18n: () => ({ t: (key) => key })
}));

describe('RouteInstructions', () => {
    const mockRoute = {
        properties: {
            legs: [
                {
                    steps: [
                        {
                            maneuver: { type: 'turn', modifier: 'left' },
                            name: 'Trail A',
                            distance: 500
                        },
                        {
                            maneuver: { type: 'arrive' },
                            name: 'Destination',
                            distance: 0
                        }
                    ]
                }
            ]
        }
    };

    it('renders instructions correctly', () => {
        const wrapper = mount(RouteInstructions, {
            props: {
                route: mockRoute,
                googleMapsUrl: '#'
            },
            global: {
                mocks: {
                    $t: (msg) => msg
                }
            }
        });

        expect(wrapper.text()).toContain('hiking.instructions');
        expect(wrapper.text()).toContain('hiking.directions.turn.left');
        // Check distance formatting (500m)
        expect(wrapper.text()).toContain('500 m');
    });
    
    it('renders multiple legs headers when present', () => {
         const multiLegRoute = {
            properties: {
                legs: [
                    { steps: [] },
                    { steps: [] }
                ]
            }
        };
        
        const wrapper = mount(RouteInstructions, {
            props: {
                route: multiLegRoute
            },
            global: {
                mocks: {
                    $t: (msg) => msg
                }
            }
        });
        
        expect(wrapper.text()).toContain('hiking.leg 1');
        expect(wrapper.text()).toContain('hiking.leg 2');
    });

    it('formats long distances in km', () => {
        const longRoute = {
            properties: {
                legs: [
                    {
                        steps: [
                            {
                                maneuver: { type: 'straight' },
                                name: 'Long Road',
                                distance: 1500
                            }
                        ]
                    }
                ]
            }
        };

        const wrapper = mount(RouteInstructions, {
            props: { route: longRoute },
            global: {
                mocks: {
                    $t: (msg) => msg
                }
            }
        });

        expect(wrapper.text()).toContain('1.5 km');
    });
});
