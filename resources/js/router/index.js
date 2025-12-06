import { createRouter, createWebHistory } from 'vue-router';

/**
 * Vue Router Configuration
 * 
 * Uses lazy loading (dynamic imports) for all views to improve initial bundle size.
 * Each route loads its component only when navigated to.
 */
const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('@/views/HomeView.vue'),
        meta: { title: 'Home' }
    },
    {
        path: '/projects',
        name: 'projects',
        component: () => import('@/views/ProjectsView.vue'),
        meta: { title: 'Projects' }
    },
    {
        path: '/projects/demo/atm-manager',
        name: 'atm-manager',
        component: () => import('@/views/MapDemoView.vue'),
        meta: { title: 'ATM Manager Demo' }
    },
    {
        path: '/projects/demo/guaguas-tracker',
        name: 'guaguas-tracker',
        component: () => import('@/views/demos/GuaguasTracker.vue'),
        meta: { title: 'Guaguas Tracker Demo' }
    },
    {
        path: '/resume',
        name: 'resume',
        component: () => import('@/views/ResumeView.vue'),
        meta: { title: 'Resume' }
    },
    {
        path: '/contact',
        name: 'contact',
        component: () => import('@/views/ContactView.vue'),
        meta: { title: 'Contact' }
    },
    // Catch-all 404 route
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/HomeView.vue'),
        meta: { title: 'Not Found' }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        // Return to saved position on back/forward navigation
        if (savedPosition) {
            return savedPosition;
        }
        // Scroll to top on new navigation
        return { top: 0 };
    }
});

export default router;
