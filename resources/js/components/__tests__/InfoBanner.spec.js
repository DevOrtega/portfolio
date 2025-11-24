import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import InfoBanner from '@/components/InfoBanner.vue'

describe('InfoBanner', () => {
  it('renders with default info type', () => {
    const wrapper = mount(InfoBanner, {
      props: {
        message: 'This is an info message'
      }
    })

    expect(wrapper.text()).toContain('This is an info message')
    expect(wrapper.find('.bg-blue-900\\/30').exists()).toBe(true)
  })

  it('renders warning type correctly', () => {
    const wrapper = mount(InfoBanner, {
      props: {
        type: 'warning',
        message: 'This is a warning'
      }
    })

    expect(wrapper.text()).toContain('This is a warning')
    expect(wrapper.find('.bg-yellow-900\\/30').exists()).toBe(true)
    expect(wrapper.find('.border-yellow-700').exists()).toBe(true)
  })

  it('renders error type correctly', () => {
    const wrapper = mount(InfoBanner, {
      props: {
        type: 'error',
        message: 'This is an error'
      }
    })

    expect(wrapper.find('.bg-red-900\\/30').exists()).toBe(true)
    expect(wrapper.find('.border-red-700').exists()).toBe(true)
  })

  it('renders success type correctly', () => {
    const wrapper = mount(InfoBanner, {
      props: {
        type: 'success',
        message: 'This is a success message'
      }
    })

    expect(wrapper.find('.bg-green-900\\/30').exists()).toBe(true)
    expect(wrapper.find('.border-green-700').exists()).toBe(true)
  })

  it('shows icon by default', () => {
    const wrapper = mount(InfoBanner, {
      props: {
        message: 'Test message'
      }
    })

    expect(wrapper.find('svg').exists()).toBe(true)
  })

  it('hides icon when showIcon is false', () => {
    const wrapper = mount(InfoBanner, {
      props: {
        message: 'Test message',
        showIcon: false
      }
    })

    expect(wrapper.find('svg').exists()).toBe(false)
  })

  it('renders slot content instead of message prop', () => {
    const wrapper = mount(InfoBanner, {
      props: {
        type: 'info'
      },
      slots: {
        default: '<p>Custom slot content</p>'
      }
    })

    expect(wrapper.html()).toContain('Custom slot content')
  })

  it('renders custom icon via slot', () => {
    const wrapper = mount(InfoBanner, {
      props: {
        message: 'Test'
      },
      slots: {
        icon: '<span class="custom-icon">⚠️</span>'
      }
    })

    expect(wrapper.html()).toContain('custom-icon')
    expect(wrapper.html()).toContain('⚠️')
  })

  it('has proper layout classes', () => {
    const wrapper = mount(InfoBanner, {
      props: {
        message: 'Test'
      }
    })

    const container = wrapper.find('.p-4')
    expect(container.classes()).toContain('rounded-lg')
    expect(container.classes()).toContain('border')
    expect(container.classes()).toContain('flex')
    expect(container.classes()).toContain('items-start')
  })
})
