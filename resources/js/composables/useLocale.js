/**
 * @file useLocale.js
 * @description Composable for reactive locale management
 * 
 * Provides a centralized way to change locale and notify components
 * to refetch their data without requiring a full page reload.
 */
import { ref, readonly } from 'vue';
import { useI18n } from 'vue-i18n';

// Event bus for locale changes
const localeChangeCallbacks = ref([]);

/**
 * Composable for locale management
 */
export function useLocale() {
  const { locale } = useI18n();

  /**
   * Change the application locale
   * @param {string} newLocale - The new locale code ('es' or 'en')
   */
  const changeLocale = (newLocale) => {
    if (!['es', 'en'].includes(newLocale)) {
      console.warn(`Invalid locale: ${newLocale}`);
      return;
    }

    // Update locale
    locale.value = newLocale;
    localStorage.setItem('locale', newLocale);
    document.documentElement.lang = newLocale;

    // Notify all registered callbacks
    localeChangeCallbacks.value.forEach(callback => {
      try {
        callback(newLocale);
      } catch (error) {
        console.error('Error in locale change callback:', error);
      }
    });
  };

  /**
   * Register a callback to be called when locale changes
   * @param {Function} callback - Function to call with new locale
   * @returns {Function} Unsubscribe function
   */
  const onLocaleChange = (callback) => {
    localeChangeCallbacks.value.push(callback);
    
    // Return unsubscribe function
    return () => {
      const index = localeChangeCallbacks.value.indexOf(callback);
      if (index > -1) {
        localeChangeCallbacks.value.splice(index, 1);
      }
    };
  };

  return {
    locale: readonly(locale),
    changeLocale,
    onLocaleChange
  };
}

export default useLocale;
