<template>
   <div class="card product-info-card">
      <div class="card__bottom card__side product-info-card__bottom"></div>
      <div class="card__container card__side product-info-card__container">
         <div class="product-info-card__image">
            <img src="@/assets/img/main-page/headphones-sale.png" alt />
         </div>
         <div class="product-info-card__message">
            <div class="product-info-card__title">{{ title }}</div>
            <div class="product-info-card__text">{{ text }}</div>
         </div>
         <div class="product-info-card__button button button--colored-bg">В каталог скидок</div>
      </div>
   </div>
</template>

<script>
export default {
   name: "ProductInfoCard",
   props: {
      title: {
         type: String,
         default: "Информация",
      },
      text: {
         type: String,
         default:
            "За прошедшие годы текст Lorem Ipsum получил много версий. Некоторые версии появились по ошибке, некоторые - намеренно",
      },
   },
};
</script>