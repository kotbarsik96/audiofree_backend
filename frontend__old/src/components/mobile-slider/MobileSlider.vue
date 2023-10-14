<template>
   <swiper
      class="mobile-slider"
      :class="{ 'cards-list': !mediaQueries[media] }"
      :slidesPerView="1"
      :breakpoints="{ 500: { slidesPerView: 2 } }"
      :style="{ 'padding-bottom': mediaQueries[media] ? '50px' : '0' }"
      @swiper="initMobileSlider"
   >
      <component
         :is="mediaQueries[media] ? 'swiper-slide' : 'div'"
         class="mobile-slider__slide"
         :class="slideClassName"
         v-for="(slide, slName) in $slots"
         :key="slName"
      >
         <slot :name="slName"></slot>
      </component>
      <template v-slot:container-end v-if="mediaQueries[media]">
         <div class="icon-circle icon-circle--slider" @click="flipMobileSlides($event)">
            <div class="icon-circle__item __icon-slider-hand"></div>
         </div>
      </template>
   </swiper>
</template>

<script>
import { mediaQueriesHandlers } from "@/assets/js/scripts";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css/bundle";

export default {
   name: "MobileSlider",
   components: { Swiper, SwiperSlide },
   props: {
      media: {
         type: Number,
         default: 767,
      },
      slideClassName: String,
   },
   data() {
      return {
         mediaQueries: {},
         mobileSliders: [],
      };
   },
   methods: {
      mediaQueriesHandlers,
      initMobileSlider(swiper) {
         this.mobileSliders.push(swiper);
      },
      flipMobileSlides(event) {
         this.mobileSliders.forEach((slider) => {
            if (event.target.closest(".swiper") === slider.el) {
               if (slider.isEnd) slider.direction = "prev";
               else if (slider.isBeginning) slider.direction = "next";

               slider.direction === "prev"
                  ? slider.slidePrev()
                  : slider.slideNext();
            }
         });
      },
   },
   created() {
      this.mediaQueries[this.media] = false;
   },
   mounted() {
      this.mediaQueriesHandlers();
   },
};
</script>