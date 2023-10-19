<template>
    <footer class="footer">
        <div class="footer__body">
            <div class="container">
                <div class="footer__column footer__column--personal">
                    <h5 class="footer__column-title">
                        Личный кабинет
                    </h5>
                    <ul class="footer__column-list">
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Зарегистрироваться
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Войти в аккаунт
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Отложенные товары
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Ваши заказы
                            </RouterLink>
                        </li>
                    </ul>
                </div>
                <div class="footer__column footer__column--categories">
                    <h5 class="footer__column-title">
                        Категории
                    </h5>
                    <ul class="footer__column-list">
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                TRUE WIRELESS
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Гарнитуры
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Накладные
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                С проводом
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                С шейным ободком
                            </RouterLink>
                        </li>
                    </ul>
                </div>
                <div class="footer__column footer__column--shop">
                    <h5 class="footer__column-title">
                        Магазин
                    </h5>
                    <ul class="footer__column-list">
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                О компании
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Доставка и оплата
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Гарантия и возврат
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Пункты самовывоза
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Отзывы
                            </RouterLink>
                        </li>
                        <li class="footer__column-list-item">
                            <RouterLink class="link" :to="{ name: 'Home' }">
                                Контакты
                            </RouterLink>
                        </li>
                    </ul>
                </div>
                <div class="footer__column footer__column--contacts">
                    <h5 class="footer__column-title">
                        Контакты
                    </h5>
                    <ul class="footer__column-list footer__contacts">
                        <li class="footer__contacts-item">
                            <div class="contact-block">
                                <a class="circle-wrapper" href="tel:81111111">
                                    <PhoneCallIcon></PhoneCallIcon>
                                </a>
                                <div class="contact-block__text">
                                    <div class="contact-block__top-text">
                                        Бесплатный звонок по РФ
                                    </div>
                                    <div class="contact-block__value contact-block__value--bigger">
                                        <a class="link" href="tel:81111111">8 111 111-11-11</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="footer__contacts-item">
                            <div class="contact-block">
                                <div class="circle-wrapper circle-wrapper--not-interactive">
                                    <LocationIcon></LocationIcon>
                                </div>
                                <div class="contact-block__text">
                                    <div class="contact-block__top-text">
                                        Санкт-Петербург,
                                    </div>
                                    <div class="contact-block__value">
                                        Колотуева, 1756
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="footer__contacts-item">
                            <div class="contact-block">
                                <div class="circle-wrapper circle-wrapper--not-interactive">
                                    <MailIcon></MailIcon>
                                </div>
                                <div class="contact-block__text">
                                    <div class="contact-block__top-text">
                                        По всем вопросам пишите:
                                    </div>
                                    <div class="contact-block__value">
                                        <a href="mailto:kotbarsik96@mail.ru">kotbarsik96@mail.ru</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer__bottom">
            <div class="container">
                <div class="footer__bottom-item footer__bottom-item--url">
                    <a class="link" href="https://audiofree.kotbarsik96.ru">
                        audiofree.kotbarsik96.ru
                    </a>
                </div>
                <div class="footer__bottom-item footer__bottom-item--logo">
                    <div class="logo">
                        <RouterLink class="logo__text" :to="{ name: 'Home' }">
                            <HeadphonesIcon></HeadphonesIcon>
                            <span class="logo__title">AudioFree</span>
                        </RouterLink>
                    </div>
                </div>
                <div class="footer__bottom-item footer__bottom-item--conf-policy">
                    <a class="link" href="#">Политика конфиденциальности</a>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>

export default {
    name: 'PageFooter',
}
</script>

<style lang="scss">
.footer {
    color: #fff;

    &__body {
        background-color: var(--theme_color_darker);
        padding: 70px 0 50px 0;

        .container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
    }

    &__column {
        flex: 0 0 205px;
        margin-right: 20px;

        &:last-child {
            margin-right: 0;
        }
    }

    &__column--contacts {
        flex: 0 0 240px;
    }

    &__column-title {
        font-size: 18px;
        line-height: 21px;
        font-weight: 500;
        margin-bottom: 50px;
    }

    &__column-list-item {
        font-size: 14px;
        line-height: 24px;
    }

    &__contacts-item {
        margin-bottom: 15px;

        .contact-block {
            .circle-wrapper {
                background-color: #97D413;
            }
        }
    }

    &__bottom {
        background-color: #180e35;

        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    }

    .contact-block {

        &__text,
        &__value {
            color: #fff;
        }
    }

    @media (max-width: 992px) {
        &__body {
            padding-bottom: 10px;

            .container {
                justify-content: center;
            }
        }

        &__column {
            margin-left: 10px;
            margin-right: 10px;
            margin-bottom: 40px;
            flex: 0 0 240px;
        }

        &__column-title {
            margin-bottom: 20px;
        }
    }

    @media (max-width: 579px) {
        &__body {
            padding-top: 40px;
            padding-bottom: 0;

            .container {
                flex-direction: column;
                align-items: center;
                padding: 0;
            }
        }

        &__column {
            width: 100%;
            margin-left: 0;
            margin-right: 0;
            flex: 1 1 auto;
            padding: 0 10px 30px 10px;
            margin-bottom: 30px;
            border-bottom: 1px solid #383050;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        &__column--contacts {
            margin-bottom: 0;
        }

        &__column--personal,
        &__column--categories {
            display: none;
        }

        &__column-title {
            margin-bottom: 15px;
        }

        &__contacts-item {
            display: flex;
            align-items: center;
            justify-content: center;

            .circle-wrapper {
                display: none;
            }
        }

        &__bottom {
            padding-bottom: 45px;

            .container {
                flex-wrap: wrap;
                justify-content: center;
                text-align: center;
                padding: 0;
            }
        }

        &__bottom-item {
            padding: 10px;
            font-size: 14px;
        }

        &__bottom-item--conf-policy {
            order: 1;
            border-bottom: 1px solid #383050;
            flex: 0 0 100%;
            padding: 10px 0;
        }

        &__bottom-item--url {
            order: 2;
        }

        &__bottom-item--logo {
            display: none;
        }
    }
}
</style>