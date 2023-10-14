<template>
   <main class="main-page">
      <swiper
         :tag="'section'"
         class="main-page__block main-page__block--colored pagination-slider"
         :modules="modules"
         :pagination="{ clickable: true, el: '.pagination-slider__bullets' }"
         effect="flip"
      >
         <div class="pagination-slider__bullets swiper-pagination"></div>
         <swiper-slide class="pagination-slider__slide pagination-slide swiper-slide">
            <div class="pagination-slide__container container">
               <div class="pagination-slide__layer-1 pagination-slide__square square-logo">
                  <div class="square-logo__ellipse"></div>
                  <div class="square-logo__text">
                     <div class="square-logo__text-item square-logo__text-item--small">Ваш выбор</div>
                     <div class="square-logo__text-item">JBL T500BT</div>
                  </div>
               </div>
               <div class="pagination-slide__layer-2">
                  <div class="pagination-slide__text">BESTSELLER</div>
               </div>
               <div class="pagination-slide__layer-3">
                  <div class="pagination-slide__image">
                     <img src="img/products/jbl_t500bt/6.png" alt />
                  </div>
               </div>
               <router-link :to="{ name: 'product', params: { vendorCode: '400523' } }" class="pagination-slide__button button">Купить</router-link>
            </div>
         </swiper-slide>
         <swiper-slide class="pagination-slider__slide pagination-slide swiper-slide">
            <div class="pagination-slide__container container">
               <div class="pagination-slide__layer-1 pagination-slide__square square-logo">
                  <div class="square-logo__text">
                     <div class="square-logo__text-item square-logo__text-item--small">Ваш выбор</div>
                     <div class="square-logo__text-item">HUAWEI FREEBUDS</div>
                  </div>
               </div>
               <div class="pagination-slide__layer-2">
                  <div class="pagination-slide__text">GOOD CHOICE</div>
               </div>
               <div class="pagination-slide__layer-3">
                  <div class="pagination-slide__image">
                     <img src="img/products/huawei_freebuds/6.png" alt />
                  </div>
               </div>
               <router-link :to="{ name: 'product', params: { vendorCode: '400521' } }" class="pagination-slide__button button">Купить</router-link>
            </div>
         </swiper-slide>
         <swiper-slide class="pagination-slider__slide pagination-slide swiper-slide">
            <div class="pagination-slide__container container">
               <div class="pagination-slide__layer-1 pagination-slide__square square-logo">
                  <div class="square-logo__text">
                     <div class="square-logo__text-item square-logo__text-item--small">Ваш выбор</div>
                     <div class="square-logo__text-item">GALAXY BUDS 2</div>
                  </div>
               </div>
               <div class="pagination-slide__layer-2">
                  <div class="pagination-slide__text">WORTH IT</div>
               </div>
               <div class="pagination-slide__layer-3">
                  <div class="pagination-slide__image">
                     <img src="img/products/galaxy_buds_2/6.png" alt />
                  </div>
               </div>
               <router-link :to="{ name: 'product', params: { vendorCode: '400515' } }" class="pagination-slide__button button">Купить</router-link>
            </div>
         </swiper-slide>
      </swiper>
      <section class="main-page__block page-reference">
         <div class="page-reference__title title">
            <div class="title__top">100% совместимость</div>
            <div class="title__bottom title__bold">с любым интерфейсом</div>
         </div>
         <devices-toggle
            class="page-reference__container page-reference__flex container"
            :isMobileSlider="true"
            :devices="{
               iPhone: { title: 'Для iPhone', icon: 'apple', vendorCodes: ['400511', '400513', '400514'] },
               Samsung: { title: 'Для Samsung', icon: 'samsung', vendorCodes: ['400515', '400516', '400517'] },
               Xiaomi: { title: 'Для Xiaomi', icon: 'xiaomi', vendorCodes: ['400518', '400519', '400520'] },
               Huawei: { title: 'Для Huawei', icon: 'huawei', vendorCodes: ['400521', '400522', '400521'] },
               discount: { title: 'Со скидкой', icon: 'percent-shaped', vendorCodes: ['400523', '400524', '400525'] },
            }"
            :productCardType="'1'"
         ></devices-toggle>
      </section>
      <section class="main-page__block tape-slider-block">
         <div class="tape-slider-block__title title title--on-colored-bg">
            <div class="title__top">Выберите свои наушники</div>
            <div class="title__bottom title__bold">Функциональные, комфортные, стильные</div>
         </div>
         <swiper
            :slidesPerView="1"
            :breakpoints="{
               640: {
                  slidesPerView: 2
               },
               940: {
                  slidesPerView: 3
               },
               1260: {
                  slidesPerView: 4
               },
               1800: {
                  slidesPerView: 5
               }
            }"
         >
            <swiper-slide class="tape-slide">
               <router-link :to="{ name: 'catalogue', params: { pageNumber: 1 } }" class="tape-slide__container">
                  <div class="tape-slide__image">
                     <img src="img/tape-slider/true-wireless.png" alt />
                  </div>
                  <div class="tape-slide__text">TRUE WIRELESS</div>
               </router-link>
            </swiper-slide>
            <swiper-slide class="tape-slide">
               <router-link :to="{ name: 'catalogue', params: { pageNumber: [1] } }" class="tape-slide__container">
                  <div class="tape-slide__image">
                     <img src="img/tape-slider/headset.png" alt />
                  </div>
                  <div class="tape-slide__text tape-slide__text--topright">Гарнитуры</div>
               </router-link>
            </swiper-slide>
            <swiper-slide class="tape-slide">
               <router-link :to="{ name: 'catalogue', params: { pageNumber: [1] } }" class="tape-slide__container">
                  <div class="tape-slide__image">
                     <img src="img/tape-slider/on-ear-green.png" alt />
                  </div>
                  <div class="tape-slide__text">Накладные наушники</div>
               </router-link>
            </swiper-slide>
            <swiper-slide class="tape-slide">
               <router-link :to="{ name: 'catalogue', params: { pageNumber: [1] } }" class="tape-slide__container">
                  <div class="tape-slide__image">
                     <img src="img/tape-slider/full-size.png" alt />
                  </div>
                  <div class="tape-slide__text">Полно-размерные наушники</div>
               </router-link>
            </swiper-slide>
            <swiper-slide class="tape-slide">
               <router-link :to="{ name: 'catalogue', params: { pageNumber: [1] } }" class="tape-slide__container">
                  <div class="tape-slide__image">
                     <img src="img/tape-slider/on-ear-red.png" alt />
                  </div>
                  <div class="tape-slide__text">Накладные наушники</div>
               </router-link>
            </swiper-slide>
            <swiper-slide class="tape-slide">
               <router-link :to="{ name: 'catalogue', params: { pageNumber: [1] } }" class="tape-slide__container">
                  <div class="tape-slide__image">
                     <img src="img/tape-slider/headset.png" alt />
                  </div>
                  <div class="tape-slide__text tape-slide__text--topright">Гарнитуры</div>
               </router-link>
            </swiper-slide>
            <swiper-slide class="tape-slide">
               <router-link :to="{ name: 'catalogue', params: { pageNumber: [1] } }" class="tape-slide__container">
                  <div class="tape-slide__image">
                     <img src="img/tape-slider/full-size.png" alt />
                  </div>
                  <div class="tape-slide__text">Полно-размерные наушники</div>
               </router-link>
            </swiper-slide>
         </swiper>
      </section>
      <section class="main-page__block page-reference">
         <div class="page-reference__title title">
            <div class="title__top">Постоянные акции</div>
            <div class="title__bottom title__bold">и сезонные предложения</div>
         </div>
         <mobile-slider
            :media="767"
            :slideClassName="'card-wrapper'"
            class="page-reference__container container"
         >
            <template v-slot:slide1>
               <product-info-card
                  :title="'Распродажа'"
                  :text="'За прошедшие годы текст Lorem Ipsum получил много версий. Некоторые версии появились по ошибке, некоторые - намеренно'"
               ></product-info-card>
            </template>
            <template v-slot:slide2>
               <product-card-3 :vendorCode="'400514'"></product-card-3>
            </template>
            <template v-slot:slide3>
               <product-card-3 :vendorCode="'400515'"></product-card-3>
            </template>
            <template v-slot:slide4>
               <product-card-3 :vendorCode="'400516'"></product-card-3>
            </template>
         </mobile-slider>
      </section>
      <section class="main-page__block offer-block test-offer">
         <div class="offer-block__container test-offer__container container">
            <div class="offer-block__message test-offer__message message">
               <div class="test-offer__title message__title">
                  <span class="message__bold">Подберем 5 лучших моделей</span>
                  беспроводных наушников под Ваши цели
               </div>
               <div class="test-offer__text message__text">
                  Пройдите тест и получите
                  <span class="message__bold--colored">скидку до 10%</span>
                  на первую покупку
               </div>
            </div>
            <div class="offer-block__button">
               <a href="#" class="button button--colored-bg">Пройти тест и получить скидку!</a>
            </div>
            <div class="offer-block__pictures test-offer__pictures">
               <div
                  class="offer-block__background test-offer__background square-logo square-logo--rotated"
               >
                  <div class="square-logo__ellipse"></div>
                  <div class="square-logo__text">
                     <div class="square-logo__text-item">5 BEST</div>
                     <div class="square-logo__text-item">MODELS</div>
                  </div>
               </div>
               <div class="offer-block__image test-offer__image">
                  <img src="img/main-page/test-picture.png" alt />
               </div>
            </div>
         </div>
      </section>
      <section class="main-page__block page-reference">
         <div class="page-reference__title title">
            <div class="title__top">Постоянные акции</div>
            <div class="title__bottom title__bold">и сезонные предложения</div>
         </div>
         <mobile-slider
            :media="767"
            :slideClassName="'card-wrapper'"
            class="page-reference__container container"
         >
            <template v-slot:slide1>
               <product-card-3 :vendorCode="'400517'"></product-card-3>
            </template>
            <template v-slot:slide2>
               <product-card-3 :vendorCode="'400515'"></product-card-3>
            </template>
            <template v-slot:slide3>
               <product-card-3 :vendorCode="'400513'"></product-card-3>
            </template>
            <template v-slot:slide4>
               <product-card-3 :vendorCode="'400520'"></product-card-3>
            </template>
         </mobile-slider>
         <div class="page-reference__button">
            <a
               href="../html-main/catalogue.html"
               class="button button--colored-bg"
            >Смотреть больше моделей</a>
         </div>
      </section>
      <section class="main-page__block num-list-block">
         <div class="num-list-block__title title title--on-colored-bg">
            <div class="title__top">Чем беспроводные лучше?</div>
            <div class="title__bottom title__bold">Всё о преимуществах наушников Bluetooth</div>
         </div>
         <div class="num-list-block__container num-list container">
            <div class="num-list__side num-list__side--left">
               <div class="num-list__item">
                  <div class="num-list__number">01</div>
                  <div class="num-list__title">Отсутствие проводов</div>
                  <div class="num-list__text">
                     Многие думают, что Lorem Ipsum - взятый с потолка псевдо-латинский набор слов, но
                     это не
                     совсем так.
                  </div>
               </div>
               <div class="num-list__item">
                  <div class="num-list__number">02</div>
                  <div class="num-list__title">Удобство</div>
                  <div class="num-list__text">
                     Многие думают, что Lorem Ipsum - взятый с потолка псевдо-латинский набор слов, но
                     это не
                     совсем так.
                  </div>
               </div>
               <div class="num-list__item">
                  <div class="num-list__number">03</div>
                  <div class="num-list__title">Красивый аксессуар</div>
                  <div class="num-list__text">
                     Многие думают, что Lorem Ipsum - взятый с потолка псевдо-латинский набор слов, но
                     это не
                     совсем так.
                  </div>
               </div>
            </div>
            <div class="num-list__pictures">
               <div class="num-list__background ellipse"></div>
               <div class="num-list__image">
                  <img src="img/main-page/NA_1.png" alt />
               </div>
            </div>
            <div class="num-list__side num-list__side--right">
               <div class="num-list__item">
                  <div class="num-list__number">04</div>
                  <div class="num-list__title">Без ограничений</div>
                  <div class="num-list__text">
                     Многие думают, что Lorem Ipsum - взятый с потолка псевдо-латинский набор слов, но
                     это не
                     совсем так.
                  </div>
               </div>
               <div class="num-list__item">
                  <div class="num-list__number">05</div>
                  <div class="num-list__title">Функциональность</div>
                  <div class="num-list__text">
                     Многие думают, что Lorem Ipsum - взятый с потолка псевдо-латинский набор слов, но
                     это не
                     совсем так.
                  </div>
               </div>
               <div class="num-list__item">
                  <div class="num-list__number">06</div>
                  <div class="num-list__title">Экономия зарядки</div>
                  <div class="num-list__text">
                     Многие думают, что Lorem Ipsum - взятый с потолка псевдо-латинский набор слов, но
                     это не
                     совсем так.
                  </div>
               </div>
            </div>
         </div>
      </section>
      <section class="main-page__block page-reference page-reference--light">
         <div class="page-reference__title title">
            <div class="title__top title__bold">ТОП-2020</div>
            <div class="title__bottom title__bold">лидеры предзаказов, обзоров и рекомендаций</div>
         </div>
         <mobile-slider
            :media="992"
            :slideClassName="'card-wrapper--horizontal'"
            class="page-reference__container container"
         >
            <template v-slot:slide1>
               <product-card-horizontal :vendorCode="'400511'"></product-card-horizontal>
            </template>
            <template v-slot:slide2>
               <product-card-horizontal :vendorCode="'400512'"></product-card-horizontal>
            </template>
            <template v-slot:slide3>
               <product-card-horizontal :vendorCode="'400513'"></product-card-horizontal>
            </template>
            <template v-slot:slide4>
               <product-card-horizontal :vendorCode="'400514'"></product-card-horizontal>
            </template>
            <template v-slot:slide5>
               <product-card-horizontal :vendorCode="'400517'"></product-card-horizontal>
            </template>
            <template v-slot:slide6>
               <product-card-horizontal :vendorCode="'400521'"></product-card-horizontal>
            </template>
            <template v-slot:slide7>
               <product-card-horizontal :vendorCode="'400522'"></product-card-horizontal>
            </template>
            <template v-slot:slide8>
               <product-card-horizontal :vendorCode="'400523'"></product-card-horizontal>
            </template>
            <template v-slot:slide9>
               <product-card-horizontal :vendorCode="'400524'"></product-card-horizontal>
            </template>
         </mobile-slider>
         <div class="page-reference__button">
            <a
               href="../html-main/catalogue.html"
               class="button button--colored-bg"
            >Смотреть больше моделей</a>
         </div>
      </section>
      <section class="main-page__block tab-spoiler tab-spoiler--colored-bg">
         <div class="tab-spoiler__title title">
            <div class="title__top">Что стоит знать</div>
            <div class="title__bottom title__bold">при выборе наушников</div>
         </div>
         <spoiler-tab-wrapper
            class="container"
            :titles="[
            'Есть ли гарантии?', 'Сколько устройств можно подключить?', 'Какой провод использовать?',
            ]"
            :spoilerParams="{
               isAccordeon: true
            }"
         >
            <template #0>
               <h5>Гарантия на всю продукцию - 3 месяца</h5>
               <p>Есть много вариантов Lorem Ipsum, но большинство из них имеет не всегда приемлемые модификации, например, юмористические вставки или слова, которые даже отдалённо не напоминают латынь.</p>
               <p>Если вам нужен Lorem Ipsum для серьёзного проекта, вы наверняка не хотите какой-нибудь шутки, скрытой в середине абзаца.</p>
            </template>
            <template #1>
               <h5>Возможно подключение только одного устройства</h5>
               <p>Есть много вариантов Lorem Ipsum, но большинство из них имеет не всегда приемлемые модификации, например, юмористические вставки или слова, которые даже отдалённо не напоминают латынь.</p>
               <p>Если вам нужен Lorem Ipsum для серьёзного проекта, вы наверняка не хотите какой-нибудь шутки, скрытой в середине абзаца.</p>
               <p>Есть много вариантов Lorem Ipsum, но большинство из них имеет не всегда приемлемые модификации, например, юмористические вставки или слова, которые даже отдалённо не напоминают латынь.</p>
               <p>Если вам нужен Lorem Ipsum для серьёзного проекта, вы наверняка не хотите какой-нибудь шутки, скрытой в середине абзаца.</p>
            </template>
            <template #2>
               <p>Есть много вариантов Lorem Ipsum, но большинство из них имеет не всегда приемлемые модификации, например, юмористические вставки или слова, которые даже отдалённо не напоминают латынь.</p>
               <p>Если вам нужен Lorem Ipsum для серьёзного проекта, вы наверняка не хотите какой-нибудь шутки, скрытой в середине абзаца.</p>
            </template>
         </spoiler-tab-wrapper>
      </section>
      <section class="main-page__block advantages-block">
         <div class="advantages-block__title title">
            <div class="title__top">Где выгодно купить качественные наушники?</div>
            <div
               class="title__bottom title__bold"
            >Интернет-магазин AudioFree - магазин хороших покупок</div>
            <div class="advantages-block__message message">
               <div class="message__text">
                  Наушники подбираются определенным образом: ассортимент формируется исходя из спроса и
                  новинок рынка, с учетом требований к качеству и по наличию популярных, топовых,
                  классических
                  позиций
               </div>
            </div>
         </div>
         <div class="advantages-block__container container">
            <div class="advantages-block__list cards-list">
               <info-card>
                  <template v-slot:title>Доставка 1-2 дня</template>
                  <template v-slot:body>
                     <p>Команда "AudioFree" быстро соберет и доставит Вашу посылку - от 1 до 2 дней по всей Росcии в зависимости от адреса</p>
                  </template>
                  <template v-slot:image>
                     <img src="img/info-cards/delivery.svg" alt="delivery" />
                  </template>
               </info-card>
               <info-card>
                  <template v-slot:title>Предоплата 0 руб.</template>
                  <template v-slot:body>
                     <p>Никаких предоплат при доставке по Москве и Санкт-Петербургу - платите при получении, когда удостоверитесь в качестве покупки</p>
                  </template>
                  <template v-slot:image>
                     <img src="img/info-cards/money.svg" alt="money" />
                  </template>
               </info-card>
               <info-card>
                  <template v-slot:title>0 рисков при покупке</template>
                  <template v-slot:body>
                     <p>При покупке в "AudioFree" Вы обязательно получите чек и гарантийный талон. Мы всегда соблюдаем все права потребителей</p>
                  </template>
                  <template v-slot:image>
                     <img src="img/info-cards/shopping.svg" alt="shopping">
                  </template>
               </info-card>
            </div>
         </div>
      </section>
      <section class="main-page__block offer-block offer-block--reversed consult-offer">
         <div class="offer-block__container consult-offer__container container">
            <div class="offer-block__message consult-offer__message message">
               <div class="consult-offer__title message__title">
                  Персональный
                  <span class="message__bold">Консультант на связи!</span>
               </div>
               <div class="consult-offer__text message__text">
                  Когда Вы совершаете покупки на сайте “AudioFree” - все Ваши заказы ведет личный
                  менеджер,
                  который поможет с выбором и оформлением заказа.
               </div>
               <div
                  class="consult-offer__text message__text message__text--underlined message__bold"
               >Приятных и безопасных покупок!</div>
            </div>
            <div class="offer-block__button consult-offer__button">
               <div class="button button--colored-bg">Получить консультацию</div>
            </div>
            <div class="offer-block__pictures consult-offer__pictures">
               <div class="offer-block__background consult-offer__background ellipse"></div>
               <div class="offer-block__image consult-offer__image">
                  <img src="img/main-page/consult-picture.png" alt />
               </div>
            </div>
         </div>
      </section>
   </main>
</template>

<script>
import MobileSlider from "@/components/mobile-slider/MobileSlider.vue";
import DevicesToggle from "@/components/misc/DevicesToggle.vue";
import SpoilerTabWrapper from "@/components/spoiler-tab/SpoilerTabWrapper.vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Pagination } from "swiper/modules";
import { EffectFlip } from "swiper/modules";
import "swiper/css/bundle";

export default {
   components: {
      Swiper,
      SwiperSlide,
      MobileSlider,
      DevicesToggle,
      SpoilerTabWrapper,
   },
   setup() {
      return {
         modules: [Pagination, EffectFlip],
      };
   },
};
</script>