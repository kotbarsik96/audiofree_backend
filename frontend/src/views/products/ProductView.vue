<template>
    <div class="container">
        <div class="product-page" v-if="product">
            <div class="product-page__main">
                <ProductImagesSlider class="product-page__images-container" :images="gallery"></ProductImagesSlider>
                <div class="product-page__breadcrumbs breadcrumbs">
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Home' }">
                        Главная
                    </RouterLink>
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Catalog' }">
                        Каталог
                    </RouterLink>
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Product', params: { productId: product.id } }">
                        {{ fullname }}
                    </RouterLink>
                </div>
                <DynamicAdaptive class="product-page__vendor-code" query="max-width: 849px"
                    destinationSelector="#vendor-code-mobile">
                    <span>
                        Артикул:
                    </span>
                    <span class="bold">
                        {{ product.id }}
                    </span>
                </DynamicAdaptive>
                <div class="product-page__product">
                    <h1 class="product-page__page-title page-title">
                        {{ fullname }}
                    </h1>
                    <div class="product-page__rating">
                        <div class="product-page__rating-wrapper">
                            <StarRating ref="starRatingComponent" :stars="5" :rating="starRatingValue" interactive
                                @update-rating="setRating">
                            </StarRating>
                            <span v-if="personalRating < 1" class="product-page__rating-value-text">
                                ( {{ product.rating_value || 0 }} из 5 )
                            </span>
                            <span v-else class="product-page__rating-value-text">
                                ( {{ product.rating_value || 0 }} из 5, ваша оценка: {{ personalRating }} )
                            </span>
                        </div>
                        <div id="vendor-code-mobile"></div>
                    </div>
                    <div class="product-page__pricing">
                        <div class="product-page__price-current price-current">
                            {{ product.current_price }} ₽
                        </div>
                        <div class="product-page__price-old price-old" v-if="product.discount_price">
                            {{ product.price }} ₽
                        </div>
                    </div>
                    <div class="product-page__quantity">
                        <QuantityInput v-model="cart.quantity" name="product-quantity" id="product-quantity" :min="1"
                            :max="99">
                            Количество
                        </QuantityInput>
                    </div>
                    <div class="product-page__variations">
                        <div class="product-page__variation-item" v-for="(obj, objIndex) in product.variations"
                            :key="obj.variation.id">
                            <h6 class="product-page__variation-title">
                                {{ obj.variation.name }}
                            </h6>
                            <ul class="product-page__variations-list">
                                <li class="product-page__variation-item" v-for="(varValue, valueIndex) in obj.values"
                                    :key="varValue.id">
                                    <RadioLabel v-model="cart.variations[objIndex].value" ref="variationRadio"
                                        :name="obj.variation.name" :checked="valueIndex === 0" :value="varValue.value">
                                        {{ varValue.value }}
                                    </RadioLabel>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="product-page__buttons">
                        <button class="button button--colored" type="button">
                            Купить в 1 клик
                        </button>
                        <button class="button" type="button">
                            В корзину
                        </button>
                        <button class="button" v-if="personalRating > 0" type="button" @click="removeRating">
                            Убрать оценку
                        </button>
                    </div>
                </div>
                <div class="product-page__info">
                    <div class="product-page__info-buttons">
                        <div class="product-page__notify">
                            <div class="circle-wrapper circle-wrapper--not-interactive warning-circle"></div>
                            <div class="product-page__notify-text">
                                До конца акции осталось:
                                <span class="bold">3 дня</span>
                            </div>
                        </div>
                        <div class="product-page__circle-buttons">
                            <button class="circle-wrapper circle-wrapper--gray" type="button">
                                <HeartIcon></HeartIcon>
                            </button>
                            <RouterLink class="circle-wrapper circle-wrapper--gray" v-if="isAdmin" :to="{ name: 'ProductUpdate', params: { productId: this.product.id } }">
                                <PencilIcon></PencilIcon>
                            </RouterLink>
                        </div>
                    </div>
                    <div class="product-page__delivery-payment">
                        <CheckmarksTable :contents="[
                            { title: 'Доставка', items: ['Санкт-Петербург', 'Ленинградская область', 'Россия'] },
                            { title: 'Варианты оплаты', items: ['Наличными', 'Оплата картой', 'Оплата через СБП'], checkmarkColor: '#FFC107' },
                            { title: 'Наши преимущества', items: ['Гарантия', 'Возврат и обмен', 'Лучшая цена'], checkmarkColor: '#0DB10A' },
                        ]"></CheckmarksTable>
                    </div>
                </div>
            </div>
            <div class="product-page__description">
                <SpoilerTabAdaptive isAccordeon isVertical :content="tabSpoilerContent"></SpoilerTabAdaptive>
            </div>
            <div class="product-page__others" v-if="brandOtherProducts.length > 0">
                <h3 class="page-title">
                    Другие товары бренда
                </h3>
                <div class="product-cards">
                    <ProductCard v-for="product in brandOtherProducts" :product="product"></ProductCard>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ProductImagesSlider from '@/components/sliders/ProductImagesSlider.vue'
import StarRating from '@/components/misc/StarRating.vue'
import QuantityInput from '@/components/inputs/QuantityInput.vue'
import CheckmarksTable from '@/components/tables/CheckmarksTable.vue'
import DynamicAdaptive from '@/components/misc/DynamicAdaptive.vue'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import SpoilerTabAdaptive from '@/components/spoiler-tabs/SpoilerTabAdaptive.vue'
import ProductCard from '@/components/cards/products/ProductCard.vue'
import { useIndexStore } from '@/stores/'
import { useModalsStore } from '@/stores/modals.js'
import { useNotificationsStore } from '@/stores/notifications.js'
import { h } from 'vue'
import { mapState } from 'pinia'
import axios from 'axios'
import { renderEditorDataHTML } from '@/assets/js/editorjs.js'

export default {
    name: 'ProductView',
    components: {
        ProductImagesSlider,
        StarRating,
        QuantityInput,
        CheckmarksTable,
        DynamicAdaptive,
        ConfirmModal,
        SpoilerTabAdaptive,
        ProductCard
    },
    emits: ['updateRouteKey'],
    data() {
        return {
            cart: {
                quantity: '',
                variations: [],
            },
            personalRating: 0,
            product: null,
            brandOtherProducts: []
        }
    },
    computed: {
        ...mapState(useIndexStore, ['isAdmin']),
        fullname() {
            const brand = this.product.brand || ''
            const name = this.product.name || ''
            let fullstring = name
            if (brand)
                fullstring = `${brand} ${name}`

            return fullstring
        },
        gallery() {
            return [this.product.image_path, ...this.product.images.map(obj => obj.path)]
        },
        starRatingValue() {
            return this.personalRating > 0
                ? this.personalRating
                : this.product.rating_value
        },
        infoHTML() {
            if (!Array.isArray(this.product.info))
                return null
            if (this.product.info.length < 1)
                return null

            let string = '<div class="product-info"></div>'
            this.product.info.forEach(obj => {
                string += `
                    <div class="product-info__row">
                        <div class="product-info__name">${obj.name}</div>
                        <div class="product-info__value">${obj.value}</div>
                    </div>
                `
            })

            return string
        },
        descriptionHTML() {
            const description = renderEditorDataHTML(this.product.description)
            if (!description)
                return null

            return `
                <div class="text">
                    ${description}
                </div>
            `
        },
        tabSpoilerContent() {
            const arr = []
            if (this.descriptionHTML)
                arr.push({ button: 'Описание товара', text: this.descriptionHTML, isEdidorJS: true })
            if (this.infoHTML)
                arr.push({ button: 'Характеристики', text: this.infoHTML })

            return arr
        }
    },
    methods: {
        async loadBrandOtherProducts() {
            const link = import.meta.env.VITE_PRODUCTS_GET_LINK

            try {
                const res = await axios.get(link, {
                    params: {
                        except: [this.product.id],
                        brand: this.product.brand,
                        limit: 4
                    }
                })
                if (Array.isArray(res.data.result))
                    this.brandOtherProducts = res.data.result
            } catch (err) { }
        },
        async updateProduct() {
            try {
                const res = await axios.get(`${import.meta.env.VITE_PRODUCT_GET_LINK}${this.product.id}`)
                if (res.data.id)
                    this.product = res.data
            } catch (err) { }
        },
        setCartDefaultVariations() {
            this.cart.variations = this.$route.meta.product.variations.map(obj => {
                if (!obj.values[0] && !obj.values[0].value)
                    return null

                const value = obj.values[0].value
                const name = obj.variation.name
                return { name, value }
            }).filter(v => v)
        },
        async getPersonalRating() {
            const link = `${import.meta.env.VITE_USER_PRODUCT_RATING_LINK}${this.$route.meta.product.id}`

            try {
                const res = await axios.get(link)
                if (!res.data) {
                    this.personalRating = 0
                    return
                }
                if (!res.data.value) {
                    this.personalRating = 0
                    return
                }

                this.personalRating = parseInt(res.data.value)
            } catch (err) {
                this.personalRating = 0
            }
        },
        async setRating(value) {
            value = parseInt(value)
            if (isNaN(value) || value < 1)
                return

            const callback = async () => {
                const link = `${import.meta.env.VITE_RATING_SET_LINK}${this.product.id}/${value}`

                try {
                    await axios.post(link)
                    this.personalRating = value
                    this.updateProduct()
                } catch (err) {
                    useNotificationsStore().addNotification({
                        message: 'Произошла ошибка при попытке поставить оценку'
                    })
                }
            }

            const component = h(
                ConfirmModal,
                {
                    title: `Поставить оценку "${value}" товару ${this.fullname}?`,
                    confirmProps: {
                        text: 'Поставить',
                        callback
                    },
                    declineProps: {
                        callback: () => {
                            if (this.$refs.starRatingComponent)
                                this.$refs.starRatingComponent.setValue(this.product.rating_value)
                        }
                    }
                }
            )
            useModalsStore().addModal({ component })
        },
        removeRating() {
            const callback = async () => {
                const link = `${import.meta.env.VITE_RATING_DELETE_LINK}${this.product.id}`

                try {
                    await axios.delete(link)
                    this.personalRating = 0
                    this.updateProduct()
                } catch (err) {
                    useNotificationsStore().addNotification({
                        message: 'Произошла ошибка при попытке удалить оценку'
                    })
                }
            }

            useModalsStore().addModal({
                component: h(ConfirmModal, {
                    title: `Убрать оценку товара ${this.fullname}? (ваша оценка: "${this.personalRating}")`,
                    confirmProps: {
                        text: 'Убрать',
                        callback,
                    }
                })
            })
        }
    },
    watch: {
        product() {
            this.setCartDefaultVariations()
            this.getPersonalRating()
        }
    },
    mounted() {
        this.product = this.$route.meta.product
        this.loadBrandOtherProducts()
    },
    beforeRouteUpdate() {
        this.$emit('updateRouteKey')
    }
}
</script>

<style lang="scss">
.product-page {
    padding: 65px 0 100px;

    &__main {
        display: grid;
        grid-template-columns: 470px 1fr 270px;
        grid-template-rows: 25px auto;
        grid-gap: 10px 30px;
        padding-bottom: 75px;
        margin-bottom: 70px;
        border-bottom: 1px solid #ececec;
    }

    &__images-container {
        grid-column: 1 / 2;
        grid-row: 1 / -1;
    }

    &__breadcrumbs {
        grid-column: 2 / 3;
    }

    &__vendor-code {
        grid-column: 3 / -1;
        display: flex;
        justify-content: flex-end;

        span {
            display: inline-block;
        }

        span:first-child {
            margin-right: 5px;
        }
    }

    &__page-title {
        font-size: 24px;
        line-height: 35px;
        margin-bottom: 20px;
    }

    &__product {
        grid-column: 2 / 3;
        grid-row: 2 / -1;
    }

    &__rating {
        margin-bottom: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    &__wrapper {
        display: flex;
        align-items: center;
    }

    &__rating-value-text {
        margin-left: 11px;
        font-weight: 500;
        font-size: 14px;
        line-height: 16px;
    }

    &__pricing {
        margin-bottom: 35px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;

        .price-old {
            margin-left: 10px;

        }
    }

    &__quantity {
        margin-bottom: 20px;
    }

    &__variation-title {
        font-size: 14px;
        line-height: 16px;
        margin-bottom: 10px;
        font-weight: 500;
    }

    &__variations-list {
        display: flex;
        flex-wrap: wrap;
    }

    &__variation-item {
        margin-right: 15px;
        margin-bottom: 10px;
    }

    &__buttons {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;

        .button {
            font-size: 13px;
            margin-right: 20px;
            margin-bottom: 15px;
            min-width: 140px;
            padding: 15px 40px;
        }
    }

    &__info {
        grid-column: 3 / -1;
    }

    &__info-buttons {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    &__notify {
        display: flex;
        align-items: center;

        .circle-wrapper {
            flex: 0 0 auto;
        }
    }

    &__notify-text {
        margin-left: 6px;
    }

    &__circle-buttons {
        display: flex;
        margin-left: 60px;

        .circle-wrapper {
            margin-right: 11px;

            &:last-child {
                margin-right: 0;
            }
        }
    }

    &__others {
        margin-top: 95px;

        .page-title {
            margin-bottom: 45px;
        }
    }

    @media (max-width: 1099px) {
        &__main {
            display: grid;
            grid-template-columns: 470px 1fr;
            grid-template-rows: 20px 15px repeat(auto-fit, minmax(0, auto));
            grid-gap: 10px 30px;
        }

        &__breadcrumbs {
            margin-bottom: 0;
        }

        &__vendor-code {
            grid-column: 2 / -1;
            grid-row: 2 / 3;
            justify-content: flex-start;
        }

        &__product {
            grid-column: 2 / -1;
            grid-row: 3 / -1;
        }

        &__info {
            grid-column: 1 / 2;
            grid-row: 4 / -1;
            margin-top: 20px;
        }

        &__buttons {
            .button {
                margin-right: 10px;
            }
        }
    }

    @media (max-width: 992px) {
        padding: 30px 0 40px;
    }

    @media (max-width: 849px) {
        &__main {
            display: flex;
            flex-wrap: wrap;
        }

        &__main>* {
            flex: 0 0 100%;
        }

        &__breadcrumbs {
            order: 1;
        }

        &__images-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            order: 2;
            width: 100%;
        }

        &__product {
            order: 3;
        }

        &__info {
            order: 4;
        }

        &__rating {
            margin-bottom: 20px;
        }

        &__pricing {
            margin-bottom: 25px;
        }
    }
}
</style>