<template>
   <div
      class="product-card card product-card--type-3"
      v-if="product"
   >
      <div class="card__bottom card__side product-card__bottom"></div>
      <div class="card__container card__side product-card__container">
         <div class="product-card__icons prodcard-icons">
            <div
               class="prodcard-icons__more icon-circle"
               @click="toggleCardIcons"
               :class="{
               '__active': isIconsOpened
            }"
            >
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
         <RouterLink
            :to="{ name: 'product', params: { vendorCode } }"
            class="product-card__image"
         >
            <img :src="rootPath + 'img/products/' + product.images[0]" alt />
         </RouterLink>
         <RouterLink
            :to="{ name: 'product', params: { vendorCode } }"
            class="product-card__name"
         >{{ product.name }}</RouterLink>
         <div class="product-card__info">
            <div class="product-card__time prodcard-time">
               <div class="prodcard-time__icon-circle icon-circle icon-circle--text">
                  <div class="icon-circle__item">
                     <div class="icon-circle__bold">50</div>
                     <div class="icon-circle__small">Дней</div>
                  </div>
               </div>
               <div class="prodcard-time__text">До конца акции на этот товар осталось:</div>
            </div>
         </div>
         <div class="product-card__buttons product-buttons" ref="cardButtons">
            <RouterLink
               :to="{ name: 'product', params: { vendorCode } }"
               class="button button--colored-border"
            >Подробней</RouterLink>
         </div>
      </div>
   </div>
</template>

<script>
// общая логика компонентов product-card-[x]
import shared from "@/components/product-cards/shared";
import injectShared from "@/components/inject-shared";

const componentStates = injectShared(shared, "ProductCard3");

export default componentStates;
</script>