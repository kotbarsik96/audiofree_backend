<template>
    <div class="card product-card">
        <div class="card__container product-card__container">
            <button class="product-card__expand-top circle-wrapper" :class="{ '__active': isTopExpanded }" type="button"
                @click="expandTop">
                <PlusIcon></PlusIcon>
            </button>
            <div class="product-card__top" :class="{ '__expanded': isTopExpanded }">
                <InStockPlaceholder class="product-card__stock" type="in-stock"></InStockPlaceholder>
                <div class="product-card__circle-buttons">
                    <button class="circle-wrapper circle-wrapper--shadow circle-wrapper--gray" type="button">
                        <HeartIcon></HeartIcon>
                    </button>
                </div>
                <div data-prodcard-mobile-buttons></div>
            </div>
            <div class="product-card__image-container">
                <RouterLink class="link" :to="{ name: 'Product', params: { productId: product.id } }">
                    <img class="product-card__image" :src="imageSrc" :alt="product.name">
                </RouterLink>
            </div>
            <div class="product-card__title">
                <RouterLink class="link" :to="{ name: 'Product', params: { productId: product.id } }">
                    {{ product.name }}
                </RouterLink>
            </div>
            <div class="product-card__flex">
                <div class="product-card__flex-item">
                    <StarRating :stars="5" :rating="product.rating_value || 0" v-model="rating"></StarRating>
                </div>
                <div class="product-card__flex-item product-card__price">
                    {{ product.current_price ? product.current_price.toLocaleString() : '' }} ₽
                </div>
            </div>
            <DynamicAdaptive query="max-width: 599px" destinationSelector="[data-prodcard-mobile-buttons]"
                :maxParentsCount="1">
                <div class="product-card__buttons">
                    <button class="button button--colored" type="button">
                        Купить в 1 клик
                    </button>
                    <button class="button" type="button">
                        В корзину
                    </button>
                </div>
            </DynamicAdaptive>
        </div>
    </div>
</template>

<script>
import injectShared from '@/components/inject-shared.js'
import shared from './shared.js'
import InStockPlaceholder from '@/components/misc/InStockPlaceholder.vue'
import StarRating from '@/components/misc/StarRating.vue'

export default injectShared(shared, {
    name: 'ProductCard',
    components: {
        InStockPlaceholder,
        StarRating,
    },
    data() {
        return Object.assign({
            rating: 0
        }, shared.data)
    }
})
</script>