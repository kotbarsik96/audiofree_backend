<template>
    <main class="cart-page">
        <div class="cart-page__container container">
            <section class="page-title">
                <div class="page-title__links">
                    <router-link
                        :to="{ name: 'home' }"
                        class="page-title__arrow-back __icon-back-arrow"
                    ></router-link>
                    <router-link
                        :to="{ name: 'home' }"
                        class="page-title__link-back"
                        >Главная</router-link
                    >
                    <router-link
                        :to="{ name: cartType }"
                        class="page-title__link-back"
                        >Корзина</router-link
                    >
                </div>
                <div class="page-title__title">Корзина</div>
            </section>
            <section class="cart-page__body">
                <div class="cart-list">
                    <div
                        class="cart-list__titles ctlt-titles"
                        v-if="!cartIsEmpty"
                    >
                        <div class="cart-list__title ctlt-titles__image"></div>
                        <div class="cart-list__title ctlt-titles__name">
                            Название товара:
                        </div>
                        <div class="cart-list__title ctlt-titles__price">
                            Цена за штуку:
                        </div>
                        <div class="cart-list__title ctlt-titles__amount">
                            Количество:
                        </div>
                        <div class="cart-list__title ctlt-titles__total">
                            Сумма:
                        </div>
                        <div class="cart-list__title ctlt-titles__cancel"></div>
                    </div>
                    <transition-group
                        name="cart-list"
                        :tag="'div'"
                        class="cart-list__container"
                    >
                        <div class="__isempty" v-if="cartIsEmpty">
                            Корзина пуста.
                            <router-link to="/catalogue"
                                >Подобрать что-нибудь</router-link
                            >
                        </div>
                        <div
                            class="cart-list__item ctlt-item"
                            v-for="prod in cartProducts"
                            :key="prod.id"
                        >
                            <div class="ctlt-item__image ctlt-item__item">
                                <router-link
                                    :to="'/products/' + prod.vendorCode"
                                >
                                    <img
                                        :src="
                                            rootPath +
                                            'img/products/' +
                                            getProd(prod.vendorCode).images[0]
                                        "
                                        alt
                                    />
                                </router-link>
                            </div>
                            <div class="ctlt-item__name ctlt-item__item">
                                <router-link
                                    :to="'/products/' + prod.vendorCode"
                                    >{{ getProd(prod.vendorCode).name }} ({{
                                        prod.color.toLowerCase()
                                    }})</router-link
                                >
                            </div>
                            <div class="ctlt-item__price ctlt-item__item">
                                <div class="cart-list__title">
                                    Цена за штуку:
                                </div>
                                <p>
                                    {{
                                        getProd(
                                            prod.vendorCode
                                        ).price.toLocaleString()
                                    }}
                                    ₽
                                </p>
                            </div>
                            <div
                                class="
                                    ctlt-item__amount ctlt-item__item
                                    controls
                                "
                            >
                                <div class="controls__amount">
                                    <div
                                        class="
                                            controls__amount--less
                                            controls__amount-button
                                            __icon-back-arrow
                                        "
                                        @click="
                                            changeAmount(
                                                $event,
                                                'less',
                                                prod.id
                                            )
                                        "
                                    ></div>
                                    <div class="controls__amount--number">
                                        <input
                                            type="text"
                                            :value="prod.amount"
                                            maxlength="2"
                                            @input="
                                                changeAmount(
                                                    $event,
                                                    'typing',
                                                    prod.id
                                                )
                                            "
                                        />
                                    </div>
                                    <div
                                        class="
                                            controls__amount--more
                                            controls__amount-button
                                            __icon-next-arrow
                                        "
                                        @click="
                                            changeAmount(
                                                $event,
                                                'more',
                                                prod.id
                                            )
                                        "
                                    ></div>
                                </div>
                            </div>
                            <div class="ctlt-item__total ctlt-item__item">
                                <div
                                    class="
                                        cart-list__title
                                        cart-list__title--colored
                                    "
                                >
                                    Итого:
                                </div>
                                <p>
                                    {{
                                        (
                                            getProd(prod.vendorCode).price *
                                            prod.amount
                                        ).toLocaleString()
                                    }}
                                    ₽
                                </p>
                            </div>
                            <div
                                class="ctlt-item__cancel ctlt-item__item"
                                @click="removeFromCart(prod.id, prod)"
                                v-if="cartType === 'cart'"
                            >
                                <div
                                    class="
                                        ctlt-item__cancel--icon
                                        __icon-cancel
                                    "
                                ></div>
                            </div>
                        </div>
                    </transition-group>
                </div>
                <div
                    class="cart-page__info"
                    :style="{ display: cartIsEmpty ? 'none' : '' }"
                >
                    <div class="cart-page__coupon">
                        <p>Введите Ваш код купона, если он у вас есть:</p>
                        <form class="text-input">
                            <div class="text-input__text">
                                <input type="text" placeholder="Введите код" />
                            </div>
                            <button class="text-input__submit">
                                <input type="button" value="Активировать" />
                            </button>
                        </form>
                    </div>
                    <div class="cart-total">
                        <div class="cart-total__text">Сумма заказа:</div>
                        <div class="cart-total__number" v-if="totalPrice">
                            {{ totalPrice.toLocaleString() }} ₽
                        </div>
                    </div>
                </div>
            </section>
            <section
                class="order-button"
                :style="{ display: cartIsEmpty ? 'none' : '' }"
            >
                <router-link
                    :to="{ name: orderRouteName }"
                    class="button button--colored-bg"
                    >Оформить заказ</router-link
                >
            </section>
        </div>
    </main>
</template>

<script>
import {
    lStorage,
    checkProductAmountCorrect,
    getProd,
    generateId,
} from "@/assets/js/scripts";

export default {
    name: "CartPage",
    data() {
        return {
            
        }
    },
};
</script>

<style>
.cart-list-enter-active,
.cart-list-leave-active {
    transition: all 0.5s;
}

.cart-list-enter-from,
.cart-list-leave-to {
    opacity: 0;
    max-height: 0;
    margin: 0;
    padding: 0;
}
.cart-list-enter-to,
.cart-list-leave-from {
    max-height: 500px;
}
</style>