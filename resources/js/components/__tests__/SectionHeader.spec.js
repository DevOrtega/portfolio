import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import SectionHeader from '@/components/SectionHeader.vue'

describe('SectionHeader', () => {
  it('renders title correctly', () => {
    const wrapper = mount(SectionHeader, {
      props: {
        title: 'Skills'
      }
    })

    expect(wrapper.text()).toContain('Skills')
  })

  it('renders without default icon when not specified', () => {
    const wrapper = mount(SectionHeader, {
      props: {
        title: 'Custom Section'
      }
    })

    expect(wrapper.text()).toContain('Custom Section')
    expect(wrapper.find('svg').exists()).toBe(false)
  })

  it('renders skills icon when specified', () => {
    const wrapper = mount(SectionHeader, {
      props: {
        title: 'Skills',
        defaultIcon: 'skills'
      }
    })

    expect(wrapper.find('svg').exists()).toBe(true)
    expect(wrapper.find('.text-indigo-400').exists()).toBe(true)
  })

  it('renders experience icon when specified', () => {
    const wrapper = mount(SectionHeader, {
      props: {
        title: 'Experience',
        defaultIcon: 'experience'
      }
    })

    expect(wrapper.find('svg').exists()).toBe(true)
  })

  it('renders education icon when specified', () => {
    const wrapper = mount(SectionHeader, {
      props: {
        title: 'Education',
        defaultIcon: 'education'
      }
    })

    expect(wrapper.find('svg').exists()).toBe(true)
  })

  it('renders projects icon when specified', () => {
    const wrapper = mount(SectionHeader, {
      props: {
        title: 'Projects',
        defaultIcon: 'projects'
      }
    })

    expect(wrapper.find('svg').exists()).toBe(true)
  })

  it('renders custom icon via slot', () => {
    const wrapper = mount(SectionHeader, {
      props: {
        title: 'Custom'
      },
      slots: {
        icon: '<span class="custom-icon">🚀</span>'
      }
    })

    expect(wrapper.html()).toContain('custom-icon')
    expect(wrapper.html()).toContain('🚀')
  })

  it('has proper heading styling', () => {
    const wrapper = mount(SectionHeader, {
      props: {
        title: 'Test'
      }
    })

    const heading = wrapper.find('h2')
    expect(heading.classes()).toContain('text-2xl')
    expect(heading.classes()).toContain('font-bold')
    expect(heading.classes()).toContain('text-white')
    expect(heading.classes()).toContain('mb-6')
  })

  it('displays items in flex layout with gap', () => {
    const wrapper = mount(SectionHeader, {
      props: {
        title: 'Test',
        defaultIcon: 'skills'
      }
    })

    const heading = wrapper.find('h2')
    expect(heading.classes()).toContain('flex')
    expect(heading.classes()).toContain('items-center')
    expect(heading.classes()).toContain('gap-2')
  })

  it('icon has correct size classes', () => {
    const wrapper = mount(SectionHeader, {
      props: {
        title: 'Test',
        defaultIcon: 'skills'
      }
    })

    const svg = wrapper.find('svg')
    expect(svg.classes()).toContain('w-6')
    expect(svg.classes()).toContain('h-6')
  })
})
