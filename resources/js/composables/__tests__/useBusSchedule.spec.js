import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest'
import { useBusSchedule } from '@/composables/useBusSchedule'

describe('useBusSchedule', () => {
  const { isNightTime, isInService } = useBusSchedule()

  let originalDate

  beforeEach(() => {
    originalDate = Date
  })

  afterEach(() => {
    global.Date = originalDate
  })

  const mockDate = (hour, dayOfWeek = 1) => {
    const mockDateInstance = new originalDate()
    mockDateInstance.getHours = () => hour
    mockDateInstance.getDay = () => dayOfWeek
    
    global.Date = class extends originalDate {
      constructor() {
        super()
        return mockDateInstance
      }
    }
  }

  describe('isNightTime', () => {
    it('returns true between 23:00 and 05:59', () => {
      mockDate(23) // 23:00
      expect(isNightTime()).toBe(true)

      mockDate(0) // 00:00
      expect(isNightTime()).toBe(true)

      mockDate(3) // 03:00
      expect(isNightTime()).toBe(true)

      mockDate(5) // 05:00
      expect(isNightTime()).toBe(true)
    })

    it('returns false during day hours', () => {
      mockDate(6) // 06:00
      expect(isNightTime()).toBe(false)

      mockDate(12) // 12:00
      expect(isNightTime()).toBe(false)

      mockDate(22) // 22:00
      expect(isNightTime()).toBe(false)
    })
  })

  describe('isInService - night buses', () => {
    it('returns true for night buses on Friday night (00:00-05:59)', () => {
      mockDate(2, 5) // Friday, 02:00
      expect(isInService('night', 'N1')).toBe(true)
    })

    it('returns true for night buses on Saturday night', () => {
      mockDate(1, 6) // Saturday, 01:00
      expect(isInService('night', 'N1')).toBe(true)
    })

    it('returns true for night buses on Sunday early morning', () => {
      mockDate(4, 0) // Sunday, 04:00
      expect(isInService('night', 'N1')).toBe(true)
    })

    it('returns true for night buses on weekday nights (default schedule)', () => {
      // Default night schedule: weekdays 23:00 - 05:00
      mockDate(23, 1) // Monday, 23:00
      expect(isInService('night', 'N1')).toBe(true)

      mockDate(2, 3) // Wednesday, 02:00
      expect(isInService('night', 'N1')).toBe(true)
    })

    it('returns false for night buses during day hours', () => {
      mockDate(12, 5) // Friday, 12:00
      expect(isInService('night', 'N1')).toBe(false)
    })
  })

  describe('isInService - urban buses', () => {
    it('returns true during service hours (06:00-23:59)', () => {
      mockDate(6) // 06:00
      expect(isInService('urban', '1')).toBe(true)

      mockDate(12) // 12:00
      expect(isInService('urban', '1')).toBe(true)

      mockDate(23) // 23:00
      expect(isInService('urban', '1')).toBe(true)
    })

    it('returns false outside service hours', () => {
      mockDate(5) // 05:00
      expect(isInService('urban', '1')).toBe(false)

      mockDate(2) // 02:00
      expect(isInService('urban', '1')).toBe(false)
    })

    it('returns true on all days of the week', () => {
      for (let day = 0; day <= 6; day++) {
        mockDate(10, day) // 10:00
        expect(isInService('urban', '1')).toBe(true)
      }
    })
  })

  describe('isInService - interurban buses', () => {
    it('returns true during weekday service hours (05:00-23:00)', () => {
      mockDate(6, 1) // Monday, 06:00 (not in late night)
      expect(isInService('interurban', '30')).toBe(true)

      mockDate(14, 3) // Wednesday, 14:00
      expect(isInService('interurban', '30')).toBe(true)

      mockDate(21, 4) // Thursday, 21:00
      expect(isInService('interurban', '30')).toBe(true)
    })

    it('returns false during late night hours (00:00-05:59)', () => {
      mockDate(4, 1) // Monday, 04:00 (late night)
      expect(isInService('interurban', '30')).toBe(false)

      mockDate(2, 2) // Tuesday, 02:00 (late night)
      expect(isInService('interurban', '30')).toBe(false)
    })

    it('has reduced service on Sundays (07:00-22:00)', () => {
      mockDate(7, 0) // Sunday, 07:00
      expect(isInService('interurban', '30')).toBe(true)

      mockDate(14, 0) // Sunday, 14:00
      expect(isInService('interurban', '30')).toBe(true)

      mockDate(22, 0) // Sunday, 22:00
      expect(isInService('interurban', '30')).toBe(true)
    })

    it('has normal service on Saturdays', () => {
      mockDate(6, 6) // Saturday, 06:00
      expect(isInService('interurban', '30')).toBe(true)

      mockDate(21, 6) // Saturday, 21:00
      expect(isInService('interurban', '30')).toBe(true)
    })
  })

  describe('isInService - unknown bus type', () => {
    it('returns false for unknown bus types', () => {
      mockDate(12) // 12:00
      expect(isInService('express', '1')).toBe(false)
      expect(isInService('special', '1')).toBe(false)
    })
  })
})
