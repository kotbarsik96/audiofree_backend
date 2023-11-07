<template>
    <div class="order-page">
        <div class="container">
            <div class="order-page__page-heading page-heading">
                <div class="breadcrumbs">
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Home' }">
                        Главная
                    </RouterLink>
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Cart' }">
                        Корзина
                    </RouterLink>
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Order', params: { id: $route.params.id } }">
                        Оформить заказ
                    </RouterLink>
                </div>
                <h1 class="page-title">
                    Оформить заказ
                </h1>
            </div>
            <div class="order-page__body">
                <div class="order-page__section">
                    <h3 class="order-page__section-title">
                        <span>01</span>
                        <span>Личные данные</span>
                    </h3>
                    <div class="order-page__section-inputs">
                        <TextInputWrapper name="name" id="name" placeholder="Имя" v-model="input.name"></TextInputWrapper>
                        <TextInputWrapper name="email" id="email" placeholder="Email" v-model="input.email">
                        </TextInputWrapper>
                        <TextInputWrapper name="phone_number" id="phone_number" placeholder="Телефон"
                            v-model="input.phone_number" mask="phone"></TextInputWrapper>
                        <TextInputWrapper name="location" id="location" placeholder="Город" v-model="input.location">
                        </TextInputWrapper>
                        <TextareaWrapper name="comment" placeholder="Комментарий к заказу" v-model="input.comment">
                        </TextareaWrapper>
                    </div>
                </div>
                <div class="order-page__section">
                    <h3 class="order-page__section-title">
                        <span>02</span>
                        <span>Доставка</span>
                    </h3>
                    <div class="order-page__section-inputs">
                        <ul class="order-page__radios">
                            <!-- <li class="order-page__radio-item" v-for="deliveryType in deliveryTypes" :key="deliveryType.id" :value="deliveryType.name">
                                <RadioLabel name="delivery_type" v-model="input.delivery_type">
                                    {{ deliveryType.title }}
                                </RadioLabel>
                            </li> -->
                            <li class="order-page__radio-item">
                                <RadioLabel name="delivery_type" v-model="input.delivery_type"
                                    value="Доставка в пределах города">
                                    Доставка в пределах города
                                </RadioLabel>
                            </li>
                            <li class="order-page__radio-item">
                                <RadioLabel name="delivery_type" v-model="input.delivery_type"
                                    value="Доставка за пределы города">
                                    Доставка за пределы города
                                </RadioLabel>
                            </li>
                            <li class="order-page__radio-item">
                                <RadioLabel name="delivery_type" v-model="input.delivery_type" value="Самовывоз">
                                    Самовывоз
                                </RadioLabel>
                            </li>
                            <li class="order-page__radio-item">
                                <RadioLabel name="delivery_type" v-model="input.delivery_type" value="Экспресс доставка">
                                    Экспресс доставка
                                </RadioLabel>
                            </li>
                        </ul>
                        <TextareaWrapper name="delivery_address" placeholder="Адрес доставки" v-model="input.address">
                        </TextareaWrapper>
                    </div>
                </div>
                <div class="order-page__section order-page__section--halved">
                    <div class="order-page__section-half">
                        <h3 class="order-page__section-title">
                            <span>03</span>
                            <span>Оплата</span>
                        </h3>
                        <div class="order-page__section-inputs">
                            <ul class="order-page__radios">
                                <li class="order-page__radio-item" v-for="paymentType in paymentTypes" :key="paymentType.id"
                                    :value="paymentType.name">
                                    <RadioLabel name="payment_type" v-model="input.payment_type">
                                        {{ paymentType.title }}
                                    </RadioLabel>
                                </li>
                                <li class="order-page__radio-item">
                                    <RadioLabel name="payment_type" v-model="input.payment_type" value="Оплата наличными">
                                        Оплата наличными
                                    </RadioLabel>
                                </li>
                                <li class="order-page__radio-item">
                                    <RadioLabel name="payment_type" v-model="input.payment_type"
                                        value="Оплата банковской картой">
                                        Оплата банковской картой
                                    </RadioLabel>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="order-page__section-half order-page__total">
                        <div class="order-page__total-position">
                            <h5 class="order-page__total-name">
                                Сумма заказа:
                            </h5>
                            <div class="order-page__total-value">
                                15 600 ₽
                            </div>
                        </div>
                        <div class="order-page__total-position">
                            <h5 class="order-page__total-name">
                                Сумма доставки:
                            </h5>
                            <div class="order-page__total-value">
                                0 ₽
                            </div>
                        </div>
                        <div class="order-page__total-position">
                            <h5 class="order-page__total-name">
                                Итого:
                            </h5>
                            <div class="order-page__total-value">
                                15 600 ₽
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="order-page__bottom">
                <button class="checkout-button button button--colored" type="submit" @click.prevent>
                    Оформить заказ
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import TextareaWrapper from '@/components/inputs/TextareaWrapper.vue'

export default {
    name: 'OrderView',
    components: {
        TextInputWrapper,
        TextareaWrapper
    },
    data() {
        return {
            input: {
                name: '',
                email: '',
                phone_number: '',
                location: '',
                comment: ''
            },
            deliveryTypes: [],
            paymentTypes: [],
        }
    }
}
</script>

<style lang="scss">
.order-page {
    --border_color: #dedede;

    padding: 70px 0 45px 0;

    &__page-heading {
        margin-bottom: 50px;
    }

    &__body {
        border-top: 1px solid var(--border_color);
        border-bottom: 1px solid var(--border_color);
        padding: 50px 0 55px 0;
        display: flex;
    }

    &__section {
        padding: 20px 45px;
        border-right: 1px solid var(--border_color);
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        &:first-child {
            padding-left: 0;
            flex: 1 1 30%;
        }

        &:nth-child(2) {
            flex: 1 1 34%;
        }

        &:last-child {
            padding-right: 0px;
            border-right-width: 0px;
            flex: 1 1 37%;
        }
    }

    &__section-title {
        display: flex;
        align-items: center;
        font-size: 20px;
        line-height: 24px;
        color: #444;
        margin-bottom: 45px;

        span {
            display: inline-block;
        }

        span:first-child {
            color: var(--theme_color);
            font-size: 30px;
            line-height: 34px;
            margin-right: 10px;
        }
    }

    &__section-inputs {
        .text-input {
            width: 100%;

            &:not(:last-child) {
                margin-bottom: 11px;
            }
        }
    }

    &__radio-item {
        margin-bottom: 25px;
    }

    &__section-half {
        &:first-child {
            border-bottom: 1px solid var(--border_color);
            padding-bottom: 70px;
            margin-bottom: 50px;
        }
    }

    &__total-position {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;

        &:last-child {
            margin-bottom: 0;
        }
    }

    &__total-name,
    &__total-value {
        font-weight: 700;
        font-size: 14px;
        line-height: 16px;
    }

    &__total-name {
        flex: 1 1 auto;
    }

    &__total-value {
        flex: 0 0 30%;
        display: flex;
        justify-content: flex-end;
    }

    &__bottom {
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 949px) {
        padding: 25px 0 60px 0;
    }

    @media (max-width: 899px) {
        &__body {
            flex-wrap: wrap;
            padding: 30px 0 20px 0;
        }

        &__section:first-child,
        &__section:nth-child(2),
        &__section:nth-child(3) {
            flex: 0 0 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-left: 0;
            padding-right: 0;
            width: 100%;
            border-right-width: 0px;
            border-bottom: 1px solid var(--border_color);
        }

        &__section:first-child {
            padding-top: 0;
        }

        &__section:last-child {
            border-bottom-width: 0px;
            padding-bottom: 0;
        }

        &__section-inputs {
            width: 400px;
            display: flex;
            flex-direction: column;

            .text-input {
                max-width: 400px;
            }
        }

        &__section-title {
            width: 100%;
        }

        &__section-half {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;

            &:first-child {
                margin-bottom: 25px;
            }
        }

        &__total-position {
            width: 400px;
            max-width: 100%;
        }
    }

    @media (max-width: 459px) {
        &__section-inputs {
            width: 100%;
        }
    }
}
</style>