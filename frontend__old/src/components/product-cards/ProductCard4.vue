<template>
   <div
      class="product-card card product-card--type-4"
      v-if="product"
   >
      <div class="card__bottom card__side product-card__bottom"></div>
      <div class="card__container card__side product-card__container">
         <div class="product-card__icons prodcard-icons">
            <div class="prodcard-icons__more icon-circle" @click="toggleCardIcons">
               <div class="icon-circle__item __icon-plus"></div>
            </div>
            <div
               class="prodcard-icons__container"
               ref="cardIconsContainer"
               :class="{
               '__active': isIconsOpened
            }"
            >
               <div class="prodcard-icons__circles">
                  <div class="icon-circle icon-circle--text prodcard-icons__sale">
                     <div class="icon-circle__item">-15%</div>
                  </div>
               </div>
            </div>
         </div>
         <div class="product-card__info">
            <RouterLink
               :to="{ name: 'product', params: { vendorCode } }"
               class="product-card__image"
            >
               <img :src="rootPath + 'img/products/' + product.images[0]" alt />
            </RouterLink>
            <div class="product-card__rating rating">
               <product-rating :rating="product.rating"></product-rating>
            </div>
         </div>
         <RouterLink :to="{ name: 'product', params: { vendorCode } }" class="product-card__name">{{ product.name }}</RouterLink>
         <div
            class="product-card__text"
         >Многие думают, что Lorem Ipsum - взятый с потолка псевдо-латинский набор слов, но это не совсем так.</div>
         <div class="product-card__buttons product-buttons" ref="cardButtons">
            <RouterLink
               :to="{ name: 'product', params: { vendorCode } }"
               class="button button--colored-bg"
            >Купить</RouterLink>
            <div class="product-card__price">{{ product.price }} ₽</div>
         </div>
      </div>
   </div>
</template>

<script>
// общая логика компонентов product-card-[x]
import shared from "@/components/product-cards/shared";
import injectShared from "@/components/inject-shared";

const componentStates = injectShared(shared, "ProductCard4");

export default componentStates;
</script>