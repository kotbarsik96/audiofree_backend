import { createRouter, createWebHistory } from "vue-router";
import HomePage from "@/views/index/HomePage.vue";

const routes = [
    // folder: "index"
    { path: '/not-found', name: 'not-found', component: () => import("@/views/index/NotFound.vue") },
    { path: '/', name: 'home', component: HomePage },
    { path: '/guarantees', name: 'guarantees', component: () => import("@/views/index/GuaranteesPage.vue") },
    { path: '/delivery-payment', name: 'delivery-payment', component: () => import("@/views/index/DeliveryPaymentPage") },
    { path: '/contacts', name: 'contacts', component: () => import("@/views/index/ContactsPage.vue") },
    {
        path: '/catalogue',
        redirect: { name: 'catalogue-list', params: { pageNumber: 1 } },
        name: 'catalogue',
        component: () => import("@/views/index/CataloguePage.vue"),
        children: [
            {
                path: 'page=:pageNumber(\\d+)+',
                name: 'catalogue-list',
                component: () => import("@/views/index/CatalogueListPage.vue")
            },
            { path: 'page=:pageNumber(\\D*)', redirect: { name: 'catalogue-list', params: { pageNumber: 1 } } },
            { path: ':pathValue(.*)*', redirect: { name: 'catalogue-list', params: { pageNumber: 1 } } },
        ]
    },
    { path: '/:pathValue(.*)*', redirect: { name: '/not-found' } },

    // folder: "admin"
    { path: "/admin", name: "admin-panel", component: () => import("@/views/admin/AdminPage.vue") },

    // folder: "products"
    { path: '/cart', name: 'cart', component: () => import("@/views/products/CartPage.vue") },
    { path: '/cart-oneclick', name: 'cart-oneclick', component: () => import("@/views/products/CartPage.vue") },
    { path: '/favorites', name: 'favorites', component: () => import("@/views/products/FavoritesPage.vue") },
    { path: '/order', name: 'order', component: () => import("@/views/products/OrderPage.vue") },
    { path: '/order-oneclick', name: 'order-oneclick', component: () => import("@/views/products/OrderPage.vue") },
    {
        path: '/products/:vendorCode',
        name: 'product',
        props: route => ({ vendorCode: route.params.vendorCode }),
        component: () => import("@/views/products/ProductPage.vue"),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        // если to.name и from.name совпадают с строками из списка, прокрутка не происходит
        const exceptionCompNames = ["catalogue", "catalogue-list"];

        return savedPosition || new Promise(resolve => {
            exceptionCompNames.forEach(name => {
                if (to.name === name && from.name === name) resolve();
            });

            setTimeout(() => {
                resolve({ top: 0, behavior: "smooth" })
            }, 1000);
        });
    }
});

export default router;
