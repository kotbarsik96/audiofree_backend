<template>
    <section class="section section--theme-colored pdt-85 pdb-85">
        <div class="container">
            <h3 class="section-title section-title--centered">
                <div>
                    Выберите свои наушники,
                </div>
                <div class="section-title__highlighted">
                    Функциональные, комфортные, стильные
                </div>
            </h3>
        </div>
        <div class="tape-slider">
            <Swiper :slides-per-view="5" :spaceBetween="30">
                <SwiperSlide>
                    <!-- <ImagePicutre webpPath="images/" :path=""></ImagePicutre> -->
                </SwiperSlide>
            </Swiper>
        </div>
    </section>
</template>

<script>
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'

export default {
    name: 'TapeSlider',
    components: {
        Swiper,
        SwiperSlide,
    },

}
</script>

<style></style>