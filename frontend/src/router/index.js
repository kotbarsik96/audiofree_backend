import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/index/HomeView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'Home',
            component: HomeView
        },
        // DEVELOPMENT:
        {
            path: '/ui',
            name: 'UI',
            component: () => import('@/views/.dev/UIKit.vue')
        }
    ]
})

export default router
