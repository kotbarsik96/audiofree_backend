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
                    path: 'products/:pageNumber?',
                    name: 'ProductsControl',
                    component: () => import('@/views/admin/products/ProductsControl.vue'),
                    meta: {
                        hasPageNumber: true
                    }
                },
                {
                    path: 'product/create',
                    name: 'ProductCreate',
                    component: () => import('@/views/admin/products/ProductCreate.vue')
                },
                {
                    path: 'product/update/:productId',
                    name: 'ProductUpdate',
                    component: () => import('@/views/admin/products/ProductCreate.vue')
                },
                {
                    path: 'users/:pageNumber?',
                    name: 'UsersControl',
                    component: () => import('@/views/admin/users/UsersControl.vue'),
                    meta: {
                        hasPageNumber: true
                    }
                },  
                {
                    path: 'role/:pageNumber?',
                    name: 'RolesControl',
                    component: () => import('@/views/admin/roles/RolesControl.vue'),
                    meta: {
                        hasPageNumber: true
                    }
                },
                {
                    path: 'taxonomies-control/:taxonomyName/:pageNumber?',
                    name: 'TaxonomiesControl',
                    component: () => import('@/views/admin/taxonomies/TaxonomiesControl.vue'),
                    meta: {
                        hasPageNumber: true
                    }
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

    if (to.meta.hasPageNumber) {
        if (!to.params.pageNumber) {
            to.params.pageNumber = 1
            return { name: to.name, params: to.params, meta: to.meta }
        }
    }

    if(to.name === 'TaxonomiesControl') {
        const taxonomies = [
            'brand',
            'type',
            'category',
            'product_status'
        ]
        if(!taxonomies.includes(to.params.taxonomyName))
            return { name: 'NotFound' }
    }
})

export default router
