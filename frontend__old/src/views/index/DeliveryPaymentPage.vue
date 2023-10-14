<template>
   <main class="delivery-payment">
      <div class="delivery-payment__container container">
         <section class="page-title">
            <div class="page-title__links">
               <a href="../html-main/index.html" class="page-title__arrow-back __icon-back-arrow"></a>
               <a href="../html-main/index.html" class="page-title__link-back">Главная</a>
               <a
                  href="../html-main/delivery-payment.html"
                  class="page-title__link-back"
               >Доставка и оплата</a>
            </div>
            <div class="page-title__title">Доставка и оплата</div>
         </section>
         <section class="info-cards-list cards-list">
            <info-card>
               <template v-slot:title>Принимаем заявки 24/7</template>
               <template v-slot:body>
                  <p>Принимаем ваши заявки на сайте круглосуточно</p>
               </template>
               <template v-slot:image>
                  <img src="img/info-cards/shopping.svg" />
               </template>
            </info-card>
            <info-card>
               <template v-slot:title>Время работы</template>
               <template v-slot:body>
                  <p>Пн-Пт с 10:00 до 19:00</p>
                  <p>Сб-Вс с 11:00 до 17:00</p>
               </template>
               <template v-slot:image>
                  <img src="img/info-cards/support.svg" />
               </template>
            </info-card>
            <info-card>
               <template v-slot:title>Время доставки</template>
               <template v-slot:body>
                  <p>Курьерская доставка “до дверей” ежедневно, с 10:00 до 22:00</p>
               </template>
               <template v-slot:image>
                  <img src="img/info-cards/delivery.svg" />
               </template>
            </info-card>
            <info-card>
               <template v-slot:title>Самовывоз</template>
               <template v-slot:body>
                  <p>Самовывоз из выдачи заказов, в часы работы выбранного пункта</p>
               </template>
               <template v-slot:image>
                  <img src="img/info-cards/supermarket.svg" />
               </template>
            </info-card>
         </section>
         <section class="cards-bordered cards-list">
            <info-card :cardType="'bordered'">
               <template v-slot:title>Бесплатно</template>
               <template v-slot:body>При заказе на сумму от 3 000 руб</template>
            </info-card>
            <info-card :cardType="'bordered'">
               <template v-slot:title>От 300 руб.</template>
               <template v-slot:body>При заказе на сумму от 3 000 руб</template>
            </info-card>
            <info-card :cardType="'bordered'">
               <template v-slot:title>Бесплатно</template>
               <template v-slot:body>Самовывоз из пункта выдачи заказов</template>
            </info-card>
         </section>
         <section class="delivery-payment__block delivery-payment__payment">
            <div class="page-title">
               <div class="page-title__title">Оплата при получении</div>
            </div>
            <div class="cards-image cards-list">
               <div class="card info-card">
                  <div class="card__bottom card__side"></div>
                  <div class="card__container card__side info-card__container">
                     <div class="info-card__image">
                        <img src="@/assets/img/delivery-order/wallet.svg" alt />
                     </div>
                     <div class="info-card__text">Наличные</div>
                  </div>
               </div>
               <div class="card info-card">
                  <div class="card__bottom card__side"></div>
                  <div class="card__container card__side info-card__container">
                     <div class="info-card__image">
                        <img src="@/assets/img/delivery-order/visa.svg" alt />
                     </div>
                  </div>
               </div>
               <div class="card info-card">
                  <div class="card__bottom card__side"></div>
                  <div class="card__container card__side info-card__container">
                     <div class="info-card__image">
                        <img src="@/assets/img/delivery-order/mastercard.svg" alt />
                     </div>
                  </div>
               </div>
               <div class="card info-card">
                  <div class="card__bottom card__side"></div>
                  <div class="card__container card__side info-card__container">
                     <div class="info-card__image">
                        <img src="@/assets/img/delivery-order/mir.svg" alt />
                     </div>
                  </div>
               </div>
            </div>
         </section>
         <section class="delivery-payment__block table-block">
            <div class="page-title">
               <div class="page-title__title">Работаем со всеми проверенными компаниями</div>
            </div>
            <div class="table">
               <div class="table__column">
                  <div class="table__cell">
                     <p class="bold-text">ООО "СДЭК"</p>
                  </div>
                  <div class="table__cell">
                     <p class="bold-text">ООО "Деловые Линии"</p>
                  </div>
                  <div class="table__cell">
                     <p>Flip post</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "М-Транс-Лайн"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Экспресс-Почта"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "ВЛК-Карго"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Аэрокарго"</p>
                  </div>
               </div>
               <div class="table__column">
                  <div class="table__cell">
                     <p>ООО "ЖелДорЭкспидиция"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Беломортранс"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Карго Логистика"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Аэродар"</p>
                  </div>
                  <div class="table__cell">
                     <p>Экспресс-Доставка "DPD"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Аэрогруз"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Энергия"</p>
                  </div>
               </div>
               <div class="table__column">
                  <div class="table__cell">
                     <p>ООО "Байкал-Сервис"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "КИТ"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Центр Авто Перевозок"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Мэйджор Экспресс"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "СПСР-Экспресс"</p>
                  </div>
                  <div class="table__cell">
                     <p>"Экспресс-Авто"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "ЖелДорАльянс"</p>
                  </div>
               </div>
               <div class="table__column">
                  <div class="table__cell">
                     <p>ООО "ЖелДорЭкспидиция"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Пони Экспресс"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "Автотрейдинг"</p>
                  </div>
                  <div class="table__cell">
                     <p>"EMS Russian Post"</p>
                  </div>
                  <div class="table__cell">
                     <p>ООО "УралТранСервис"</p>
                  </div>
               </div>
            </div>
         </section>
      </div>
   </main>
</template>

<script>
import InfoCard from "@/components/misc/InfoCard";

export default {
   name: "DeliveryPayment",
   components: { InfoCard },
};
</script>