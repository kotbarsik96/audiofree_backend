import DynamicAdaptive from '@/components/misc/DynamicAdaptive.vue'
import { checkIfFavorite, toggleFavorite } from '@/assets/js/methods.js'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import QuantityInput from '@/components/inputs/QuantityInput.vue'
import SelectProductVariations from '@/components/page/sections/SelectProductVariations.vue'
import { useModalsStore } from '@/stores/modals.js'
import { useNotificationsStore } from '@/stores/notifications.js'
import { useIndexStore } from '@/stores/'
import { h } from 'vue'
import { addToCart } from '@/assets/js/methods.js'
import axios from 'axios'
import { mapState } from 'pinia'

export default {
    components: {
        DynamicAdaptive,
        ConfirmModal,
        SelectProductVariations,
        QuantityInput
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
        },
        async onAddToCartClick() {
            const store = useIndexStore()
            const loadVariations = async () => {
                const link = `${import.meta.env.VITE_PRODUCT_GET_LINK}${this.product.id}`
                const res = await axios.get(link, {
                    params: {
                        onlyOuterData: true
                    }
                })
                if (!Array.isArray(res.data.variations))
                    throw new Error()

                return res.data.variations
            }

            try {
                store.toggleLoading('addToCartCard', true)

                const variations = await loadVariations()
                // создаются компоненты QuantityInput и SelectProductVariations, которые помещаются в компонент ConfirmModal. При нажатии на "В корзину" внутри модального окна, что вызовет confirmProps.callback(), запрос будет отправлен на сервер
                const quantityComp = h(QuantityInput, {
                    class: 'mb-20',
                    name: 'product-popup-quantity',
                    id: 'product-popup-quantity',
                    min: 1,
                    max: 99
                }, () => 'Количество')
                const variationsComp = h(SelectProductVariations, { variations })
                const component = h(ConfirmModal, {
                    title: 'Выберите параметры',
                    nestedComponents: [quantityComp, variationsComp],
                    confirmProps: {
                        text: 'Добавить в корзину',
                        callback: (modalWindowCtx) => {
                            const cart = {
                                quantity: modalWindowCtx.$refs.nestedComponentRef[0].value,
                                variations: modalWindowCtx.$refs.nestedComponentRef[1].values,
                                productName: this.product.name
                            }
                            addToCart(this.product.id, cart)
                        }
                    }
                })
                useModalsStore().addModal({ component })
            } catch (err) {
                useNotificationsStore().addNotification({
                    message: 'Произошла ошибка при попытке добавить товар в корзину',
                    timeout: 3500
                })
            }

            store.toggleLoading('addToCartCard', false)
        }
    },
    computed: {
        ...mapState(useIndexStore, ['favoritesCount']),
        imageSrc() {
            return `${import.meta.env.VITE_LINK}${this.product.image_path}`
        }
    },
    watch: {
        favoritesCount(){
            this.checkIfFavorite()
        }
    },
    mounted() {
        this.checkIfFavorite()
    }
}