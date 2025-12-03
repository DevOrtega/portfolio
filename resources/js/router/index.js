import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import ProjectsView from '../views/ProjectsView.vue';
import ResumeView from '../views/ResumeView.vue';
import ContactView from '../views/ContactView.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView
    },
    {
        path: '/projects',
        name: 'projects',
        component: ProjectsView
    },
    {
        path: '/projects/demo/atm-manager',
        name: 'atm-manager',
        component: () => import('../views/MapDemoView.vue')
    },
    {
        path: '/projects/demo/guaguas-tracker',
        name: 'guaguas-tracker',
        component: () => import('../views/demos/GuaguasTracker.vue')
    },
    {
        path: '/resume',
        name: 'resume',
        component: ResumeView
    },
    {
        path: '/contact',
        name: 'contact',
        component: ContactView
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
