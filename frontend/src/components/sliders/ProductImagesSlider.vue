<template>
    <div>
        <template v-if="isMobile">
            <swiper-container>
                <swiper-slide></swiper-slide>
            </swiper-container>
        </template>
        <template v-else>
            <div class="product-page__main-image">
                <img :src="getImagePath(mainImage)" alt="">
            </div>
            <ul class="product-page__images-list">
                <li class="product-page__image-item" v-for="img in gallery">
                    <img :src="getImagePath(img)" alt="">
                </li>
            </ul>
        </template>
    </div>
</template>

<script>
import { setMatchMedia } from '@/assets/js/methods.js'
import { register } from 'swiper/element/bundle'
register()

export default {
    name: 'ProductImagesSlider',
    props: {
        mainImage: String,
        gallery: Array
    },
    data() {
        return {
            matchMediaMatches: {
                max: {
                    '992': false
                }
            }
        }
    },
    computed: {
        isMobile() {
            return this.matchMediaMatches.max['992']
        },
    },
    methods: {
        setMatchMedia,
        getImagePath(path){
            if(!path)
                return '#'

            return `${import.meta.env.VITE_LINK}${path}`
        }
    },
    mounted() {
        console.log(this.gallery);
        this.setMatchMedia()
    }
}
</script>

<style></style>