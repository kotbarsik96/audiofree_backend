<template>
    <div>
        <template v-if="isMobile">
            <Swiper ref="swiperContainer" @swiper="onSwiper">
                <SwiperSlide v-for="(obj, index) in product.images" :key="obj.id" :data-index="index">
                    <ImagePicture :obj="obj" :alt="obj.path" ref="sliderSlideImage"></ImagePicture>
                </SwiperSlide>
            </Swiper>
        </template>
        <template v-else>
            <div class="product-page__main-image">
                <ImagePicture :obj="selectedImage" :alt="selectedImage.path"></ImagePicture>
            </div>
        </template>
        <ul class="product-page__images-list">
            <li class="product-page__image-item" v-for="(obj, index) in product.images" :key="obj.id"
                @click="selectedImageIndex = index">
                <ImagePicture :obj="obj" :alt="obj.path" ref="sliderSlideImage"></ImagePicture>
            </li>
        </ul>
    </div>
</template>

<script>
import { setMatchMedia, getImagePath } from '@/assets/js/methods.js'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'

export default {
    name: 'ProductImagesSlider',
    props: {
        images: Array,
        product: {
            type: Object,
            required: true
        }
    },
    components: {
        Swiper,
        SwiperSlide
    },
    data() {
        return {
            selectedImageIndex: 0,
            swiper: null,
            matchMediaMatches: {
                max: {
                    '849': false
                }
            }
        }
    },
    computed: {
        isMobile() {
            return this.matchMediaMatches.max['849']
        },
        selectedImage() {
            return this.product.images[this.selectedImageIndex] || {}
        }
    },
    methods: {
        setMatchMedia,
        getImagePath,
        onSwiper(swiper) {
            this.swiper = swiper
        }
    },
    watch: {
        selectedImageIndex(index) {
            const swiperInst = this.$refs.swiperContainer
            if (swiperInst) {
                if (index >= 0 && this.swiper)
                    this.swiper.slideTo(index)
            }
        }
    },
    mounted() {
        this.setMatchMedia()
    }
}
</script>

<style lang="scss">
.product-page {
    &__main-image {
        border-radius: var(--border_radius);
        border: 1px solid #e3e3e3;
        box-shadow: 0px 0px 11px rgba(0, 0, 0, .05);
        width: 470px;
        height: 470px;
        padding: 20px;
        display: flex;
        align-items: center;

        picture, img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    }

    &__images-list {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }

    &__image-item {
        cursor: pointer;
        width: 80px;
        height: 80px;
        border-radius: var(--border_radius);
        border: 1px solid #e3e3e3;
        box-shadow: 0px 0px 11px rgba(0, 0, 0, .05);
        padding: 15px;
        margin-right: 17px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;

        &:last-child {
            margin-right: 0;
        }

        img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    }

    @media (max-width: 849px) {
        .swiper {
            width: 500px;
            margin-right: 20px;
        }

        .swiper-slide {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .swiper-slide picture {
            width: 500px;
            height: 465px;
            object-fit: contain;
        }

        &__image-item {
            &:last-child {
                margin-right: 20px;
            }
        }
    }

    @media (max-width: 659px) {
        .swiper-container {
            margin-right: 0;
        }

        &__images-list {
            display: none;
        }
    }

    @media (max-width: 519px) {
        .swiper-container {
            width: 100%;
        }

        .swiper-slide img {
            width: 85vw;
            height: 81vw;
        }
    }
}
</style>