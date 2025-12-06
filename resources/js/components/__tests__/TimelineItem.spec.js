import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import TimelineItem from '@/components/TimelineItem.vue'

describe('TimelineItem', () => {
  const defaultProps = {
    title: 'Senior Developer',
    subtitle: 'Tech Company',
    dateRange: '2023-01 - Present',
    description: 'Working on exciting projects'
  }

  it('renders all content correctly', () => {
    const wrapper = mount(TimelineItem, {
      props: defaultProps
    })

    expect(wrapper.text()).toContain('Senior Developer')
    expect(wrapper.text()).toContain('Tech Company')
    expect(wrapper.text()).toContain('2023-01 - Present')
    expect(wrapper.text()).toContain('Working on exciting projects')
  })

  it('renders without subtitle when not provided', () => {
    const wrapper = mount(TimelineItem, {
      props: {
        ...defaultProps,
        subtitle: ''
      }
    })

    expect(wrapper.text()).toContain('Senior Developer')
    expect(wrapper.text()).not.toContain('Tech Company')
  })

  it('applies indigo color by default', () => {
    const wrapper = mount(TimelineItem, {
      props: defaultProps
    })

    expect(wrapper.find('.bg-indigo-500').exists()).toBe(true)
    expect(wrapper.find('.hover\\:border-indigo-500').exists()).toBe(true)
  })

  it('applies cyan color when specified', () => {
    const wrapper = mount(TimelineItem, {
      props: {
        ...defaultProps,
        color: 'cyan'
      }
    })

    expect(wrapper.find('.bg-cyan-500').exists()).toBe(true)
    expect(wrapper.find('.hover\\:border-cyan-500').exists()).toBe(true)
  })

  it('applies purple color when specified', () => {
    const wrapper = mount(TimelineItem, {
      props: {
        ...defaultProps,
        color: 'purple'
      }
    })

    expect(wrapper.find('.bg-purple-500').exists()).toBe(true)
  })

  it('applies green color when specified', () => {
    const wrapper = mount(TimelineItem, {
      props: {
        ...defaultProps,
        color: 'green'
      }
    })

    expect(wrapper.find('.bg-green-500').exists()).toBe(true)
  })

  it('has timeline dot positioned correctly', () => {
    const wrapper = mount(TimelineItem, {
      props: defaultProps
    })

    const dot = wrapper.find('.absolute')
    expect(dot.classes()).toContain('h-5')
    expect(dot.classes()).toContain('w-5')
    expect(dot.classes()).toContain('rounded-full')
  })

  it('preserves whitespace in description', () => {
    const multilineDescription = 'Line 1\nLine 2\nLine 3'
    const wrapper = mount(TimelineItem, {
      props: {
        ...defaultProps,
        description: multilineDescription
      }
    })

    const description = wrapper.find('.whitespace-pre-line')
    expect(description.exists()).toBe(true)
  })

  it('has proper card styling', () => {
    const wrapper = mount(TimelineItem, {
      props: defaultProps
    })

    const card = wrapper.find('.bg-gray-800')
    expect(card.classes()).toContain('rounded-xl')
    expect(card.classes()).toContain('p-6')
    expect(card.classes()).toContain('border')
    expect(card.classes()).toContain('border-gray-700')
  })

  it('badge has correct styling for indigo', () => {
    const wrapper = mount(TimelineItem, {
      props: {
        title: 'Test Title',
        subtitle: 'Test Subtitle',
        dateRange: '2020 - 2021',
        description: 'Test description',
        color: 'indigo',
      },
    })

    const badge = wrapper.find('.text-indigo-300')
    // Check that badge has the class (Tailwind processes /10 as separate class)
    expect(badge.classes()).toContain('text-indigo-300')
    expect(badge.classes()).toContain('px-2')
    expect(badge.classes()).toContain('py-1')
    expect(badge.classes()).toContain('rounded-full')
  })
})
