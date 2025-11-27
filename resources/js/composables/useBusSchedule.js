/**
 * Composable for bus service schedule management
 * Determines if buses should be in service based on time and type
 * Based on real schedules from guaguas.com and guaguasglobal.com
 */
export function useBusSchedule() {
  /**
   * Check if it's night time (between 23:00 and 06:00)
   * @returns {boolean}
   */
  const isNightTime = () => {
    const hour = new Date().getHours();
    return hour >= 23 || hour < 6;
  };

  /**
   * Check if it's late night (00:00 - 06:00) - when only night lines run
   * @returns {boolean}
   */
  const isLateNight = () => {
    const hour = new Date().getHours();
    return hour >= 0 && hour < 6;
  };

  /**
   * Check if a bus should be in service based on current time
   * @param {string} busType - Type of bus (urban/interurban/night)
   * @param {string} lineNumber - Line number for specific schedule rules
   * @returns {boolean}
   */
  const isInService = (busType, lineNumber) => {
    const now = new Date();
    const hour = now.getHours();
    const dayOfWeek = now.getDay(); // 0 = Sunday, 6 = Saturday

    // Night lines (L1, L2, L3, 64, 65): 23:00 - 06:00 every day
    // Some have extended hours on weekends
    if (busType === 'night') {
      const isWeekend = dayOfWeek === 0 || dayOfWeek === 6 || dayOfWeek === 5;
      
      // L1, L2, L3 run every night 23:30 - 06:00
      if (['L1', 'L2', 'L3'].includes(lineNumber)) {
        return hour >= 23 || hour < 6;
      }
      
      // 64, 65 (Medianoche lines) run from 22:00 - 00:30
      if (['64', '65'].includes(lineNumber)) {
        return hour >= 22 || hour < 1;
      }
      
      // Default night schedule
      return isWeekend ? (hour >= 23 || hour < 6) : (hour >= 23 || hour < 5);
    }

    // Urban lines: 06:00 - 23:30 (some start at 05:30)
    if (busType === 'urban') {
      // During late night, urban buses don't run (night lines take over)
      if (isLateNight()) {
        return false;
      }
      return hour >= 6 || hour === 23;
    }

    // Interurban lines: 05:30 - 23:00 (reduced service on Sundays)
    if (busType === 'interurban') {
      // During late night, interurban buses don't run
      if (isLateNight()) {
        return false;
      }
      
      if (dayOfWeek === 0) { // Sunday - reduced service
        return hour >= 7 && hour <= 22;
      }
      return hour >= 5 && hour <= 23;
    }

    return false;
  };

  return {
    isNightTime,
    isLateNight,
    isInService
  };
}
