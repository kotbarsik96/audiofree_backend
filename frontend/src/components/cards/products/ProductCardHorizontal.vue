<template>
    <div class="card product-card product-card--horizontal">
        <div class="card__container product-card__container">
            <div class="product-card__image-container">
                <img class="product-card__image" :src="imageSrc" :alt="product.name">
            </div>
            <div class="product-card__title">
                {{ product.name }}
            </div>
            <div class="product-card__star-rating-container">
                <StarRating :stars="5" :rating="product.rating_value || 0" v-model="rating"></StarRating>
            </div>
            <div class="product-card__flex-item product-card__price">
                {{ product.current_price ? product.current_price.toLocaleString() : '' }} ₽
            </div>
        </div>
    </div>
</template>

<script>
import injectShared from '@/components/inject-shared.js'
import shared from './shared.js'
import StarRating from '@/components/misc/StarRating.vue'

export default injectShared(shared, {
    name: 'ProductCardHorizontal',
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