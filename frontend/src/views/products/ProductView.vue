<template>
    <div class="container">
        <div class="product-page">
            <ProductImagesSlider class="product-page__images-container" :mainImage="product.image_path" :gallry="product.images"></ProductImagesSlider>
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
            <h1 class="product-page__page-title page-title">
                {{ fullname }}
            </h1>
        </div>
    </div>
</template>

<script>
import ProductImagesSlider from '@/components/sliders/ProductImagesSlider.vue'

export default {
    name: 'ProductView',
    components: {
        ProductImagesSlider
    },
    data() {
        return {}
    },
    computed: {
        product() {
            return this.$route.meta.product
        },
        fullname() {
            const brand = this.product.brand || ''
            const name = this.product.name || ''
            let fullstring = name
            if (brand)
                fullstring = `${brand} ${name}`

            return fullstring
        }
    },
    methods: {

    },
    mounted(){
        
    }
}
</script>

<style lang="scss">
.product-page {
    padding: 65px 0 100px;

    @media (max-width: 992px) {
        padding: 30px 0 40px;
    }
}
</style>