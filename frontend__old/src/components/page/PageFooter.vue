<template>
   <footer class="footer">
      <div class="footer-main">
         <div class="footer-main__container container">
            <div class="footer-main__block footer-main__lk">
               <div class="footer-main__title">Личный кабинет</div>
               <div class="footer-main__list">
                  <div class="footer-main__item">
                     <a href="#">Зарегестрироваться</a>
                  </div>
                  <div class="footer-main__item">
                     <a href="#">Войти в аккаунт</a>
                  </div>
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'favorites' }">Отложенные товары</router-link>
                  </div>
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'cart' }">Ваши заказы</router-link>
                  </div>
               </div>
               <div class="footer-main__back-call input-field">
                  <div class="input-field__title">Закажите обратный звонок:</div>
                  <form class="input-field__form">
                     <div class="input-field__text">
                        <input type="tel" placeholder="Ваш номер телефона" />
                     </div>
                     <label for="back-call__submit" class="input-field__submit">
                        <div class="__icon __icon-next-arrow">
                           <input type="submit" value id="back-call__submit" />
                        </div>
                     </label>
                  </form>
               </div>
            </div>
            <div class="footer-main__block footer-main__categories">
               <div class="footer-main__title">Категории</div>
               <div class="footer-main__list">
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'catalogue' }">TRUE WIRELESS</router-link>
                  </div>
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'catalogue' }">Гарнитуры</router-link>
                  </div>
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'catalogue' }">Накладные</router-link>
                  </div>
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'catalogue' }">Полноразмерные</router-link>
                  </div>
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'catalogue' }">С проводом</router-link>
                  </div>
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'catalogue' }">С шейным ободком</router-link>
                  </div>
               </div>
            </div>
            <div class="footer-main__block footer-main__shop">
               <div class="footer-main__title">Магазин</div>
               <div class="footer-main__list">
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'delivery-payment' }">Доставка и оплата</router-link>
                  </div>
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'guarantees' }">Гарантия и возврат</router-link>
                  </div>
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'delivery-payment' }">Пункты самовывоза</router-link>
                  </div>
                  <div class="footer-main__item">
                     <router-link :to="{ name: 'contacts' }">Контакты</router-link>
                  </div>
               </div>
            </div>
            <div class="footer-main__block footer-main__contacts">
               <div class="footer-main__title">Контакты:</div>
               <div class="footer-main__contacts-block contacts-block">
                  <div class="contacts-block__circle icon-circle __mobile-adapt">
                     <a
                        class="contacts-block__icon icon-circle__item __icon-phone"
                        href="tel: +71111111111"
                     ></a>
                  </div>
                  <div class="contacts-block__text">
                     <div class="contacts-block__title">Бесплатный звонок по РФ</div>
                     <div class="contacts-block__info">
                        <a href="tel: +71111111111">8 111-11-11-111</a>
                     </div>
                  </div>
               </div>
               <div class="footer-main__contacts-block contacts-block">
                  <div class="contacts-block__circle icon-circle __mobile-adapt">
                     <span class="contacts-block__icon icon-circle__item __icon-gps"></span>
                  </div>
                  <div class="contacts-block__text">
                     <div class="contacts-block__title">Санкт-Петербург,</div>
                     <div class="contacts-block__info">Калининградская ул. 25</div>
                  </div>
               </div>
               <div class="footer-main__contacts-block contacts-block">
                  <div class="contacts-block__circle icon-circle __mobile-adapt">
                     <span class="contacts-block__icon icon-circle__item __icon-mail"></span>
                  </div>
                  <div class="contacts-block__text">
                     <div class="contacts-block__title">По всем вопросам пишите:</div>
                     <div class="contacts-block__info">hello@audiofree.ru</div>
                  </div>
               </div>
               <div class="footer-main__socials">
                  <a href="https://vk.com" class="__icon-vk"></a>
                  <a href="https://facebook.com" class="__icon-fb"></a>
                  <a href="https://web.telegram.org/" class="__icon-tg"></a>
                  <a href="https://www.whatsapp.com/" class="__icon-whatsapp"></a>
                  <a href="https://www.viber.com/" class="__icon-viber"></a>
               </div>
            </div>
         </div>
      </div>
      <div class="footer-bottom">
         <div class="footer-bottom__container container">
            <div class="footer-bottom__copyrights">audiofree.kotbarsik96.ru</div>
            <div class="footer-bottom__logo">
               <div class="site-logo __icon-logo">AudioFree</div>
            </div>
            <div class="footer-bottom__politics">
               <a href="#">Политика конфиденциальности</a>
            </div>
         </div>
      </div>
   </footer>
</template>

<script>
export default {
   name: "PageFooter",
};
</script>