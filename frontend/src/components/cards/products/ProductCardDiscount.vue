<template>
    <div class="card product-card product-card--discount">
        <div class="card__container product-card__container" v-if="product">
            <div class="product-card__circle-discount circle-text circle-text--discount">
                {{ discountValue }}
            </div>
            <button class="product-card__expand-top circle-wrapper circle-wrapper--gray"
                :class="{ '__active': isTopExpanded }" type="button" @click="expandTop">
                <PlusIcon></PlusIcon>
            </button>
            <div class="product-card__top" :class="{ '__expanded': isTopExpanded }">
                <div data-prodcard-mobile-buttons></div>
            </div>
            <div class="product-card__image-container">
                <RouterLink :to="{ name: 'Product', params: { productId: product.id } }">
                    <img class="product-card__image" :src="imageSrc" :alt="product.name">
                </RouterLink>
            </div>
            <div class="product-card__star-rating-container">
                <StarRating :stars="5" :rating="product.rating_value || 0" v-model="rating"></StarRating>
            </div>
            <div class="product-card__title">
                <RouterLink class="link" :to="{ name: 'Product', params: { productId: product.id } }">
                    {{ product.name }}
                </RouterLink>
            </div>
            <div class="product-card__description">
                {{ getExcerpt(getDescription(product), { after: '...' }) }}
            </div>
            <div class="product-card__flex">
                <div class="product-card__flex-item">
                    <DynamicAdaptive class="product-card__buttons" destinationSelector="[data-prodcard-mobile-buttons]"
                        query="max-width: 599px">
                        <RouterLink class="button button--colored"
                            :to="{ name: 'Product', params: { productId: product.id } }">
                            Купить
                        </RouterLink>
                    </DynamicAdaptive>
                </div>
                <div class="product-card__flex-item product-card__price">
                    {{ product.current_price ? product.current_price.toLocaleString() : '' }} ₽
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import injectShared from '@/components/inject-shared.js'
import shared from './shared.js'
import StarRating from '@/components/misc/StarRating.vue'
import { getExcerpt } from '@/assets/js/scripts.js'

export default injectShared(shared, {
    name: 'ProductCardDiscount',
    components: {
        StarRating,
    },
    data() {
        return Object.assign({}, shared.data)
    },
    methods: {
        getExcerpt
    },
    computed: {
        discountValue() {
            if (!this.product.discount_price)
                return '-0%';

            const diff = this.product.price - this.product.discount_price
            const diffPercent = diff / (this.product.price / 100)
            return `-${diffPercent}%`
        }
    }
})
</script>