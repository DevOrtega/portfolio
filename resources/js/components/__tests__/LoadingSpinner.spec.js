import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

describe('LoadingSpinner', () => {
  it('renders with default medium size', () => {
    const wrapper = mount(LoadingSpinner)
    
    const spinner = wrapper.find('.animate-spin')
    expect(spinner.exists()).toBe(true)
    expect(spinner.classes()).toContain('h-12')
    expect(spinner.classes()).toContain('w-12')
  })

  it('renders with small size', () => {
    const wrapper = mount(LoadingSpinner, {
      props: {
        size: 'small'
      }
    })
    
    const spinner = wrapper.find('.animate-spin')
    expect(spinner.classes()).toContain('h-6')
    expect(spinner.classes()).toContain('w-6')
  })

  it('renders with large size', () => {
    const wrapper = mount(LoadingSpinner, {
      props: {
        size: 'large'
      }
    })
    
    const spinner = wrapper.find('.animate-spin')
    expect(spinner.classes()).toContain('h-16')
    expect(spinner.classes()).toContain('w-16')
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

  it('applies large min-height for large size', () => {
    const wrapper = mount(LoadingSpinner, {
      props: {
        size: 'large'
      }
    })
    
    const container = wrapper.find('.flex')
    expect(container.attributes('style')).toContain('min-height: 16rem')
  })

  it('does not apply large min-height for small size', () => {
    const wrapper = mount(LoadingSpinner, {
      props: {
        size: 'small'
      }
    })
    
    const container = wrapper.find('.flex')
    expect(container.attributes('style')).toContain('min-height: auto')
  })
})
