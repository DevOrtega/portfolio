import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import StatsCard from '@/components/StatsCard.vue'

describe('StatsCard', () => {
  it('renders title and value correctly', () => {
    const wrapper = mount(StatsCard, {
      props: {
        title: 'Active Users',
        value: 150,
        colorClass: 'text-green-400'
      }
    })

    expect(wrapper.text()).toContain('Active Users')
    expect(wrapper.text()).toContain('150')
  })

  it('renders with default color class', () => {
    const wrapper = mount(StatsCard, {
      props: {
        title: 'Total',
        value: 100
      }
    })

    const valueElement = wrapper.find('.text-2xl')
    expect(valueElement.classes()).toContain('text-white')
  })

  it('applies custom color class', () => {
    const wrapper = mount(StatsCard, {
      props: {
        title: 'Errors',
        value: 5,
        colorClass: 'text-red-400'
      }
    })

    const valueElement = wrapper.find('.text-2xl')
    expect(valueElement.classes()).toContain('text-red-400')
  })

  it('handles string values', () => {
    const wrapper = mount(StatsCard, {
      props: {
        title: 'Status',
        value: 'Online',
        colorClass: 'text-green-400'
      }
    })

    expect(wrapper.text()).toContain('Online')
  })

  it('handles numeric values', () => {
    const wrapper = mount(StatsCard, {
      props: {
        title: 'Count',
        value: 999,
        colorClass: 'text-blue-400'
      }
    })

    expect(wrapper.text()).toContain('999')
  })

  it('has hover effect classes', () => {
    const wrapper = mount(StatsCard, {
      props: {
        title: 'Test',
        value: 0
      }
    })

    const container = wrapper.find('.bg-gray-800')
    expect(container.classes()).toContain('hover:border-gray-600')
    expect(container.classes()).toContain('hover:shadow-lg')
  })

  it('displays title in uppercase with proper styling', () => {
    const wrapper = mount(StatsCard, {
      props: {
        title: 'total items',
        value: 42
      }
    })

    const titleElement = wrapper.find('.text-\\[10px\\]')
    expect(titleElement.classes()).toContain('uppercase')
    expect(titleElement.classes()).toContain('tracking-wider')
  })
})
