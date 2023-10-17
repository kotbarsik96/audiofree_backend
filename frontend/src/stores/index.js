import { defineStore } from 'pinia'
import axios from 'axios'
import { useNotificationsStore } from '@/stores/notifications.js'

axios.defaults.withCredentials = true

export const useIndexStore = defineStore('index', {
    state: () => {
        return {
            isUserLogged: false,
            products: []
        }
    },
    actions: {
        async getCsrfToken() {
            await axios.get(import.meta.env.VITE_TOKEN_LINK, { withCredentials: true })
            return true
        },
        async checkAuth() {
            const res = await axios(import.meta.env.VITE_AUTH_CHECK_LINK)
            if (res.data.error || !res.data.success)
                return

            this.isUserLogged = true
        },
        async checkPageAccess(page) {
            const res = await axios(import.meta.env.VITE_ROLE_CHECK_PAGE_ACCESS_LINK, {
                page
            })
            if (res.data.error || !res.data.success)
                return false

            return true
        },
        async register(data) {
            try {
                await this.getCsrfToken()

                const res = await axios.post(import.meta.env.VITE_AUTH_REGISTER_LINK, data)
                if (res.data.success)
                    this.isUserLogged = true
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
    }
})