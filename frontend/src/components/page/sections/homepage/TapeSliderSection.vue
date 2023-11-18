<template>
    <ComponentWithGallery tag="section" class="tape-slider-section section section--theme-colored" imageTag="tape-slider" v-model="gallery">
        <div class="container">
            <h3 class="section-title section-title--centered">
                <div>
                    Выберите свои наушники
                </div>
                <div class="section-title__highlighted">
                    Функциональные, комфортные, стильные
                </div>
            </h3>
        </div>
        <div class="tape-slider">
            <Swiper :modules="modules" :slides-per-view="1" loop :autoplay="{ delay: 5000 }" :breakpoints="{
                450: {
                    slidesPerView: 2
                },
                700: {
                    slidesPerView: 3
                },
                1400: {
                    slidesPerView: 4
                },
                1630: {
                    slidesPerView: 5
                }
            }">
                <SwiperSlide v-for="obj in gallery">
                    <ImagePicture :obj="obj"></ImagePicture>
                </SwiperSlide>
            </Swiper>
        </div>
    </ComponentWithGallery>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay } from 'swiper'
import 'swiper/css'
import ComponentWithGallery from '@/components/misc/ComponentWithGallery.vue'

export default {
    name: 'TapeSliderSection',
    components: {
        Swiper,
        SwiperSlide,
        ComponentWithGallery,
    },
    setup() {
        return {
            modules: [Autoplay]
        }
    },
    data(){
        return {
            gallery: []
        }
    }
}
</script>

<style lang="scss">
.tape-slider-section {
    padding: 85px 0;

    .section-title {
        margin-bottom: 65px;
    }

    @media (max-width: 949px){
        padding: 40px 0 50px 0;
    }
}

.tape-slider {
    .swiper-slide {
        padding-left: 20px;

        picture,
        img {
            width: 17.2vw;
            height: 17.2vw;
            padding: 0 15px;
        }

        @media (max-width: 1919px) {

            picture,
            img {
                width: 330px;
                height: 330px;
            }
        }

        @media (max-width: 1049px) {
            padding-left: 13px;

            picture,
            img {
                width: 30vw;
                height: 30vw;
                padding: 0;
            }
        }

        @media (max-width: 767px) {

            picture,
            img {
                width: 225px;
                height: 225px;
            }
        }

        @media (max-width: 699px) {

            picture,
            img {
                width: 45vw;
                height: 45vw;
            }
        }

        @media (max-width: 459px) {

            picture,
            img {
                width: 97%;
                height: 100%;
            }
        }
    }
}
</style>