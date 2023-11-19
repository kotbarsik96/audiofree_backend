<template>
    <div class="card product-card product-card--horizontal">
        <div class="card__container product-card__container" v-if="product">
            <div class="product-card__image-container">
                <RouterLink :to="{ name: 'Product', params: { productId: productData.id } }">
                    <ImagePicture class="product-card__image" :obj="productData"></ImagePicture>
                </RouterLink>
            </div>
            <div class="product-card__title">
                <RouterLink class="link" :to="{ name: 'Product', params: { productId: productData.id } }">
                    {{ product.name }}
                </RouterLink>
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
        return Object.assign({}, shared.data)
    }
})
</script>