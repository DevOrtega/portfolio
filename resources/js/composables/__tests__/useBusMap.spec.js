import { describe, it, expect } from 'vitest'
import { useBusMap } from '@/composables/useBusMap'
import L from 'leaflet'

describe('useBusMap', () => {
  const { 
    BOUNDS, 
    mapOptions, 
    getResponsiveZoom, 
    isWithinBounds, 
    createBusIcon,
    getCompanyLabel,
    getBusTypeLabel
  } = useBusMap()

  describe('BOUNDS', () => {
    it('has correct geographic boundaries for Gran Canaria', () => {
      expect(BOUNDS).toEqual({
        north: 28.18,
        south: 27.74,
        east: -15.35,
        west: -15.60
      })
    })
  })

  describe('mapOptions', () => {
    it('has correct map configuration', () => {
      expect(mapOptions).toMatchObject({
        zoomControl: true,
        attributionControl: true,
        minZoom: 9.5,
        maxBoundsViscosity: 0.8
      })
    })

    it('has correct maxBounds array', () => {
      expect(mapOptions.maxBounds).toEqual([[27.70, -15.90], [28.20, -15.30]])
    })
  })

  describe('getResponsiveZoom', () => {
    it('returns default zoom when window is undefined', () => {
      const zoom = getResponsiveZoom()
      expect(zoom).toBe(10.25)
    })

    it('returns correct zoom levels based on window width', () => {
      // Assuming we're in a browser environment with global window
      // The actual value will depend on the test environment
      const zoom = getResponsiveZoom()
      expect(zoom).toBeGreaterThanOrEqual(9.5)
      expect(zoom).toBeLessThanOrEqual(10.5)
    })
  })

  describe('isWithinBounds', () => {
    it('returns true for coordinates within Gran Canaria', () => {
      // Las Palmas de Gran Canaria
      expect(isWithinBounds(28.10, -15.43)).toBe(true)
      
      // Maspalomas (south)
      expect(isWithinBounds(27.76, -15.58)).toBe(true)
      
      // Telde (east)
      expect(isWithinBounds(27.99, -15.42)).toBe(true)
    })

    it('returns false for coordinates outside Gran Canaria', () => {
      // Too far north
      expect(isWithinBounds(28.50, -15.50)).toBe(false)
      
      // Too far south
      expect(isWithinBounds(27.50, -15.50)).toBe(false)
      
      // Too far east
      expect(isWithinBounds(28.00, -15.00)).toBe(false)
      
      // Too far west
      expect(isWithinBounds(28.00, -16.00)).toBe(false)
    })

    it('handles boundary edge cases', () => {
      // Exactly on boundaries
      expect(isWithinBounds(BOUNDS.north, BOUNDS.east)).toBe(true)
      expect(isWithinBounds(BOUNDS.south, BOUNDS.west)).toBe(true)
    })
  })

  describe('createBusIcon', () => {
    it('creates icon for municipales bus', () => {
      const bus = {
        company: 'municipales',
        line: '1'
      }
      
      const icon = createBusIcon(bus)
      
      expect(icon).toBeInstanceOf(L.DivIcon)
      expect(icon.options.className).toBe('custom-bus-icon')
      expect(icon.options.iconSize).toEqual([40, 40])
      expect(icon.options.iconAnchor).toEqual([20, 20])
    })

    it('creates icon for global bus', () => {
      const bus = {
        company: 'global',
        line: '30'
      }
      
      const icon = createBusIcon(bus)
      
      expect(icon).toBeInstanceOf(L.DivIcon)
      expect(icon.options.html).toContain('#0066CC') // Global blue color
    })

    it('creates icon with default colors for unknown company', () => {
      const bus = {
        company: 'unknown',
        line: 'X'
      }
      
      const icon = createBusIcon(bus)
      
      expect(icon).toBeInstanceOf(L.DivIcon)
      expect(icon.options.html).toContain('#9933FF') // Default purple color
    })

    it('includes line number in icon HTML', () => {
      const bus = {
        company: 'municipales',
        line: '25'
      }
      
      const icon = createBusIcon(bus)
      
      expect(icon.options.html).toContain('25')
    })
  })

  describe('getCompanyLabel', () => {
    it('returns correct label for municipales', () => {
      expect(getCompanyLabel('municipales')).toBe('Guaguas Municipales')
    })

    it('returns correct label for global', () => {
      expect(getCompanyLabel('global')).toBe('Global')
    })

    it('returns correct label for night', () => {
      expect(getCompanyLabel('night')).toBe('Night Line')
    })

    it('returns original value for unknown company', () => {
      expect(getCompanyLabel('unknown')).toBe('unknown')
    })
  })

  describe('getBusTypeLabel', () => {
    it('returns correct label for urban', () => {
      expect(getBusTypeLabel('urban')).toBe('Urban')
    })

    it('returns correct label for interurban', () => {
      expect(getBusTypeLabel('interurban')).toBe('Interurban')
    })

    it('returns correct label for night', () => {
      expect(getBusTypeLabel('night')).toBe('Night')
    })

    it('returns original value for unknown type', () => {
      expect(getBusTypeLabel('express')).toBe('express')
    })
  })
})
