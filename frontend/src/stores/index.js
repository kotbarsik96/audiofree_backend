import { defineStore } from 'pinia'
import axios from 'axios'
import { useNotificationsStore } from '@/stores/notifications.js'

axios.defaults.withCredentials = true

export const useIndexStore = defineStore('index', {
    state: () => {
        return {
            isUserLogged: false,
            loadings: [],
            role: 999,
            emailVerified: false,
            products: [],
            currentRoute: null,
            cart: [],
            favorites: []
        }
    },
    actions: {
        async getCsrfToken() {
            await axios.get(import.meta.env.VITE_TOKEN_LINK, { withCredentials: true })
            return true
        },
        async checkAuth() {
            if (this.isCheckingAuth)
                return

            this.toggleLoading('checkAuth', true)
            const onAny = () => {
                this.loadEntity('cart')
                this.loadEntity('favorites')
            }
            const onFalse = () => {
                this.role = 999
                this.isCheckingAuth = false
                document.dispatchEvent(new CustomEvent('auth-checked'))
                this.toggleLoading('checkAuth', false)
            }
            const onTrue = () => {
                this.isUserLogged = true

                this.role = res.data.role || 999
                this.emailVerified = res.data.email_verified

                this.isCheckingAuth = false
                document.dispatchEvent(new CustomEvent('auth-checked'))
                this.toggleLoading('checkAuth', false)
                onAny()
            }

            this.isCheckingAuth = true

            const res = await axios(import.meta.env.VITE_AUTH_CHECK_LINK)
            if (res.data.error || !res.data.success) {
                onFalse()
                return false
            }

            onTrue()
            return true
        },
        async checkPageAccess(page) {
            try {
                const res = await axios.get(import.meta.env.VITE_ROLE_CHECK_PAGE_ACCESS_LINK, {
                    params: {
                        page,
                        noError: true
                    }
                })
                if (!res.data.success || res.data.error)
                    return false

                return true
            } catch (err) {
                return false
            }
        },
        async register(data) {
            try {
                await this.getCsrfToken()

                const res = await axios.post(import.meta.env.VITE_AUTH_REGISTER_LINK, data)
                if (res.data.success)
                    this.isUserLogged = true

                this.checkAuth()
                return res.data
            } catch (err) {
                throw err
            }
        },
        async login(data) {
            try {
                await this.getCsrfToken()

                const res = await axios.post(import.meta.env.VITE_AUTH_LOGIN_LINK, data)
                if (res.data.success)
                    this.isUserLogged = true

                this.checkAuth()

                return res.data
            } catch (err) {
                throw err
            }
        },
        async logout() {
            try {
                const res = await axios.post(import.meta.env.VITE_AUTH_LOGOUT_LINK)
                this.isUserLogged = false
                useNotificationsStore().addNotification({
                    message: res.data.message
                })
                this.checkAuth()
                return res
            } catch (err) {
                throw err
            }
        },
        async loadProducts(opts = { limit: 4, offset: 0, filters: {} }) {
            if (!opts.limit)
                opts.limit = 4

            this.toggleLoading('loadProducts', true)

            const options = Object.assign(opts.filters, { limit: opts.limit, offset: opts.offset })
            try {
                const res = await axios.get(import.meta.env.VITE_PRODUCTS_GET_LINK, options)
                this.toggleLoading('loadProducts', false)
                return res.data
            } catch (err) {
                this.toggleLoading('loadProducts', false)
                throw err
            }
        },
        async loadTaxonomies(taxonomiesObj = {}, showError = false) {
            useIndexStore().toggleLoading('loadTaxonomies', true)

            try {
                const res = await axios.get(import.meta.env.VITE_TAXONOMIES_GET_LINK)
                for (let key in taxonomiesObj) {
                    if (!Array.isArray(res.data[key]))
                        continue

                    taxonomiesObj[key] = res.data[key].map(obj => obj.name)
                }
            } catch (err) {
                if (showError) {
                    useNotificationsStore().addNotification({
                        message: 'Произошла ошибка при загрузке таксономий',
                        timeout: 5000
                    })
                }
            }

            useIndexStore().toggleLoading('loadTaxonomies', false)
        },
        /* добавляет в массив store.loadings загрузку loadingName. loadingName - просто строка, может быть любая. Если массив не пустой, будет показан экран загрузки */
        toggleLoading(loadingName, adding = false) {
            if (adding)
                this.loadings.push(loadingName)
            else {
                const index = this.loadings.findIndex(n => loadingName === n)
                if (index >= 0)
                    this.loadings.splice(index, 1)

                document.dispatchEvent(new CustomEvent('loading-end'))
            }
        },
        /* принимает loadingName из store.loadings, и, когда в store.loadings закончатся элементы с loadingName, зарезолвит промис */
        onLoadingEnd(loadingName) {
            return new Promise(resolve => {
                const callback = () => {
                    if (!this.loadings.includes(loadingName)) {
                        document.removeEventListener('loading-end', callback)
                        resolve()
                    }
                }

                callback()
                document.addEventListener('loading-end', callback)
            })
        },
        /* для загрузки корзины, избранного */
        async loadEntity(entityName) {
            if (!this.isUserLogged)
                return

            this.toggleLoading(`loadEntity_${entityName}`, true)
            let link
            switch (entityName) {
                case 'cart':
                    link = import.meta.env.VITE_USER_CART
                    break
                case 'favorites':
                    link = import.meta.env.VITE_USER_FAVORITE
                    break
            }

            try {
                if (!link)
                    throw new Error('')

                const res = await axios.get(link)
                if (Array.isArray(res.data)) {
                    switch (entityName) {
                        case 'cart':
                            this[entityName] = res.data
                            break
                        case 'favorites':
                            this[entityName] = res.data.map(obj => obj.product_id)
                            break
                    }
                }
            } catch (err) {
                this[entityName] = []
            }

            this.toggleLoading(`loadEntity_${entityName}`, false)
        }
    },
    getters: {
        isAdmin: (state) => state.role <= 2,
        isPageLoading: (state) => state.loadings.length > 0,
        favoritesCount: (state) => state.favorites.length,
        cartCount: (state) => state.cart.length,
    }
})