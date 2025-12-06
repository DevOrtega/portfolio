/**
 * @file i18n.js
 * @description Vue I18n configuration with locale detection and persistence
 */
import { createI18n } from 'vue-i18n';
import es from './locales/es';
import en from './locales/en';

const messages = {
  es,
  en
};

/**
 * Get initial locale from localStorage or browser preference
 * @returns {string} Locale code ('es' or 'en')
 */
const getInitialLocale = () => {
  // Check localStorage first
  const savedLocale = localStorage.getItem('locale');
  if (savedLocale && ['es', 'en'].includes(savedLocale)) {
    return savedLocale;
  }
  
  // Check browser language
  const browserLocale = navigator.language?.split('-')[0];
  if (browserLocale && ['es', 'en'].includes(browserLocale)) {
    return browserLocale;
  }
  
  // Default to Spanish
  return 'es';
};

const i18n = createI18n({
  legacy: false,
  locale: getInitialLocale(),
  fallbackLocale: 'es',
  messages,
  globalInjection: true,
  // Handle missing translations gracefully
  missingWarn: import.meta.env.DEV,
  fallbackWarn: import.meta.env.DEV
});

export default i18n;
