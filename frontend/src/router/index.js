import { createRouter, createWebHistory } from 'vue-router'
import { useIndexStore } from '@/stores'
import axios from 'axios'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        // folder: index
        {
            path: '/',
            name: 'Home',
            component: () => import('@/views/index/HomePage.vue')
        },
        {
            path: '/delivery-payment',
            name: 'DeliveryPayment',
            component: () => import('@/views/index/DeliveryPaymentPage.vue')
        },
        {
            path: '/warranty',
            name: 'Warranty',
            component: () => import('@/views/index/WarrantyPage.vue')
        },
        {
            path: '/contacts',
            name: 'Contacts',
            component: () => import('@/views/index/ContactsPage.vue')
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
                    path: 'taxonomies-control/:taxonomyType/:pageNumber?',
                    name: 'TaxonomiesControl',
                    component: () => import('@/views/admin/taxonomies/TaxonomiesControl.vue'),
                    meta: {
                        hasPageNumber: true
                    }
                },
                {
                    path: 'gallery-control/:pageNumber?',
                    name: 'GalleryControl',
                    component: () => import('@/views/admin/gallery/GalleryControl.vue'),
                    meta: {
                        hasPageNumber: true
                    }
                }
            ]
        },
        // folder: user
        {
            path: '/account',
            name: 'Account',
            component: () => import('@/views/user/AccountView.vue'),
            meta: {
                requiresAuth: true
            },
            children: [
                {
                    path: 'info',
                    name: 'AccountInfo',
                    component: () => import('@/views/user/AccountInfo.vue')
                },
                {
                    path: 'change-password',
                    name: 'ChangePassword',
                    component: () => import('@/views/user/ChangePassword.vue')
                },
                {
                    path: 'email-verification',
                    name: 'EmailVerification',
                    component: () => import('@/views/user/EmailVerification.vue')
                }
            ]
        },
        // folder: products
        {
            path: '/catalog/:pageNumber?',
            name: 'Catalog',
            component: () => import('@/views/products/CatalogView.vue'),
            meta: {
                hasPageNumber: true
            }
        },
        {
            path: '/product/:productId',
            name: 'Product',
            component: () => import('@/views/products/ProductView.vue'),
        },
        {
            path: '/cart',
            name: 'Cart',
            component: () => import('@/views/products/CartView.vue'),
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/cart/one-click',
            name: 'CartOneClick',
            component: () => import('@/views/products/CartView.vue'),
            meta: {
                requiresAuth: true
            }
        },
        {
            path: '/favorites/:pageNumber?',
            name: 'Favorites',
            component: () => import('@/views/products/FavoritesView.vue'),
            meta: {
                requiresAuth: true,
                hasPageNumber: true
            }
        },
        {
            path: '/order/:id',
            name: 'Order',
            component: () => import('@/views/products/OrderView.vue'),
            meta: {
                requiresAuth: true
            }
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
    ],
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition)
            return savedPosition

        return {
            top: 0,
            behavior: 'smooth'
        }
    }
})

export let currentRoute = null

router.beforeEach(async (to) => {
    const store = useIndexStore()
    currentRoute = to

    if (to.meta.requiresAdmin) {
        const parentName = to.matched[0].name
        const hasRight = await store.checkPageAccess(parentName)
        if (!hasRight)
            return { name: 'NotFound' }
    }

    if (to.meta.requiresAuth && !store.isUserLogged) {
        const authChecking = () => {
            return new Promise(resolve => {
                document.addEventListener('auth-checked', resolve)
            })
        }

        if (store.isCheckingAuth) {
            await authChecking()
            if (!store.isUserLogged)
                return { name: 'NotFound' }
        } else {
            await store.checkAuth()
            if (!store.isUserLogged)
                return { name: 'NotFound' }
        }
    }

    if (to.meta.hasPageNumber) {
        if (!to.params.pageNumber) {
            to.params.pageNumber = 1
            return { name: to.name, params: to.params, meta: to.meta }
        }
    }

    if (to.name === 'TaxonomiesControl') {
        const taxonomies = [
            'brand',
            'type',
            'category',
            'product_status'
        ]
        if (!taxonomies.includes(to.params.taxonomyType))
            return { name: 'NotFound' }
    }

    if (to.name === 'Account') {
        return { name: 'AccountInfo' }
    }

    if (to.name === 'Product') {
        const store = useIndexStore()
        store.toggleLoading('loadProductPage', true)
        const link = `${import.meta.env.VITE_PRODUCT_GET_LINK}${to.params.productId}`

        try {
            const res = await axios.get(link)
            if (!res.data.id)
                throw new Error()

            to.meta.product = res.data
        } catch (err) {
            store.toggleLoading('loadProductPage', false)
            return { name: 'NotFound' }
        }

        store.toggleLoading('loadProductPage', false)
    }

    if (to.name === 'Order') {
        const id = to.params.id
        store.toggleLoading('orderPageAuth', true)

        try {
            const link = `${import.meta.env.VITE_ORDER_LINK}${id}`
            const res = await axios.get(link)

            if (res.data.id && res.data.status === 'waiting_userdata') {
                to.meta.orderData = res.data
            } else {
                throw new Error()
            }
        } catch (err) {
            store.toggleLoading('orderPageAuth', false)
            return { name: 'NotFound' }
        }

        store.toggleLoading('orderPageAuth', false)
    }
})

export default router
