import { defineStore } from 'pinia'
import axios from 'axios'
import { useNotificationsStore } from '@/stores/notifications.js'

axios.defaults.withCredentials = true

export const useIndexStore = defineStore('index', {
    state: () => {
        return {
            isUserLogged: false,
            role: 999,
            products: [],
            currentRoute: null
        }
    },
    actions: {
        async getCsrfToken() {
            await axios.get(import.meta.env.VITE_TOKEN_LINK, { withCredentials: true })
            return true
        },
        async checkAuth() {
            const res = await axios(import.meta.env.VITE_AUTH_CHECK_LINK)
            if (res.data.error || !res.data.success) {
                this.role = 999
                return false
            }

            this.isUserLogged = true

            if (res.data.role)
                this.role = res.data.role

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

            const options = Object.assign(opts.filters, { limit: opts.limit, offset: opts.offset })
            try {
                const res = await axios.get(import.meta.env.VITE_PRODUCTS_GET_LINK, options)
                return res.data
            } catch (err) {
                throw err
            }
        }
    },
    getters: {
        isAdmin: (state) => state.role <= 2
    }
})