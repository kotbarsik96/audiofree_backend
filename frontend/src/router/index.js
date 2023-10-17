import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/index/HomeView.vue'
import { useIndexStore } from '@/stores'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'Home',
            component: HomeView
        },
        {
            path: '/control',
            name: 'Admin',
            component: () => import('@/views/admin/AdminPage.vue'),
            meta: {
                requiresAdmin: true
            }
        },
        {
            path: '/:pathMatch(.*)*',
            name: 'NotFound',
            component: () => import('@/views/index/NotFound.vue')
        },
        // DEVELOPMENT:
        {
            path: '/ui',
            name: 'UI',
            component: () => import('@/views/.dev/UIKit.vue')
        }
    ]
})

router.beforeEach(async (to, from) => {
    if (to.meta.requiresAdmin) {
        const store = useIndexStore()
        if (!store.isUserLogged)
            return { name: 'NotFound' }

        const hasRight = await store.checkPageAccess()
        if (!hasRight)
            return { name: 'NotFound' }
    }
})

export default router
