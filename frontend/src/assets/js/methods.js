/* здесь содержатся методы, которые используются в component.methods либо напрямую, либо внутри методов */

import { isNumeric } from './scripts.js'
import { useIndexStore } from '@/stores/'
import { useNotificationsStore } from '@/stores/notifications.js'
import { useModalsStore } from '@/stores/modals.js'
import AuthModal from '@/components/modals/auth/AuthModal.vue'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import axios from 'axios'
import { h } from 'vue'

/* требуется, чтобы в data был прописан объект matchMediaMatches такого вида:
    matchMediaMatches: {
        max: {
            '992': false
        },
        min: {
            '993': false
        }
    }
А в mounted() прописать this.setMatchMedia()
будет автоматически выставлять window.matchMedia('(max-width: 992px)').matches
*/
export function setMatchMedia() {
    function forEachCallback(mediaValue, type) {
        const onChange = () => {
            this.matchMediaMatches[type][mediaValue] = mm.matches
        }

        if (!isNumeric(mediaValue))
            return

        const mm = window.matchMedia(`(${type}-width: ${mediaValue}px)`)
        mm.addEventListener('change', onChange)
        onChange()
    }

    forEachCallback = forEachCallback.bind(this)
    const max = this.matchMediaMatches.max
    const min = this.matchMediaMatches.min
    if (max) {
        Object.keys(max)
            .forEach(mediaValue => forEachCallback(mediaValue, 'max'))
    }
    if (min) {
        Object.keys(this.matchMediaMatches.min)
            .forEach(mediaValue => forEachCallback(mediaValue, 'min'))
    }
}

export function openAuthModal(defaultType = 'register') {
    const store = useModalsStore()
    const component = h(AuthModal, { defaultType })
    store.addModal({ component })
}

export function openConfirmLogoutModal() {
    const store = useModalsStore()
    const component = h(ConfirmModal, {
        title: 'Вы уверены, что хотите выйти из профиля?',
        confirmProps: {
            text: 'Выйти',
            callback: logout
        },
        declineProps: {
            text: 'Остаться'
        }
    })
    store.addModal({ component })
}

export function logout() {
    useIndexStore().logout()
}

export function getDate(dateString) {
    if (!dateString)
        return null

    const date = new Date(dateString)
    if (date && typeof date.toLocaleString === 'function')
        return `${date.toLocaleString()}`

    return null
}

// требует isInFavorites: null в data()
export async function checkIfFavorite() {
    const product = this.product
    if (!product)
        return

    const store = useIndexStore()
    await store.onLoadingEnd('loadEntity_favorites')

    const id = product.id
    this.isInFavorites = store.favorites.includes(id)
}

// требует isInFavorites: null в data()
export function toggleFavorite() {
    const store = useIndexStore()

    const add = async () => {
        const link = `${import.meta.env.VITE_USER_FAVORITE}${this.product.id}`

        try {
            const res = await axios.post(link)
            if (Array.isArray(res.data.favorites))
                store.favorites = res.data.favorites.map(obj => obj.product_id)
            this.isInFavorites = true
        } catch (err) {
            this.isInFavorites = false
        }
    }
    const remove = async () => {
        const link = `${import.meta.env.VITE_USER_FAVORITE}${this.product.id}`

        try {
            const res = await axios.delete(link)
            if (Array.isArray(res.data.favorites))
                store.favorites = res.data.favorites.map(obj => obj.product_id)
            this.isInFavorites = false
        } catch (err) {
            this.isInFavorites = false
        }
    }

    if (this.isInFavorites === null)
        return

    this.isInFavorites ? remove() : add()
    this.isInFavorites = null
}

/* если "в 1 клик", нужно указать cart.isOneClick === true */
export async function addToCart(productId, cart = {}) {
    const store = useIndexStore()
    store.toggleLoading('addToCart', true)

    try {
        const link = `${import.meta.env.VITE_USER_CART}${productId}`
        const res = await axios.post(link, cart)
        if (!res.data.success)
            throw new Error()

        if (Array.isArray(res.data.cart))
            store.cart = res.data.cart

        let message = cart.productName
            ? `Товар ${cart.productName} добавлен в корзину`
            : 'Товар добавлен в корзину'
        if (cart.quantity > 1)
            message += ` (${cart.quantity} шт.)`
        useNotificationsStore().addNotification({ message })
    } catch (err) {
        useNotificationsStore()
            .addNotification({ message: 'Произошла ошибка при попытке добавить товар в корзину' })
    }

    store.toggleLoading('addToCart', false)
}

export function getImagePath(pathOrObject, extension = null) {
    if (!pathOrObject)
        return '#'

    let path = pathOrObject
    if (typeof pathOrObject === 'object') {
        path = pathOrObject.image_path || pathOrObject.path
        if (!extension)
            extension = pathOrObject.image_extension || pathOrObject.extension
    }
    if (!path || !extension)
        return '#'

    return `${import.meta.env.VITE_LINK}${path}.${extension}`
}

export function capitalizeFirstLetter(string) {
    return string.split('')
        .map((letter, i) => i === 0 ? letter.toUpperCase() : letter)
        .join('')
}