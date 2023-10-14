<template>
   <main class="order-page">
      <div class="order-page__container container">
         <div class="page-title">
            <div class="page-title__links">
               <RouterLink
                  :to="'/' + cartRouteName"
                  class="page-title__arrow-back __icon-back-arrow"
               ></RouterLink>
               <RouterLink :to="{ name: 'home' }" class="page-title__link-back">Главная</RouterLink>
               <RouterLink :to="{ name: cartRouteName }" class="page-title__link-back">Корзина</RouterLink>
               <RouterLink
                  :to="{ name: orderRouteName }"
                  class="page-title__link-back"
               >Оформление заказа</RouterLink>
            </div>
            <div class="page-title__title">Оформите заказ</div>
         </div>
         <section class="order-block">
            <div class="order-block__container">
               <div class="order-block__item orbl-item">
                  <div class="orbl-item__title">
                     <div class="orbl-item__title-number">01</div>
                     <div class="orbl-item__title-text">Личные данные</div>
                  </div>
                  <div class="orbl-item__body">
                     <div class="orbl-item__list">
                        <div class="text-input">
                           <div class="text-input__text">
                              <input type="text" placeholder="Имя" />
                           </div>
                        </div>
                        <div class="text-input">
                           <div class="text-input__text">
                              <input type="email" placeholder="E-mail" />
                           </div>
                        </div>
                        <div class="text-input">
                           <div class="text-input__text">
                              <input type="tel" placeholder="Телефон" />
                           </div>
                        </div>
                        <div class="text-input">
                           <div class="text-input__text">
                              <input type="text" placeholder="Город" />
                           </div>
                        </div>
                     </div>
                     <div class="orbl-item__block">
                        <div class="text-input orbl-item__block-body">
                           <textarea
                              name="order-comment"
                              rows="7"
                              placeholder="Комментарий к заказу"
                           ></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="order-block__item orbl-item">
                  <div class="orbl-item__title">
                     <div class="orbl-item__title-number">02</div>
                     <div class="orbl-item__title-text">Доставка</div>
                  </div>
                  <div class="orbl-item__body">
                     <div class="orbl-item__list">
                        <label class="checkcircle">
                           <input type="radio" name="order-delivery" />
                           <div class="checkcircle__option">
                              <span></span>
                              Доставка в пределах города
                           </div>
                        </label>
                        <label class="checkcircle">
                           <input type="radio" name="order-delivery" />
                           <div class="checkcircle__option">
                              <span></span>
                              Доставка за пределы города
                           </div>
                        </label>
                        <label class="checkcircle">
                           <input type="radio" name="order-delivery" />
                           <div class="checkcircle__option">
                              <span></span>
                              Самовывоз
                           </div>
                        </label>
                        <label class="checkcircle">
                           <input type="radio" name="order-delivery" />
                           <div class="checkcircle__option">
                              <span></span>
                              Экспресс доставка
                           </div>
                        </label>
                     </div>
                     <div class="orbl-item__block">
                        <div class="text-input orbl-item__block-body">
                           <textarea name="order-address" placeholder="Адрес доставки" rows="7"></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="order-block__item orbl-item">
                  <div class="orbl-item__title">
                     <div class="orbl-item__title-number">03</div>
                     <div class="orbl-item__title-text">Оплата</div>
                  </div>
                  <div class="orbl-item__body orbl-item__body--divided">
                     <div class="orbl-item__list">
                        <label class="checkcircle">
                           <input type="radio" name="order-payment" />
                           <div class="checkcircle__option">
                              <span></span>
                              Оплата наличными
                           </div>
                        </label>
                        <label class="checkcircle">
                           <input type="radio" name="order-payment" />
                           <div class="checkcircle__option">
                              <span></span>
                              Оплата банковской картой
                           </div>
                        </label>
                     </div>
                     <div id="cart-app" class="orbl-item__block">
                        <div class="orbl-item__block-body">
                           <div class="orbl-item__sum">
                              <div class="orbl-item__sum-text">Сумма заказа:</div>
                              <div
                                 class="orbl-item__sum-number total-sum-products"
                              >{{ totalPrice.toLocaleString() }} ₽</div>
                           </div>
                           <div class="orbl-item__sum">
                              <div class="orbl-item__sum-text">Сумма доставки:</div>
                              <div
                                 class="orbl-item__sum-number total-sum-delivery"
                                 data-total-sum-delivery="0"
                              >0 ₽</div>
                           </div>
                           <div class="orbl-item__sum">
                              <div class="orbl-item__sum-text">Итого:</div>
                              <div
                                 class="orbl-item__sum-number total-sum-all"
                              >{{ totalPrice.toLocaleString() }} ₽</div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </section>
         <section class="order-button">
            <a
               href="../html-products/order-page.html"
               class="button button--colored-bg"
            >Оформить заказ</a>
         </section>
      </div>
   </main>
</template>

<script>
import { lStorage, getProd } from "@/assets/js/scripts";
import { mapGetters, mapActions } from "vuex";

export default {
   name: "OrderPage",
   data() {
      return {
         cartRouteName: "cart",
         orderRouteName: "order",
      };
   },
   methods: {
      getProd,
      ...mapActions(["loadProducts"])
   },
   computed: {
      ...mapGetters(["products"]),
      cartType() {
         switch (this.$route.name) {
            case "order":
               this.cartRouteName = "cart";
               this.orderRouteName = "order";
               return "cart";
            case "order-oneclick":
               this.cartRouteName = "cart-oneclick";
               this.orderRouteName = "order-oneclick";
               return "cart-oneclick";
         }
      },
      totalPrice() {
         const productsCart = lStorage.getStorage(lStorage.keys[this.cartType]);
         let totalPrice = 0;
         productsCart.forEach((cartProd) => {
            const prodInfo = this.getProd(cartProd.vendorCode);
            totalPrice += prodInfo.price * cartProd.amount;
         });

         return totalPrice;
      },
   },
   created(){
      this.loadProducts();
   }
};
</script>