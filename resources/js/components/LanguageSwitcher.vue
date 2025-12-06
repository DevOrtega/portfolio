<template>
  <div class="language-switcher">
    <button
      v-for="lang in availableLanguages"
      :key="lang.code"
      @click="changeLanguage(lang.code)"
      :class="['language-btn', { active: currentLocale === lang.code }]"
      :title="lang.name"
    >
      <img 
        :src="`https://flagcdn.com/w20/${lang.flagCode}.png`" 
        :srcset="`https://flagcdn.com/w40/${lang.flagCode}.png 2x`"
        :alt="lang.name"
        class="flag-img"
        width="20"
        height="15"
      />
      {{ lang.label }}
    </button>
  </div>
</template>

<script setup>
/**
 * @component LanguageSwitcher
 * @description Language toggle buttons for switching between ES and EN
 * 
 * Uses useLocale composable for reactive locale changes without page reload.
 */
import { computed } from 'vue';
import { useLocale } from '@/composables/useLocale';

const { locale, changeLocale } = useLocale();

const availableLanguages = [
  { code: 'es', label: 'ES', name: 'Español', flagCode: 'es' },
  { code: 'en', label: 'EN', name: 'English', flagCode: 'gb' }
];

const currentLocale = computed(() => locale.value);

/**
 * Change application language
 * Uses reactive locale management - no page reload needed
 */
const changeLanguage = (lang) => {
  changeLocale(lang);
};
</script>

<style scoped>
.language-switcher {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.language-btn {
  padding: 0.375rem 0.75rem;
  border: 1px solid #374151;
  background: #1f2937;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.875rem;
  font-weight: 500;
  color: #9ca3af;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.language-btn:hover {
  background: #374151;
  border-color: #4b5563;
  color: #e5e7eb;
}

.language-btn.active {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.flag-img {
  display: inline-block;
  vertical-align: middle;
  border-radius: 2px;
  flex-shrink: 0;
}
</style>
