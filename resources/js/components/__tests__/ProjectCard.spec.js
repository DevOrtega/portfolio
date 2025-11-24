import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import ProjectCard from '@/components/ProjectCard.vue'

describe('ProjectCard', () => {
  const mockProject = {
    id: 1,
    title: 'Test Project',
    description: 'This is a test project description',
    tags: ['Vue.js', 'Laravel', 'Tailwind'],
    image_path: '/images/test.png',
    url: 'https://example.com',
    github_url: 'https://github.com/test/project'
  }

  it('renders project title and description', () => {
    const wrapper = mount(ProjectCard, {
      props: {
        project: mockProject
      }
    })

    expect(wrapper.text()).toContain('Test Project')
    expect(wrapper.text()).toContain('This is a test project description')
  })

  it('renders all tags', () => {
    const wrapper = mount(ProjectCard, {
      props: {
        project: mockProject
      }
    })

    expect(wrapper.text()).toContain('Vue.js')
    expect(wrapper.text()).toContain('Laravel')
    expect(wrapper.text()).toContain('Tailwind')
  })

  it('renders project image when provided', () => {
    const wrapper = mount(ProjectCard, {
      props: {
        project: mockProject
      }
    })

    const img = wrapper.find('img')
    expect(img.exists()).toBe(true)
    expect(img.attributes('src')).toBe('/images/test.png')
    expect(img.attributes('alt')).toBe('Test Project')
  })

  it('renders first letter as placeholder when no image', () => {
    const projectWithoutImage = { ...mockProject, image_path: null }
    const wrapper = mount(ProjectCard, {
      props: {
        project: projectWithoutImage
      }
    })

    expect(wrapper.text()).toContain('T') // First letter of "Test Project"
    expect(wrapper.find('img').exists()).toBe(false)
  })

  it('renders GitHub link when provided', () => {
    const wrapper = mount(ProjectCard, {
      props: {
        project: mockProject
      }
    })

    const githubLink = wrapper.find('a[href="https://github.com/test/project"]')
    expect(githubLink.exists()).toBe(true)
    expect(githubLink.attributes('target')).toBe('_blank')
  })

  it('does not render GitHub link when not provided', () => {
    const projectWithoutGithub = { ...mockProject, github_url: null }
    const wrapper = mount(ProjectCard, {
      props: {
        project: projectWithoutGithub
      }
    })

    const githubLink = wrapper.find('a[title="View Code"]')
    expect(githubLink.exists()).toBe(false)
  })

  it('renders external project URL correctly', () => {
    const wrapper = mount(ProjectCard, {
      props: {
        project: mockProject
      }
    })

    const projectLink = wrapper.find('a[href="https://example.com"]')
    expect(projectLink.exists()).toBe(true)
    expect(projectLink.attributes('target')).toBe('_blank')
  })

  it('handles internal URLs with router-link', () => {
    const wrapper = mount(ProjectCard, {
      props: {
        project: {
          title: 'Test Project',
          description: 'This is a test project description',
          image: '/images/test.png',
          tags: ['Vue.js', 'Laravel', 'Tailwind'],
          github_url: 'https://github.com/test/project',
          url: '/projects/test',
        },
      },
      global: {
        stubs: {
          RouterLink: true,
        },
      },
    })

    // Check for RouterLink component (it will be stubbed)
    const routerLink = wrapper.findComponent({ name: 'RouterLink' })
    expect(routerLink.exists()).toBe(true)
  })

  it('has hover effects on card', () => {
    const wrapper = mount(ProjectCard, {
      props: {
        project: mockProject
      }
    })

    const card = wrapper.find('.bg-gray-800')
    expect(card.classes()).toContain('hover:border-indigo-500')
    expect(card.classes()).toContain('hover:shadow-xl')
    expect(card.classes()).toContain('hover:-translate-y-1')
  })

  it('has hover effects on image', () => {
    const wrapper = mount(ProjectCard, {
      props: {
        project: mockProject
      }
    })

    const img = wrapper.find('img')
    expect(img.classes()).toContain('group-hover:scale-110')
  })
})
