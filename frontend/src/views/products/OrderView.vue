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
                        <TextInputWrapper name="name" id="name" placeholder="Имя" v-model="input.name">
                            <template v-if="errors.name" v-slot:error>
                                {{ errors.name[0] }}
                            </template>
                        </TextInputWrapper>
                        <TextInputWrapper name="email" id="email" placeholder="Email" v-model="input.email">
                            <template v-if="errors.email" v-slot:error>
                                {{ errors.email[0] }}
                            </template>
                        </TextInputWrapper>
                        <TextInputWrapper name="phone_number" id="phone_number" placeholder="Телефон"
                            v-model="input.phone_number" mask="phone">
                            <template v-if="errors.phone_number" v-slot:error>
                                {{ errors.phone_number[0] }}
                            </template>
                        </TextInputWrapper>
                        <TextInputWrapper name="location" id="location" placeholder="Город" v-model="input.location">
                            <template v-if="errors.location" v-slot:error>
                                {{ errors.location[0] }}
                            </template>
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
                            <li class="order-page__radio-item" v-for="deliveryType in deliveryTypes" :key="deliveryType.id">
                                <RadioLabel name="delivery_type" v-model="input.delivery_type" :value="deliveryType.name"
                                    :checked="input.delivery_type === deliveryType.name" ref="deliveryTypeInput">
                                    {{ deliveryType.title }}
                                </RadioLabel>
                            </li>
                            <Transition name="grow">
                                <li class="error" v-if="errors.delivery_type">
                                    {{ errors.delivery_type[0] }}
                                </li>
                            </Transition>
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
                                <li class="order-page__radio-item" v-for="paymentType in paymentTypes"
                                    :key="paymentType.id">
                                    <RadioLabel name="payment_type" :value="paymentType.name" v-model="input.payment_type"
                                        :checked="input.payment_type === paymentType.name" ref="paymentTypeInput">
                                        {{ paymentType.title }}
                                    </RadioLabel>
                                </li>
                                <Transition name="grow">
                                    <li class="error" v-if="errors.payment_type">
                                        {{ errors.payment_type[0] }}
                                    </li>
                                </Transition>
                            </ul>
                        </div>
                    </div>
                    <div class="order-page__section-half order-page__total">
                        <div class="order-page__total-position">
                            <h5 class="order-page__total-name">
                                Сумма заказа:
                            </h5>
                            <div class="order-page__total-value">
                                {{ orderData.total_price.toLocaleString() }} ₽
                            </div>
                        </div>
                    </div>
                </div>
                <Transition name="grow">
                    <div class="order-page__error error" v-if="error">
                        {{ error }}
                    </div>
                </Transition>
            </div>
            <div class="order-page__bottom">
                <button class="checkout-button button button--colored" type="submit" @click.prevent="checkout">
                    Оформить заказ
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import TextareaWrapper from '@/components/inputs/TextareaWrapper.vue'
import { useIndexStore } from '@/stores/'
import { useModalsStore } from '@/stores/modals.js'
import { useNotificationsStore } from '@/stores/notifications.js'
import axios from 'axios'
import { handleAjaxError } from '@/assets/js/scripts.js'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import { h } from 'vue'

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
                comment: '',
                address: '',
                delivery_type: '',
                payment_type: ''
            },
            deliveryTypes: [],
            paymentTypes: [],
            error: '',
            errors: []
        }
    },
    computed: {
        orderData() {
            return this.$route.meta.orderData
        }
    },
    methods: {
        updateInputs() {
            if (!this.orderData)
                return

            this.input.name = this.orderData.name
            this.input.email = this.orderData.email
            this.input.phone_number = this.orderData.phone_number
            this.input.location = this.orderData.location
            this.input.address = this.orderData.address
        },
        async loadOrderTypes() {
            const store = useIndexStore()
            store.toggleLoading('loadOrderTypes', true)

            try {
                const link = import.meta.env.VITE_ORDER_TYPES_ALL
                const res = await axios.get(link)
                if (res.data.delivery_types)
                    this.deliveryTypes = res.data.delivery_types
                if (res.data.payment_types)
                    this.paymentTypes = res.data.payment_types
            } catch (err) {
            }

            store.toggleLoading('loadOrderTypes', false)

            await this.$nextTick()
            if (this.deliveryTypes.length > 0 && !this.input.delivery_type) {
                this.input.delivery_type = this.deliveryTypes[0].name
            }
            if (this.paymentTypes.length > 0 && !this.input.payment_type) {
                this.input.payment_type = this.paymentTypes[0].name
            }
        },
        async checkout() {
            try {
                const link = `${import.meta.env.VITE_ORDER_CHECKOUT}${this.orderData.id}`
                const data = Object.assign({}, this.input)
                data.phone_number = this.input.phone_number.replace(/[^\+0-9]/g, '')
                const res = await axios.post(link, data)
                if (!res.data.order)
                    throw new Error()

                if (res.data.pay_after_delivery)
                    this.createCheckoutSuccessModal()
                else
                    this.createPaymentModal(res.data.order)
            } catch (err) {
                handleAjaxError(err, this)
            }
        },
        createPaymentModal(order) {
            const callback = async () => {
                const link = `${import.meta.env.VITE_ORDER_CONFIRM_PAYMENT}${this.orderData.id}`

                try {
                    const res = await axios.post(link)
                    if (res.data && res.data.paid > 0)
                        this.createCheckoutSuccessModal()
                    else
                        throw new Error()
                } catch (err) {
                    useNotificationsStore().addNotification({
                        message: 'Произошла ошибка',
                        timeout: 5000
                    })
                }
            }
            const component = h(ConfirmModal, {
                title: `К оплате: ${order.total_price} ₽`,
                text: 'Оплатить? (после нажатия кнопки произойдет имитация оплаты)',
                confirmProps: {
                    text: 'Оплатить',
                    callback
                },
                declineProps: {
                    text: 'Вернуться'
                }
            })

            useModalsStore().addModal({ component })
        },
        createCheckoutSuccessModal() {
            const store = useIndexStore()
            store.loadEntity('cart')

            const callback = () => {
                this.$router.push({ name: 'Home' })
            }
            const component = h(ConfirmModal, {
                onlyConfirm: true,
                title: 'Заказ успешно оформлен',
                confirmProps: {
                    text: 'Вернуться на сайт',
                    callback
                }
            })
            useModalsStore().addModal({ component })
        }
    },
    watch: {
        orderData() {
            this.updateInputs()
        }
    },
    created() {
        this.loadOrderTypes()
    },
    mounted() {
        this.updateInputs()
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

    &__radios {
        .error {
            margin-bottom: 20px;
        }
    }

    &__radio-item {
        margin-bottom: 25px;

        .radio {
            display: inline-flex;
        }
    }

    &__section-half {
        &:first-child {
            border-bottom: 1px solid var(--border_color);
            padding-bottom: 70px;
            margin-bottom: 50px;
        }
    }

    &__total {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 80px;
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
                padding-bottom: 5px;
            }
        }

        &__total {
            min-height: 50px;
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