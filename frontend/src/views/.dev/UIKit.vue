<template>
    <div class="ui-kit">
        <div class="container">
            <div class="circle-text">
                -15%
            </div>
            <div class="circle-text circle-text--date">
                <span class="circle-text__upper">
                    50
                </span>
                <span class="circle-text__lower">
                    Дней
                </span>
            </div>
            <div class="product-cards">
                <ProductCard v-for="product in products" :key="product.id" :product="product"></ProductCard>
            </div>
            <div class="product-cards">
                <ProductCardDiscount v-for="product in products" :key="product.id" :product="product"></ProductCardDiscount>
            </div>
            <div class="product-cards product-cards--horizontal" v-if="products && products[0]">
                <ProductCardHorizontal v-for="num in 6" :key="products[0].id" :product="products[0]"></ProductCardHorizontal>
            </div>
        </div>
    </div>
</template>

<script>
import ProductCard from '@/components/cards/products/ProductCard.vue'
import ProductCardDiscount from '@/components/cards/products/ProductCardDiscount.vue'
import ProductCardHorizontal from '@/components/cards/products/ProductCardHorizontal.vue'
import { useIndexStore } from '@/stores';

export default {
    name: 'UIKit',
    components: {
    ProductCard,
    ProductCardDiscount,
    ProductCardHorizontal
},
    data() {
        return {
            products: []
        }
    },
    async mounted() {
        this.products = await this.loadProducts()
    },
    methods: {
        async loadProducts() {
            return useIndexStore().loadProducts()
        }
    }
}
</script>

<style></style>