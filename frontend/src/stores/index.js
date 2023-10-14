import { defineStore } from 'pinia'
import axios from 'axios'

axios.defaults.withCredentials = true

export const useIndexStore = defineStore('index', {
    state: () => {
        return {
            isUserLogged: false
        }
    },
    actions: {
        async checkAuth() {
            const res = await axios(import.meta.env.VITE_AUTH_CHECK_LINK)
            if (res.data.error || !res.data.success)
                return

            this.isUserLogged = true
        },
        async register(data) {
            try {
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
                return res
            } catch (err) {
                throw err
            }
        }
    }
})