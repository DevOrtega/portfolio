/**
 * @file app.js
 * @description Main Vue application entry point
 * 
 * Initializes Vue 3 with:
 * - Pinia for state management
 * - Vue Router for navigation
 * - Vue I18n for internationalization
 */
import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import i18n from './i18n';
import App from './App.vue';

// Create Vue application
const app = createApp(App);

// Register plugins
app.use(createPinia());
app.use(router);
app.use(i18n);

// Global error handler for uncaught errors
app.config.errorHandler = (err, instance, info) => {
  console.error('Vue Error:', err);
  console.error('Component:', instance);
  console.error('Info:', info);
};

// Mount application
app.mount('#app');
