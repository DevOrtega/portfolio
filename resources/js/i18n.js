import { createI18n } from 'vue-i18n';
import es from './locales/es';
import en from './locales/en';

const messages = {
  es,
  en
};

const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('locale') || 'es',
  fallbackLocale: 'es',
  messages,
  globalInjection: true
});

export default i18n;
