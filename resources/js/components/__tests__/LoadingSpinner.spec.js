import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

describe('LoadingSpinner', () => {
  it('renders with default medium size', () => {
    const wrapper = mount(LoadingSpinner)
    
    const spinner = wrapper.find('.animate-spin')
    expect(spinner.exists()).toBe(true)
    expect(spinner.classes()).toContain('size-12')
  })

  it('renders with small size', () => {
    const wrapper = mount(LoadingSpinner, {
      props: {
        size: 'small'
      }
    })
    
    const spinner = wrapper.find('.animate-spin')
    expect(spinner.classes()).toContain('size-6')
  })

  it('renders with large size', () => {
    const wrapper = mount(LoadingSpinner, {
      props: {
        size: 'large'
      }
    })
    
    const spinner = wrapper.find('.animate-spin')
    expect(spinner.classes()).toContain('size-16')
  })

  it('has proper accessibility attributes', () => {
    const wrapper = mount(LoadingSpinner)
    
    const spinner = wrapper.find('.animate-spin')
    expect(spinner.attributes('role')).toBe('status')
    expect(spinner.attributes('aria-label')).toBe('Loading')
  })

  it('applies correct styling classes', () => {
    const wrapper = mount(LoadingSpinner)
    
    const spinner = wrapper.find('.animate-spin')
    expect(spinner.classes()).toContain('rounded-full')
    expect(spinner.classes()).toContain('border-t-2')
    expect(spinner.classes()).toContain('border-b-2')
    expect(spinner.classes()).toContain('border-indigo-500')
  })

  it('centers the spinner in container', () => {
    const wrapper = mount(LoadingSpinner)
    
    const container = wrapper.find('.flex')
    expect(container.classes()).toContain('justify-center')
    expect(container.classes()).toContain('items-center')
  })

  it('applies large min-height class for large size', () => {
    const wrapper = mount(LoadingSpinner, {
      props: {
        size: 'large'
      }
    })
    
    const container = wrapper.find('.flex')
    expect(container.classes()).toContain('min-h-64')
  })

  it('does not apply large min-height class for small size', () => {
    const wrapper = mount(LoadingSpinner, {
      props: {
        size: 'small'
      }
    })
    
    const container = wrapper.find('.flex')
    expect(container.classes()).not.toContain('min-h-64')
  })
})
