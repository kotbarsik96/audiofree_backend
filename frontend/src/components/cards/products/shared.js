import DynamicAdaptive from '@/components/misc/DynamicAdaptive.vue'
import { checkIfFavorite, toggleFavorite } from '@/assets/js/methods.js'

export default {
    components: {
        DynamicAdaptive
    },
    props: {
        product: {
            type: Object,
            required: true
        }
    },
    data: {
        isTopExpanded: false,
        isInFavorites: null
    },
    methods: {
        checkIfFavorite,
        toggleFavorite,
        expandTop() {
            this.isTopExpanded = !this.isTopExpanded
        }
    },
    computed: {
        imageSrc() {
            return `${import.meta.env.VITE_LINK}${this.product.image_path}`
        }
    },
    mounted() {
        this.checkIfFavorite()
    }
}