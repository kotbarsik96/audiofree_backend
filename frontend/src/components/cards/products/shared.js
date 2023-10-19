import DynamicAdaptive from '@/components/misc/DynamicAdaptive.vue'

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
        isTopExpanded: false
    },
    methods: {
        expandTop() {
            this.isTopExpanded = !this.isTopExpanded
        }
    },
    computed: {
        imageSrc() {
            return `${import.meta.env.VITE_LINK}${this.product.image_path}`
        }
    }
}