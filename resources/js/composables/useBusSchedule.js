/**
 * Composable for bus service schedule management
 * Determines if buses should be in service based on time and type
 */
export function useBusSchedule() {
  /**
   * Check if it's night time (between 00:00 and 06:00)
   * @returns {boolean}
   */
  const isNightTime = () => {
    const hour = new Date().getHours();
    return hour >= 0 && hour < 6;
  };

  /**
   * Check if a bus should be in service based on current time
   * @param {string} busType - Type of bus (urban/interurban/night)
   * @param {string} lineNumber - Line number (unused but kept for future extensions)
   * @returns {boolean}
   */
  const isInService = (busType, lineNumber) => {
    const now = new Date();
    const hour = now.getHours();
    const dayOfWeek = now.getDay(); // 0 = Sunday, 6 = Saturday

    // Night lines: only Friday, Saturday and holiday eves, 00:00-06:00
    if (busType === 'night') {
      const isWeekendNight = dayOfWeek === 0 || dayOfWeek === 6 || dayOfWeek === 5;
      return isWeekendNight && isNightTime();
    }

    // Urban lines: 06:00 - 23:30
    if (busType === 'urban') {
      return hour >= 6 && hour < 24;
    }

    // Interurban lines: 05:30 - 22:00 (reduced service on Sundays)
    if (busType === 'interurban') {
      if (dayOfWeek === 0) { // Sunday - reduced service
        return hour >= 7 && hour < 22;
      }
      return hour >= 5 && hour < 22;
    }

    return false;
  };

  return {
    isNightTime,
    isInService
  };
}
