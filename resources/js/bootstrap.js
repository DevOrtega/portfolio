import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add interceptor to include locale in all API requests
window.axios.interceptors.request.use((config) => {
    const locale = localStorage.getItem('locale') || 'es';
    config.headers['Accept-Language'] = locale;
    return config;
});

