<template>
    <section class="section section--theme-colored pdt-90 pdb-55" v-if="!hideSection">
        <div class="bestsellers-slider">
            <Swiper :class="{ 'swiper-container--one-slide': bestsellers.length <= 1 }" :modules="[Pagination, EffectFlip]"
                effect="flip" :pagination="{
                    clickable: true,
                    bulletClass: 'bestsellers-slider__bullet',
                    bulletActiveClass: 'bestsellers-slider__bullet--active',
                }" @swiper="onSwiper">
                <SwiperSlide v-for="(product, index) in bestsellers" :key="product.id">
                    <div class="container bestsellers-slider__slide-container">
                        <div class="bestsellers-slider__slide-background-box">
                            <div class="bestsellers-slider__slide-background-title">
                                <div>
                                    ВАШ ВЫБОР
                                </div>
                                <div>
                                    {{ product.name }}
                                </div>
                            </div>
                        </div>
                        <h3 class="bestsellers-slider__slide-title">
                            {{ titles[index] }}
                        </h3>
                        <div class="bestsellers-slider__slide-image">
                            <ImagePicture :obj="product" :alt="product.name"
                                :lazyLoadConditions="swiperLazyLoadConditions[index]"></ImagePicture>
                        </div>
                        <div class="bestsellers-slider__button-container">
                            <RouterLink class="button" :to="{ name: 'Product', params: { productId: product.id } }">
                                Купить
                            </RouterLink>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>
</template>

<script>
import axios from 'axios'
import { getImagePath } from '@/assets/js/methods.js'
import { useIndexStore } from '@/stores/'
import { SwiperLazyLoad } from '@/assets/js/scripts.js'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Pagination, EffectFlip } from 'swiper'
import 'swiper/css'
import 'swiper/css/effect-flip'

export default {
    name: 'BestsellersSlider',
    components: {
        Swiper,
        SwiperSlide
    },
    setup() {
        return {
            Pagination,
            EffectFlip
        }
    },
    data() {
        return {
            bestsellers: [],
            hideSection: false,
            titles: [
                'BESTSELLER',
                'MUST HAVE',
                'GREAT CHOISE'
            ],
            swiperLazyLoadConditions: {},
            swiperLazyLoad: null
        }
    },
    methods: {
        getImagePath,
        async loadBestsellers() {
            const store = useIndexStore()
            store.toggleLoading('loadBestsellers', true)

            const link = `${import.meta.env.VITE_PRODUCTS_GET_LINK}`
            try {
                const res = await axios.get(link, {
                    params: {
                        limit: this.titles.length,
                        bestsellers_first: true
                    }
                })
                if (Array.isArray(res.data.result)) {
                    this.hideSection = false
                    res.data.result.forEach((o, i) => {
                        this.swiperLazyLoadConditions[i] = { isActiveSlide: false }
                    })

                    this.bestsellers = res.data.result
                    this.swiperLazyLoad.onSlideChange()
                }
                else
                    throw new Error()
            } catch (err) {
                this.hideSection = true
            }

            store.toggleLoading('loadBestsellers', false)
        },
        onSwiper(swiper) {
            this.swiperLazyLoad = new SwiperLazyLoad(swiper, this)
        }
    },
    created() {
        this.loadBestsellers()
    },
}
</script>

<style lang="scss">
.bestsellers-slider {
    .swiper-pagination {
        display: flex;
        justify-content: center;
        padding-top: 21px;
    }

    .swiper-slide {
        cursor: grab;
    }

    &__bullet {
        cursor: pointer;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background-color: #784E96;
        border: 3px solid #784E96;
        display: inline-block;
        margin-right: 7px;

        &--active {
            background-color: transparent;
        }
    }

    &__slide-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        position: relative;
        min-height: 560px;
    }

    &__slide-background-box {
        position: absolute;
        z-index: 5;
        width: 590px;
        height: 560px;
        border: 20px solid rgba(147, 126, 204, .5);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    &__slide-background-title {
        text-align: center;
        color: rgba(101, 88, 137, 0.1);
        margin-top: 20px;
        font-weight: 700;

        div:first-child {
            font-size: 24px;
            line-height: 0px;
        }

        div:last-child {
            font-size: 64px;
            line-height: 75px;
        }
    }

    &__slide-title {
        position: absolute;
        z-index: 10;
        text-align: center;
        font-size: 192px;
        font-weight: 700;
        line-height: 225px;
        color: #fff;
    }

    &__slide-image {
        position: relative;
        z-index: 15;

        img,
        picture {
            width: 500px;
            height: 350px;
            object-fit: contain;
        }
    }

    &__button-container {
        position: relative;
        z-index: 15;
        flex: 0 0 100%;
        display: flex;
        justify-content: center;
        margin-top: 20px;

        .button {
            font-size: 16px;
            line-height: 18px;
            padding: 15px 50px;
            min-width: 255px;
            border-color: #fff;
            color: #fff;
            transition-property: border-color, color, background-color;

            &:hover {
                border-color: var(--theme_color_darker);
            }
        }
    }

    @media (max-width: 1159px) {
        &__slide-container {
            min-height: calc(48vw - 20px);
        }

        &__slide-background-box {
            width: calc(51vw - 20px);
            height: calc(48vw - 20px);
        }

        &__slide-title {
            font-size: calc(65px + (192 - 84) * (100vw - 320px) / (var(--container_width) - 320));
            line-height: 1;
        }

        &__slide-image {

            picture,
            img {
                width: calc(43vw - 20px);
                height: calc(30vw - 20px);
            }
        }
    }

    @media (max-width: 767px) {
        &__slide-background-box {
            width: 280px;
            height: 260px;
        }

        &__slide-image {

            picture,
            img {
                width: 350px;
                height: 244px;
            }
        }
    }

    @media (max-width: 559px) {
        &__slide-title {
            font-size: calc(52px + (192 - 84) * (100vw - 320px) / (var(--container_width) - 320));
            line-height: 1;
        }
    }

    @media (max-width: 379px) {
        &__slide-image {

            picture,
            img {
                width: 280px;
                height: 195px;
            }
        }
    }
}
</style>