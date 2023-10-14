<template>
   <main class="product-page" v-if="product">
      <section class="product-block">
         <div class="product-block__container container">
            <div class="product-main">
               <div class="product-main__page-title page-title">
                  <div class="page-title__links">
                     <router-link
                        :to="{ name: 'home' }"
                        class="page-title__arrow-back __icon-back-arrow"
                     ></router-link>
                     <router-link :to="{ name: 'home' }" class="page-title__link-back">Главная</router-link>
                     <router-link :to="{ name: 'catalogue' }" class="page-title__link-back">Каталог</router-link>
                     <router-link :to="{ name: 'home' }" class="page-title__link-back">
                        {{
                        product.name }}
                     </router-link>
                  </div>
               </div>

               <div class="product-main__pictures" v-if="mediaQueries[849]">
                  <swiper class="product-main__image big-image">
                     <template v-slot:container-start>
                        <div class="product-main__icons">
                           <div class="in-stock __icon-correct">В наличии</div>
                           <div class="product-main__icons-container">
                              <div class="icon-circle icon-circle--text icon-circle--sale" v-if="product.sale.percent > 0">
                                 <div class="icon-circle__item">-{{ product.sale.percent }}%</div>
                              </div>
                           </div>
                        </div>
                     </template>
                     <swiper-slide v-for="(image, index) in product.images" :key="index">
                        <img :src="rootPath + 'img/products/' + product.images[index]" alt />
                     </swiper-slide>
                  </swiper>
               </div>
               <div class="product-main__pictures" v-if="!mediaQueries[849]">
                  <div class="product-main__image big-image">
                     <div class="product-main__icons">
                        <div class="in-stock __icon-correct">В наличии</div>
                        <div class="product-main__icons-container">
                           <div class="icon-circle icon-circle--text icon-circle--sale" v-if="product.sale.percent > 0">
                              <div class="icon-circle__item">-{{ product.sale.percent }}%</div>
                           </div>
                        </div>
                     </div>
                     <img
                        :src="rootPath + 'img/products/' + product.images[currentImageIndex]"
                        alt="Фото товара"
                     />
                  </div>
                  <div class="product-main__small-images small-images">
                     <div
                        class="product-main__small-image small-image"
                        :class="{ '__active': currentImageIndex === index }"
                        v-for="(image, index) in product.images"
                        :key="index"
                     >
                        <img
                           :src="rootPath + 'img/products/' + product.images[index]"
                           @click="currentImageIndex = index"
                           alt="Фото товара"
                        />
                     </div>
                  </div>
               </div>

               <div class="product-main__name">{{ product.name }}</div>
               <div class="product-main__rating">
                  <div class="rating">
                     <product-rating :rating="product.rating"></product-rating>
                  </div>
                  <div
                     class="product-main__vendor-code vendor-code"
                     data-dynamic-adaptive="min, 1170, product-info"
                     data-insert-type="prepend, append"
                  >
                     Артикул:
                     <span class="vendor-code__number">{{ vendorCode }}</span>
                  </div>
               </div>
               <div class="product-main__price">
                  <div class="product-main__price--current">{{ product.price.toLocaleString() }} ₽</div>
                  <div
                     class="product-main__price--old"
                     v-if="product['old-price']"
                  >{{ product['old-price'] }}</div>
               </div>
               <div class="product-main__controls controls">
                  <div class="controls__block">
                     <div class="controls__title">Количество:</div>
                     <div class="controls__amount">
                        <div
                           class="controls__amount--less controls__amount-button __icon-back-arrow"
                           @click="changeAmount($event, 'less')"
                        ></div>
                        <div class="controls__amount--number">
                           <input
                              type="text"
                              @input="changeAmount($event, 'typing')"
                              :value="prodParams.amount"
                              maxlength="2"
                           />
                        </div>
                        <div
                           class="controls__amount--more controls__amount-button __icon-next-arrow"
                           @click="changeAmount($event, 'more')"
                        ></div>
                     </div>
                  </div>
                  <div
                     class="controls__block"
                     v-for="(option, optKey) in product.options"
                     :key="optKey"
                     ref="optionBlock"
                  >
                     <div class="controls__title">{{ option.name }}:</div>
                     <div class="controls__options">
                        <label
                           class="controls__option checkcircle"
                           v-for="(optionItem, index) in option.list"
                           :key="index"
                        >
                           <input
                              type="radio"
                              :name="'product-' + optKey"
                              :value="optionItem"
                              @change="changeOption($event, optKey)"
                           />
                           <div class="checkcircle__option">
                              <span></span>
                              <span class="product-option__text">{{ optionItem }}</span>
                           </div>
                        </label>
                     </div>
                  </div>
               </div>
               <div class="product-main__buttons product-buttons">
                  <router-link
                     :to="{ name: 'cart-oneclick' }"
                     class="button button--colored-bg"
                     @click="addToCart('cartOneclick', prodParams)"
                  >Купить в 1 клик</router-link>
                  <button
                     class="button button--to-cart"
                     @click="addToCart('cart', prodParams)"
                  >В корзину</button>
               </div>
            </div>
            <div class="product-info">
               <div class="product-info__icons prodinfo-icons">
                  <div class="prodinfo-icons__warning">
                     <div class="icon-circle icon-circle--warning"></div>
                     <div class="prodinfo-icons__warning-text">
                        До конца акции осталось:
                        <span class="bold-text">3 дня</span>
                     </div>
                  </div>
                  <div class="prodinfo-icons__container">
                     <div class="icon-circle">
                        <div class="icon-circle__item __icon-judge"></div>
                     </div>
                     <div
                        class="icon-circle icon-circle--to-favorites"
                        :class="{'__active': isFavorite}"
                        @click="toggleFavorites"
                     >
                        <div class="icon-circle__item __icon-heart"></div>
                     </div>
                  </div>
               </div>
               <div class="product-info__info-block info-block">
                  <div class="info-block__item">
                     <div class="info-block__title">Доставка:</div>
                     <div
                        class="info-block__option info-block__option--purple __icon-correct"
                     >Санкт-Петербург</div>
                     <div
                        class="info-block__option info-block__option--purple __icon-correct"
                     >Ленинградская область</div>
                     <div
                        class="info-block__option info-block__option--purple __icon-correct"
                     >Россия</div>
                  </div>
                  <div class="info-block__item">
                     <div class="info-block__title">Варианты оплаты:</div>
                     <div
                        class="info-block__option info-block__option--yellow __icon-correct"
                     >Наличными</div>
                     <div
                        class="info-block__option info-block__option--yellow __icon-correct"
                     >Оплата картой</div>
                     <div
                        class="info-block__option info-block__option--yellow __icon-correct"
                     >Оплата по счету</div>
                  </div>
                  <div class="info-block__item">
                     <div class="info-block__title">Наши преимущества:</div>
                     <div
                        class="info-block__option info-block__option--green __icon-correct"
                     >Гарантии</div>
                     <div
                        class="info-block__option info-block__option--green __icon-correct"
                     >Возврат и обмен</div>
                     <div
                        class="info-block__option info-block__option--green __icon-correct"
                     >Лучшая цена</div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <section class="catalogue-page__block tab-spoiler tab-spoiler--colored-bg">
         <h3 class="tab-spoiler__title title">
            <div class="title__top">Что стоит знать</div>
            <div class="title__bottom title__bold">при выборе наушников</div>
         </h3>
         <spoiler-tab-wrapper
            v-if="productInfo"
            class="container"
            :titles="[ { text: 'Характеристики:', contentContainerClass: 'specs' }, 'Описание товара:' ]"
            :tabParams="{ isVertical: true }"
         >
            <template #0>
               <div class="specs__item" v-for="spec in productSpecs" :key="spec.name">
                  <div class="specs__name">{{ spec.name }}</div>
                  <div class="specs__content">{{ spec.text }}</div>
               </div>
            </template>
            <template #1>
               <h5>{{ productDescr.title }}</h5>
               <p v-for="(text, id) in productDescr.paragraphs" :key="id">{{ text }}</p>
            </template>
         </spoiler-tab-wrapper>
         <div class="product-page__spoiler-tab-nocontent" v-else></div>
      </section>
      <section class="product-page__recommends container">
         <div class="page-title">
            <div class="page-title__title">Вас заинтересуют</div>
         </div>
         <div class="product-page__cards-list cards-list">
            <div class="card-wrapper" v-for="reference in product.refProducts" :key="reference">
               <product-card-1 :vendorCode="reference"></product-card-1>
            </div>
         </div>
      </section>
   </main>
</template>

<script>
import { mapMutations, mapGetters, mapActions } from "vuex";
import SpoilerTabWrapper from "@/components/spoiler-tab/SpoilerTabWrapper";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import {
   lStorage,
   addToCart,
   toggleFavorites,
   mediaQueriesHandlers,
   checkProductAmountCorrect,
   getProd,
} from "@/assets/js/scripts";
import rootPath from "@/assets/root-path";

export default {
   name: "ProductPage",
   props: {
      vendorCode: {
         type: String,
         required: true,
      },
   },
   components: {
      SpoilerTabWrapper,
      Swiper,
      SwiperSlide,
   },
   data() {
      return {
         rootPath,
         isFavorite: false,
         mediaQueries: { 849: false },
         currentImageIndex: 0,
         prodParams: { amount: "1" },
         productsInfo: {},
      };
   },
   computed: {
      ...mapGetters(["products", "productCards"]),
      product() {
         return this.getProd(this.vendorCode);
      },
      popup() {
         return PopupNotification;
      },
      productInfo() {
         return this.productsInfo[this.vendorCode];
      },
      productSpecs() {
         return this.productInfo.specs;
      },
      productDescr() {
         return this.productInfo.description;
      },
   },
   methods: {
      ...mapMutations(["addProductCardComponent", "addNotification"]),
      ...mapActions(["loadProducts"]),
      toggleFavorites,
      mediaQueriesHandlers,
      addToCart,
      getProd,
      initComponentParams() {
         // добавить в общий список компонентов (vuex store)
         this.addProductCardComponent(this);
         // указать, находится ли в списке "избранное"
         this.isFavorite = lStorage
            .getStorage(lStorage.keys.favorites)
            .includes(this.vendorCode);
         // выставить опции по умолчанию
         const changeEvent = new Event("change");
         this.$refs.optionBlock.forEach((optBlock) => {
            const input = optBlock.querySelector("input");
            if (input) input.checked = true;
            input.dispatchEvent(changeEvent);
         });
      },
      changeAmount(event, action) {
         const input = event.target
            .closest(".controls__block")
            .querySelector("input[type='text']");
         const value = checkProductAmountCorrect(input, action);
         this.prodParams.amount = value.toString();
      },
      changeOption(event, optName) {
         this.prodParams[optName] = event.target.value;
      },
      async loadProductsInfo() {
         const query = await fetch(rootPath + "json/products-info.json");
         this.productsInfo = await query.json();
      },
   },
   created() {
      if (this.product) {
         this.loadProducts();
         this.loadProductsInfo();
      } else {
         this.$router.push({ name: "not-found" });
      }
   },
   mounted() {
      if (this.product) {
         this.initComponentParams();
         this.mediaQueriesHandlers();
      }
   },
};
</script>