import { isNumeric } from './scripts.js'
import axios from 'axios'

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
    const product = this.product || this.productData
    const link = `${import.meta.env.VITE_IS_USER_FAVORITE}${product.id}`
    try {
        const res = await axios.get(link)
        this.isInFavorites = Boolean(res.data)
    } catch (err) {}
}

// требует isInFavorites: null в data()
export function toggleFavorite() {
    const add = async () => {
        const link = `${import.meta.env.VITE_ADD_USER_FAVORITE}${this.product.id}`

        try {
            await axios.post(link)
            this.isInFavorites = true
        } catch (err) {
            this.isInFavorites = false
        }
    }
    const remove = async () => {
        const link = `${import.meta.env.VITE_DELETE_USER_FAVORITE}${this.product.id}`

        try {
            await axios.delete(link)
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