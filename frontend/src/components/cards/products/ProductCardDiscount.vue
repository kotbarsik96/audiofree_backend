<template>
    <div class="card product-card product-card--discount">
        <div class="card__container product-card__container">
            <div class="product-card__circle-discount circle-text circle-text--discount">
                -15%
            </div>
            <button class="product-card__expand-top circle-wrapper" :class="{ '__active': isTopExpanded }" type="button" @click="expandTop">
                <PlusIcon></PlusIcon>
            </button>
            <div class="product-card__top" :class="{ '__expanded': isTopExpanded }">
                <div data-prodcard-mobile-buttons></div>
            </div>
            <div class="product-card__image-container">
                <img class="product-card__image" :src="imageSrc" :alt="product.name">
            </div>
            <div class="product-card__star-rating-container">
                <StarRating :stars="5" :rating="product.rating_value || 0" v-model="rating"></StarRating>
            </div>
            <div class="product-card__title">
                {{ product.name }}
            </div>
            <div v-if="product.description" class="product-card__description">
                {{ product.description }}
            </div>
            <div class="product-card__flex">
                <div class="product-card__flex-item">
                    <DynamicAdaptive class="product-card__buttons" destinationSelector="[data-prodcard-mobile-buttons]" query="max-width: 599px">
                        <button class="button button--colored" type="button">
                            Купить
                        </button>
                    </DynamicAdaptive>
                </div>
                <div class="product-card__flex-item product-card__price">
                    {{ product.price ? product.price.toLocaleString() : '' }} ₽
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import injectShared from '@/components/inject-shared.js'
import shared from './shared.js'
import StarRating from '@/components/misc/StarRating.vue'

export default injectShared(shared, {
    name: 'ProductCardDiscount',
    components: {
        StarRating,
    },
    data() {
        return Object.assign({
            rating: 0
        }, shared.data)
    }
})
</script>