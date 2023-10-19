import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/index/HomeView.vue'
import { useIndexStore } from '@/stores'

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
                    component: () => import('@/views/admin/products/ProductsControl.vue')
                },
                {
                    path: 'product/create',
                    name: 'ProductCreate',
                    component: () => import('@/views/admin/products/ProductCreate.vue')
                },
                {
                    path: 'taxonomy/create/:taxonomyName',
                    name: 'TaxonomyCreate',
                    component: () => import('@/views/admin/taxonomies/TaxonomyCreate.vue')
                },
                {
                    path: 'taxonomies-control/:taxonomyName',
                    name: 'TaxonomiesControl',
                    component: () => import('@/views/admin/taxonomies/TaxonomiesControl.vue')
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
