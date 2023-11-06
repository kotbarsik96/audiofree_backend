<template>
    <div>
        <template v-if="isMobile">
            <swiper-container ref="swiperContainer">
                <swiper-slide v-for="path in images" :key="path">
                    <img :src="getImagePath(path)" :alt="path" ref="sliderSlideImage">
                </swiper-slide>
            </swiper-container>
        </template>
        <template v-else>
            <div class="product-page__main-image">
                <img :src="getImagePath(selectedImage)" :alt="selectedImage || ''">
            </div>
        </template>
        <ul class="product-page__images-list">
            <li class="product-page__image-item" v-for="path in images" :key="path" @click="selectedImage = path">
                <img :src="getImagePath(path)" :alt="path">
            </li>
        </ul>
    </div>
</template>

<script>
import { setMatchMedia, getImagePath } from '@/assets/js/methods.js'
import { register } from 'swiper/element/bundle'
register()

export default {
    name: 'ProductImagesSlider',
    props: {
        images: Array,
    },
    data() {
        return {
            selectedImage: this.images[0],
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
    },
    methods: {
        setMatchMedia,
        getImagePath
    },
    watch: {
        selectedImage() {
            if (!this.selectedImage && this.images[0])
                this.selectedImage = this.images[0]

            const swiperInst = this.$refs.swiperContainer 
                ? this.$refs.swiperContainer.swiper : null
            if (swiperInst) {
                const imageIndex = this.$refs.sliderSlideImage
                    .findIndex(img => img.getAttribute('src').includes(this.selectedImage))
                if(imageIndex >= 0)
                    swiperInst.slideTo(imageIndex)
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
        align-items: flex-start;

        img {
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
        swiper-container {
            width: 500px;
            margin-right: 20px;
        }

        swiper-slide {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        swiper-slide img {
            width: 500px;
            height: 465px;
            object-fit: contain;
        }

        &__image-item{
            &:last-child {
                margin-right: 20px;
            }
        }
    }

    @media (max-width: 659px){
        swiper-container {
            margin-right: 0;
        }

        &__images-list {
            display: none;
        }
    }

    @media (max-width: 519px){
        swiper-container {
            width: 100%;
        }

        swiper-slide img {
            width: 85vw;
            height: 81vw;
        }
    }
}
</style>