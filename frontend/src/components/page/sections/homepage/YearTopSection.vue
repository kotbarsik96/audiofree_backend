<template>
    <section class="section year-top-section">
        <div class="container">
            <h3 class="section-title section-title--centered">
                <div>
                    ТОП-2023
                </div>
                <div class="section-title__highlighted">
                    лидеры предзаказов, обзоров и рекомендаций
                </div>
            </h3>
            <div class="product-cards product-cards--horizontals">
                <div class="product-cards__wrapper">
                    <ProductCardHorizontal v-for="product in bestsellers" :productData="product"></ProductCardHorizontal>
                </div>
                <div class="product-cards__swipe circle-wrapper circle-wrapper--not-interactive">
                    <SwipeIcon></SwipeIcon>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import ProductCardHorizontal from '@/components/cards/products/ProductCardHorizontal.vue'
import axios from 'axios'

export default {
    name: 'YearTopSection',
    components: {
        ProductCardHorizontal
    },
    data() {
        return {
            bestsellers: [],
            limit: 9
        }
    },
    methods: {
        async loadBestellers() {
            const link = import.meta.env.VITE_PRODUCTS_GET_LINK

            try {
                const res = await axios.get(link, {
                    params: {
                        bestsellers_first: true,
                        limit: this.limit
                    }
                })
                
                if (Array.isArray(res.data.result)) {
                    this.bestsellers = res.data.result
                } else
                    throw new Error()
            } catch (err) { 
            }
        }
    },
    created() {
        this.loadBestellers()
    }
}
</script>

<style lang="scss">
.year-top-section {
    background-color: #E6E1F2;
    padding-top: 85px;
    padding-bottom: 75px;

    .section-title {
        color: #535353;
        font-weight: 700;
        margin-bottom: 35px;
    }

    @media (max-width: 949px){
        padding-top: 30px;
        padding-bottom: 40px;
    }
}
</style>