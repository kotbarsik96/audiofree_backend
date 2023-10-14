<template>
   <main class="favorites-page">
      <section class="favorites-page__container container">
         <div class="page-title">
            <div class="page-title__links">
               <router-link to="/" class="page-title__arrow-back __icon-back-arrow"></router-link>
               <router-link to="/" class="page-title__link-back">Главная</router-link>
               <router-link to="/favorites" class="page-title__link-back">Избранное</router-link>
            </div>
            <div class="page-title__title">Избранное</div>
         </div>
         <transition-group
            :tag="'div'"
            name="favorites-list"
            class="favorites-page__cards-list cards-list"
         >
            <div class="__isempty" v-if="favoriteProducts.length === 0">
               Здесь ничего нет. Для добавления нажмите на иконку
               <span
                  class="icon-circle__item __icon-heart"
               ></span>
               в карточке или на странице товара.
            </div>

            <div class="card-wrapper" v-for="vendorCode in favoriteProducts" :key="vendorCode">
               <product-card-2
                  :vendorCode="vendorCode"
                  @favoriteToggled="getStorageFavoriteProducts"
               ></product-card-2>
            </div>
         </transition-group>
      </section>
   </main>
</template>

<script>

export default {
   name: "FavoritesPage",
   data() {
      return {
        
      };
   },
   methods: {
      
   },
   computed: {
      
   },
   watch: {
      favoriteProducts: {
         handler() {
            lStorage.setStorage(lStorage.keys.favorites, this.favoriteProducts);
         },
         deep: true,
      },
   },
   created(){
      this.loadProducts();
   },
   mounted() {
      this.getStorageFavoriteProducts();
   },
};
</script>

<style>
.favorites-list-enter-active,
.favorites-list-leave-active {
   transition: all 0.5s;
}

.favorites-list-enter-from,
.favorites-list-leave-to {
   opacity: 0;
}
</style>