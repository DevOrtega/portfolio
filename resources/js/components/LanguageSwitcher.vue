<template>
  <div class="language-switcher">
    <button
      v-for="lang in availableLanguages"
      :key="lang.code"
      @click="changeLanguage(lang.code)"
      :class="['language-btn', { active: currentLocale === lang.code }]"
      :title="lang.name"
    >
      {{ lang.flag }} {{ lang.label }}
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();

const availableLanguages = [
  { code: 'es', label: 'ES', name: 'Español', flag: '🇪🇸' },
  { code: 'en', label: 'EN', name: 'English', flag: '🇬🇧' }
];

const currentLocale = computed(() => locale.value);

const changeLanguage = (lang) => {
  locale.value = lang;
  localStorage.setItem('locale', lang);
  document.documentElement.lang = lang;
};
</script>

<style scoped>
.language-switcher {
  display: flex;
  gap: 0.5rem;
}

.language-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.875rem;
  font-weight: 500;
  color: #6b7280;
}

.language-btn:hover {
  background: #f9fafb;
  border-color: #d1d5db;
}

.language-btn.active {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}
</style>
