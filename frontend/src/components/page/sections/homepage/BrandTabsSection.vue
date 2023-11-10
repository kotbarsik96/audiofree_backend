<template>
    <section class="section pdt-105 pdb-105">
        <div class="container">
            <h3 class="section-title section-title--centered">
                <div>
                    Бренды,
                </div>
                <div class="section-title__highlighted">
                    которые не оставят равнодушными
                </div>
            </h3>
            <div class="brand-tabs">
                <LoadingScreen v-if="isLoading"></LoadingScreen>
                <ul class="brand-tabs__buttons">
                    <li class="brand-tabs__button-item" v-for="brand in brands" :key="brand.id">
                        <button class="brand-tabs__button" :class="{ '__active': brand.name === currentBrand }"
                            type="button" @click="currentBrand = brand.name">
                            <img v-if="brand.icon" :src="brand.icon" :alt="brand.name">
                            <span>
                                {{ capitalizeFirstLetter(brand.name) }}
                            </span>
                        </button>
                    </li>
                    <li class="brand-tabs__button-item">
                        <button class="brand-tabs__button" :class="{ '__active': currentBrand === 'has_discount' }"
                            type="button" @click="currentBrand = 'has_discount'">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                                <path
                                    d="M26.1382 14.3626C26.0255 14.1321 26.0255 13.8678 26.1382 13.6373L27.1825 11.501C27.7639 10.3115 27.3033 8.8937 26.1337 8.27317L24.0332 7.15864C23.8066 7.03844 23.6512 6.8245 23.6068 6.5719L23.196 4.22971C22.9673 2.92564 21.761 2.04933 20.4503 2.23477L18.0958 2.56782C17.8417 2.60369 17.5904 2.52199 17.4061 2.34365L15.697 0.690351C14.7454 -0.230253 13.2547 -0.230307 12.3031 0.690351L10.594 2.34382C10.4096 2.52221 10.1583 2.60375 9.90425 2.56798L7.54981 2.23494C6.23868 2.04938 5.03283 2.9258 4.80407 4.22987L4.39326 6.57196C4.34891 6.82461 4.19355 7.03849 3.96698 7.15875L1.86644 8.27328C0.696903 8.89376 0.236218 10.3116 0.817652 11.5011L1.8619 13.6374C1.97456 13.8679 1.97456 14.1323 1.8619 14.3627L0.817597 16.499C0.236163 17.6885 0.696848 19.1063 1.86639 19.7268L3.96692 20.8414C4.19355 20.9616 4.34891 21.1755 4.39326 21.4281L4.80407 23.7703C5.01232 24.9575 6.03027 25.79 7.20019 25.7899C7.31542 25.7899 7.43234 25.7818 7.54986 25.7652L9.90431 25.4321C10.1582 25.3961 10.4097 25.478 10.5941 25.6563L12.3031 27.3096C12.779 27.77 13.3894 28.0001 14.0001 28C14.6105 28 15.2213 27.7699 15.697 27.3096L17.4061 25.6563C17.5905 25.478 17.8418 25.3965 18.0958 25.4321L20.4503 25.7652C21.7616 25.9507 22.9673 25.0743 23.196 23.7703L23.6069 21.4282C23.6512 21.1755 23.8066 20.9616 24.0332 20.8414L26.1337 19.7268C27.3033 19.1064 27.7639 17.6885 27.1825 16.499L26.1382 14.3626ZM10.7702 6.73285C12.4027 6.73285 13.7309 8.06104 13.7309 9.69356C13.7309 11.3261 12.4027 12.6543 10.7702 12.6543C9.13765 12.6543 7.80946 11.3261 7.80946 9.69356C7.80946 8.06104 9.13765 6.73285 10.7702 6.73285ZM9.24199 19.8999C9.08433 20.0575 8.87767 20.1364 8.67106 20.1364C8.46445 20.1364 8.25773 20.0576 8.10013 19.8999C7.7848 19.5846 7.7848 19.0733 8.10013 18.758L18.7581 8.10003C19.0733 7.7847 19.5847 7.7847 19.9 8.10003C20.2153 8.41535 20.2153 8.92662 19.9 9.24195L9.24199 19.8999ZM17.2298 21.2672C15.5973 21.2672 14.2691 19.939 14.2691 18.3065C14.2691 16.6739 15.5973 15.3457 17.2298 15.3457C18.8623 15.3457 20.1905 16.6739 20.1905 18.3065C20.1905 19.939 18.8623 21.2672 17.2298 21.2672Z"
                                    fill="currentColor" />
                                <path
                                    d="M17.2298 16.9607C16.4878 16.9607 15.884 17.5644 15.884 18.3064C15.884 19.0485 16.4877 19.6522 17.2298 19.6522C17.9719 19.6522 18.5756 19.0485 18.5756 18.3064C18.5756 17.5644 17.9719 16.9607 17.2298 16.9607Z"
                                    fill="currentColor" />
                                <path
                                    d="M10.7702 8.34774C10.0281 8.34774 9.42443 8.95144 9.42443 9.69349C9.42443 10.4355 10.0281 11.0393 10.7702 11.0393C11.5122 11.0393 12.116 10.4356 12.116 9.69349C12.1159 8.95149 11.5122 8.34774 10.7702 8.34774Z"
                                    fill="currentColor" />
                            </svg>

                            <span>
                                Со скидкой
                            </span>
                        </button>
                    </li>
                    <li class="brand-tabs__more">
                        <RouterLink class="brand-tabs__more-link" :to="{ name: 'Catalog' }">
                            Смотреть больше моделей
                        </RouterLink>
                    </li>
                </ul>
                <div class="brand-tabs__list product-cards">
                    <div class="product-cards__wrapper">
                        <component v-for="product in brandProducts" :key="product.id" :productData="product"
                            :is="productCardComponent">
                        </component>
                    </div>
                    <div class="product-cards__swipe circle-wrapper circle-wrapper--not-interactive">
                        <SwipeIcon></SwipeIcon>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import ProductCard from '@/components/cards/products/ProductCard.vue'
import ProductCardDiscount from '@/components/cards/products/ProductCardDiscount.vue'
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import axios from 'axios'
import { capitalizeFirstLetter } from '@/assets/js/methods.js'

export default {
    name: 'BrandTabsSection',
    components: {
        ProductCard,
        ProductCardDiscount,
        LoadingScreen
    },
    data() {
        return {
            brands: [],
            currentBrand: '',
            brandProducts: [],
            loadedProducts: {},
            isLoading: true,
            minProductsCount: 3
        }
    },
    methods: {
        capitalizeFirstLetter,
        async loadBrands() {
            this.isLoading = true

            const link = `${import.meta.env.VITE_TAXONOMIES_GET_LINK}/brand`

            try {
                const res = await axios.get(link, {
                    params: {
                        brand_with_products: this.minProductsCount,
                        in_stock: true,
                        product_status_active: true
                    }
                })
                if (!Array.isArray(res.data.result))
                    throw new Error()

                this.brands = res.data.result
            } catch (err) { }

            this.isLoading = false
        },
        async loadProducts() {
            const alreadyLoaded = this.loadedProducts[this.currentBrand]
            if (alreadyLoaded) {
                this.brandProducts = alreadyLoaded
                return
            }

            this.isLoading = true

            const params = {
                product_status_active: true,
                limit: this.minProductsCount
            }
            if (this.currentBrand === 'has_discount')
                params.has_discount = true
            else
                params.brands = this.currentBrand

            const link = import.meta.env.VITE_PRODUCTS_GET_LINK
            try {
                const res = await axios.get(link, { params })
                if (!Array.isArray(res.data.result))
                    throw new Error()

                this.brandProducts = res.data.result
                this.loadedProducts[this.currentBrand] = res.data.result
            } catch (err) { }

            this.isLoading = false
        }
    },
    computed: {
        productCardComponent(){
            if(this.currentBrand === 'has_discount')
                return ProductCardDiscount

            return ProductCard
        }
    },
    watch: {
        currentBrand(newBrand, oldBrand) {
            if (newBrand === oldBrand)
                return

            this.loadProducts()
        }
    },
    created() {
        this.loadBrands().then(() => {
            if (this.brands[0])
                this.currentBrand = this.brands[0].name
            else
                this.currentBrand = 'has_discount'
        })
    }
}
</script>

<style lang="scss">
.brand-tabs {
    display: flex;

    &__buttons {
        margin-right: 30px;
        flex: 0 0 270px;
    }

    &__button-item {
        margin-bottom: 14px;
    }

    &__button {
        --trans_dur: .3s;

        font-weight: 500;
        position: relative;
        display: flex;
        align-items: center;
        padding: 20px 13px 20px 75px;
        width: 100%;
        height: 100%;
        border-radius: var(--border_radius);
        box-shadow: 0px 0px 2px rgba(0, 0, 0, .1);

        svg {
            position: absolute;
            left: 13px;
            color: var(--inactive_color);
            transition-property: color;
            transition-duration: var(--trans_dur);
        }

        &::after {
            content: '';
            display: block;
            position: absolute;
            right: 0;
            top: 0;
            width: 5px;
            height: 100%;
            border-radius: var(--border_radius);
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            background-color: var(--inactive_color);
            transition-property: background-color;
            transition-duration: var(--trans_dur);
        }
    }

    &__button.__active {
        &::after {
            background-color: var(--theme_color);
        }

        svg {
            color: var(--theme_color);
        }
    }

    &__more-link {
        display: block;
        width: 100%;
        height: 100px;
        background-image: url('/img/homepage/devices-list__bg.png');
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        box-shadow: 0 0 18px rgba(0, 0, 0, .1);
        color: #fff;
        font-weight: 500;
        font-size: 14px;
        line-height: 16px;
        padding: 37px 37px 31px 78px;
        border-radius: var(--border_radius);
    }

    &__list {
        flex: 1 1 auto;

        .product-cards__wrapper {
            margin-top: 0;
        }

        .card {
            max-height: 450px;
        }
    }

    @media (max-width: 1199px) {
        flex-wrap: wrap;

        &__buttons {
            flex: 0 0 100%;
            display: grid;
            justify-content: center;
            grid-template-columns: repeat(auto-fit, 300px);
            grid-gap: 12px;
            margin-bottom: 12px;
        }

        &__button-item,
        &__more {
            flex: 0 0 300px;
        }

        &__list {
            justify-content: center;
        }

        &__button-item {
            margin-bottom: 0;
        }

        &__more-link {
            display: flex;
            align-items: center;
        }
    }

    @media (max-width: 639px) {
        &__buttons {
            justify-content: space-between;
            grid-template-columns: repeat(auto-fit, 48%);
        }

        &__button {
            padding: 14px 9px 14px 54px;
        }

        &__more {
            grid-column: 1 / -1;
        }

        &__more-link {
            height: 56px;
            padding: 15px 37px 15px 100px;
        }
    }

    @media (max-width: 349px) {
        &__buttons {
            grid-template-columns: repeat(auto-fit, 100%);
        }
    }
}
</style>