import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/index/HomeView.vue'
import { useIndexStore } from '@/stores'
import { nextTick } from 'vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        // folder: index
        {
            path: '/',
            name: 'Home',
            component: HomeView
        },
        // folder: admin
        {
            path: '/control',
            name: 'Admin',
            component: () => import('@/views/admin/AdminPage.vue'),
            meta: {
                requiresAdmin: true
            },
            children: [
                {
                    path: 'products',
                    name: 'ProductsControl',
                    component: () => import('@/views/admin/ProductsControl.vue')
                }
            ]
        },
        // other
        {
            path: '/:pathMatch(.*)*',
            redirect: { name: 'NotFound' }
        },
        {
            path: '/not-found',
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
        store.currentRoute = to

        const parentName = to.matched[0].name
        const hasRight = await store.checkPageAccess(parentName)
        if (!hasRight)
            return { name: 'NotFound' }
    }
})

export default router
