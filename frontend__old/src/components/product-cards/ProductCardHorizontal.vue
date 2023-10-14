<template>
   <div
      class="product-card card product-card--type-horizontal"
      v-if="product"
   >
      <div class="card__bottom card__side product-card__bottom"></div>
      <div class="card__container card__side product-card__container">
         <RouterLink
            :to="{ name: 'product', params: { vendorCode } }"
            href="../html-products/vendorCode.html"
            class="product-card__image"
         >
            <img :src="rootPath + 'img/products/' + product.images[0]" alt />
         </RouterLink>
         <div class="product-card__vertical-info">
            <RouterLink
               :to="{ name: 'product', params: { vendorCode } }"
               href="../html-products/vendorCode.html"
               class="product-card__name"
            >{{ product.name }}</RouterLink>
            <div class="product-card__rating rating">
               <product-rating :rating="product.rating"></product-rating>
            </div>
            <div class="product-card__price">{{ product.price }} ₽</div>
         </div>
      </div>
   </div>
</template>

<script>
// общая логика компонентов product-card-[x]
import shared from "@/components/product-cards/shared";
import injectShared from "@/components/inject-shared";

const componentStates = injectShared(shared, "ProductCardHorizontal");

export default componentStates;
</script>