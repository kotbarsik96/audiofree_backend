<!-- Многие элементы шапки из десктоп версии просто перемещаются в мобильную, а не дублируются, поэтому, если изменить элемент в десктоп версии, он будет изменен и в мобильной (работает это через DynamicAdaptive) -->

<template>
    <header class="header">
        <div v-show="isDesktop" class="header__top">
            <div class="container">
                <div class="header__logo logo">
                    <DynamicAdaptive :query="mobileMediaQuery" destinationSelector="#header-mobile-logo">
                        <RouterLink class="header__logo-main logo__text link" :to="{ name: 'Home' }">
                            <HeadphonesIcon></HeadphonesIcon>
                            <span class="header__logo-title logo__title">AudioFree</span>
                        </RouterLink>
                    </DynamicAdaptive>
                    <div class="header__logo-description">
                        Интернет магазин наушников по РФ
                    </div>
                </div>
                <nav class="header__top-nav">
                    <ul class="header__top-nav-list">
                        <li class="header__top-nav-item link">
                            <DynamicAdaptive destinationSelector="#header-mobile-shipment-link" :query="mobileMediaQuery">
                                <RouterLink :to="{ name: 'DeliveryPayment' }">Доставка и оплата</RouterLink>
                            </DynamicAdaptive>
                        </li>
                        <li class="header__top-nav-item link">
                            <DynamicAdaptive destinationSelector="#header-mobile-guarantee-link" :query="mobileMediaQuery">
                                <RouterLink :to="{ name: 'Warranty' }">Гарантия и возврат</RouterLink>
                            </DynamicAdaptive>
                        </li>
                        <li class="header__top-nav-item link">
                            <DynamicAdaptive destinationSelector="#header-mobile-contacts-link" :query="mobileMediaQuery">
                                <RouterLink :to="{ name: 'Contacts' }">Контакты</RouterLink>
                            </DynamicAdaptive>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div v-show="isDesktop" class="header__body">
            <div class="container">
                <div class="contact-block">
                    <DynamicAdaptive destinationSelector="#header-mobile-phone" :query="mobileMediaQuery">
                        <a class="circle-wrapper circle-wrapper--shadow circle-wrapper--adaptive" href="tel:81111111"
                            aria-label="Телефон">
                            <PhoneCallIcon></PhoneCallIcon>
                        </a>
                    </DynamicAdaptive>
                    <DynamicAdaptive destinationSelector="#header-mobile-phone-number" :query="mobileMediaQuery">
                        <div class="contact-block__text">
                            <div class="contact-block__top-text">
                                Бесплатный звонок по РФ
                            </div>
                            <div class="contact-block__value contact-block__value--bigger link">
                                <a href="tel:81111111" aria-label="8 111 111-11-11">
                                    8 111 111-11-11
                                </a>
                            </div>
                        </div>
                    </DynamicAdaptive>
                </div>
                <div class="header__search-container">
                    <DynamicAdaptive destinationSelector="#header-search-mobile" :query="mobileMediaQuery">
                        <HeaderSearchInput name="search-products" placeholder="Поиск товара"></HeaderSearchInput>
                    </DynamicAdaptive>
                </div>
                <nav class="header__body-nav">
                    <ul class="header__body-nav-list">
                        <li class="header__body-nav-item">
                            <DynamicAdaptive destinationSelector="#header-mobile-favorites" :query="mobileMediaQuery">
                                <RouterLink class="circle-wrapper circle-wrapper--shadow circle-wrapper--adaptive"
                                    :to="{ name: 'Favorites' }" aria-label="В избранное">
                                    <span class="circle-wrapper__number" v-if="favoritesCountComputed">
                                        {{ favoritesCountComputed }}
                                    </span>
                                    <HeartIcon></HeartIcon>
                                </RouterLink>
                            </DynamicAdaptive>
                        </li>
                        <li class="header__body-nav-item">
                            <DynamicAdaptive destinationSelector="#header-mobile-cart" :query="mobileMediaQuery">
                                <RouterLink :to="{ name: 'Cart' }"
                                    class="circle-wrapper circle-wrapper--shadow circle-wrapper--adaptive"
                                    aria-label="В корзину">
                                    <div class="circle-wrapper__number" v-if="cartCountComputed">
                                        {{ cartCountComputed }}
                                    </div>
                                    <CartIcon></CartIcon>
                                </RouterLink>
                            </DynamicAdaptive>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div v-show="isDesktop" class="header__bottom">
            <div class="container">
                <div class="header__bottom-section">
                    <DynamicAdaptive destinationSelector="#header-mobile-catalog-link" :query="mobileMediaQuery">
                        <RouterLink class="header__catalog-button link" :to="{ name: 'Catalog' }">
                            <BurgerIcon></BurgerIcon>
                            <span>
                                Каталог товаров
                            </span>
                        </RouterLink>
                    </DynamicAdaptive>
                </div>
                <nav class="header__bottom-nav">
                    <ul class="header__bottom-nav-list">
                        <li class="header__bottom-nav-item">
                            <DynamicAdaptive destinationSelector="#header-mobile-home-link" :query="mobileMediaQuery">
                                <RouterLink :to="{ name: 'Home' }" class="header__bottom-nav-link link">
                                    Главная
                                </RouterLink>
                            </DynamicAdaptive>
                        </li>
                        <li class="header__bottom-nav-item">
                            <DynamicAdaptive destinationSelector="#header-mobile-sales-link" :query="mobileMediaQuery">
                                <RouterLink :to="{ name: 'Home' }" class="header__bottom-nav-link link">
                                    Скидки
                                </RouterLink>
                            </DynamicAdaptive>
                        </li>
                        <li class="header__bottom-nav-item">
                            <DynamicAdaptive destinationSelector="#header-mobile-new-products-link"
                                :query="mobileMediaQuery">
                                <RouterLink :to="{ name: 'Home' }" class="header__bottom-nav-link link">
                                    Новинки
                                </RouterLink>
                            </DynamicAdaptive>
                        </li>
                        <li class="header__bottom-nav-item">
                            <DynamicAdaptive destinationSelector="#header-mobile-brands-link" :query="mobileMediaQuery">
                                <RouterLink :to="{ name: 'Home' }" class="header__bottom-nav-link link">
                                    Бренды
                                </RouterLink>
                            </DynamicAdaptive>
                        </li>
                        <li v-if="isAdmin" class="header__bottom-nav-item">
                            <DynamicAdaptive destinationSelector="#header-mobile-admin-link" :query="mobileMediaQuery">
                                <RouterLink :to="{ name: 'Admin' }" class="header__bottom-nav-link link">
                                    Админка
                                </RouterLink>
                            </DynamicAdaptive>
                        </li>
                    </ul>
                </nav>
                <div class="header__bottom-section header__auth">
                    <UserIcon></UserIcon>
                    <DynamicAdaptive destinationSelector="#header-mobile-auth-buttons" :query="mobileMediaQuery">
                        <span class="header__auth-text" v-if="isUserLogged">
                            <RouterLink :to="{ name: 'Account' }" class="link">
                                Профиль
                            </RouterLink>
                            /
                            <button class="link" type="button" @click="openConfirmLogoutModal">
                                Выход
                            </button>
                        </span>
                        <span class="header__auth-text" v-else>
                            <button class="link" type="button" @click="openAuthModal('login')">
                                Вход
                            </button>
                            /
                            <button class="link" type="button" @click="openAuthModal('register')">
                                Регистрация
                            </button>
                        </span>
                    </DynamicAdaptive>
                </div>
            </div>
        </div>
        <div v-show="!isDesktop" class="header__mobile" :class="{ '__shown-menu': isShownMobileMenu }">
            <div class="header__mobile-top">
                <div class="header__mobile-burger" @click="isShownMobileMenu = !isShownMobileMenu">
                    <BurgerIcon></BurgerIcon>
                </div>
                <div class="header__mobile-search" id="header-search-mobile"></div>
                <nav class="header__mobile-top-nav">
                    <div class="header__mobile-top-logo" id="header-mobile-logo"></div>
                    <div class="header__mobile-top-icon" id="header-mobile-phone"></div>
                    <div class="header__mobile-top-icon" id="header-mobile-cart"></div>
                    <div class="header__mobile-top-icon" id="header-mobile-favorites"></div>
                </nav>
            </div>
            <div class="header__mobile-menu">
                <ul class="header__mobile-menu-list">
                    <li class="header__mobile-menu-item header__mobile-menu-item--back">
                        <ChevronIcon></ChevronIcon>
                        <button class="header__mobile-menu-item-text fw-700">
                            Меню
                        </button>
                    </li>
                    <li class="header__mobile-menu-item header__mobile-menu-item--bolder">
                        <HeadphonesIcon></HeadphonesIcon>
                        <div class="header__mobile-menu-item-text">
                            <div id="header-mobile-home-link"></div>
                        </div>
                    </li>
                    <li class="header__mobile-menu-item header__mobile-menu-item--bolder">
                        <BurgerIcon></BurgerIcon>
                        <div class="header__mobile-menu-item-text">
                            <div id="header-mobile-catalog-link"></div>
                        </div>
                    </li>
                    <li class="header__mobile-menu-item header__mobile-menu-item--bolder">
                        <PercentIcon></PercentIcon>
                        <div class="header__mobile-menu-item-text">
                            <div id="header-mobile-sales-link"></div>
                        </div>
                    </li>
                    <li class="header__mobile-menu-item header__mobile-menu-item--bolder">
                        <UserIcon></UserIcon>
                        <div class="header__mobile-menu-item-text">
                            <div id="header-mobile-auth-buttons"></div>
                        </div>
                    </li>
                    <li class="header__mobile-menu-item header__mobile-menu-item--bolder">
                        <UserIcon></UserIcon>
                        <div class="header__mobile-menu-item-text">
                            <div id="header-mobile-admin-link"></div>
                        </div>
                    </li>
                    <li class="header__mobile-menu-item">
                        <div id="header-mobile-new-products-link"></div>
                    </li>
                    <li class="header__mobile-menu-item">
                        <div class="header__mobile-menu-item-text">
                            <div id="header-mobile-brands-link"></div>
                        </div>
                    </li>
                    <li class="header__mobile-menu-item">
                        <div class="header__mobile-menu-item-text">
                            <div id="header-mobile-shipment-link"></div>
                        </div>
                    </li>
                    <li class="header__mobile-menu-item">
                        <div class="header__mobile-menu-item-text">
                            <div id="header-mobile-guarantee-link"></div>
                        </div>
                    </li>
                    <li class="header__mobile-menu-item">
                        <div class="header__mobile-menu-item-text">
                            <div id="header-mobile-contacts-link"></div>
                        </div>
                    </li>
                    <li class="header__mobile-menu-item header__mobile-menu-item--phone">
                        <div class="header__mobile-menu-item-text">
                            <div id="header-mobile-phone-number"></div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
</template>

<script>
import { mapState } from 'pinia'
import { useIndexStore } from '@/stores/'
import HeaderSearchInput from '@/components/inputs/HeaderSearchInput.vue'
import DynamicAdaptive from '@/components/misc/DynamicAdaptive.vue'
import { openAuthModal, openConfirmLogoutModal, logout } from '@/assets/js/methods.js'

export default {
    name: 'PageHeader',
    components: {
        DynamicAdaptive,
        HeaderSearchInput
    },
    data() {
        return {
            mobileMediaQuery: '(max-width: 949px)',
            mobileMedia: null,
            isDesktop: false,
            isShownMobileMenu: false,
        }
    },
    computed: {
        ...mapState(useIndexStore, ['isUserLogged', 'isAdmin', 'favoritesCount', 'cartCount']),
        isLogged() {
            return true
        },
        cartCountComputed() {
            const value = parseInt(this.cartCount)
            if (value <= 0 || isNaN(value))
                return null

            if (value <= 9)
                return value

            return '9+'
        },
        favoritesCountComputed() {
            const value = parseInt(this.favoritesCount)
            if (value <= 0 || isNaN(value))
                return null

            if (value <= 9)
                return value

            return '9+'
        }
    },
    methods: {
        openAuthModal,
        openConfirmLogoutModal,
        logout,
        onMediaChange() {
            if (!this.mobileMedia)
                return

            if (this.mobileMedia.matches)
                this.isDesktop = false
            else
                this.isDesktop = true
        },
    },
    watch: {
        $route(to, from) {
            this.isShownMobileMenu = false
        }
    },
    created() {
        this.mobileMedia = window.matchMedia(this.mobileMediaQuery)
        this.mobileMedia.addEventListener('change', this.onMediaChange)
        this.onMediaChange()
    },
}
</script>

<style lang="scss">
.header {
    --hover_color: #97d413;
    z-index: 150;

    &__body,
    &__bottom {
        background-color: #fff;
    }

    &__top {
        background-color: var(--theme_color_darker);
        color: #fff;

        .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    }

    &__logo-main {
        font-weight: 700;
        font-size: 18px;
        color: #fff;
        display: flex;
        align-items: center;
        transition: color .2s;

        svg {
            margin-right: 10px;
        }

        &:hover {
            color: var(--hover_color);
        }
    }

    &__logo-title {
        display: inline-block;

        &::after {
            content: "|";
            padding: 0 5px;
        }
    }

    &__logo-description {
        font-weight: 400;
        font-size: 13px;
        color: #fff;
    }

    &__top-nav-list {
        display: flex;
    }

    &__top-nav-item {
        margin: 0 15px;
        font-size: 13px;

        &:last-child {
            margin-right: 0;
        }
    }

    &__body {
        padding: 25px 0;
        position: relative;
        z-index: 15;

        .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    }

    &__search-container {
        width: 320px;
    }

    &__body-nav-list {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    &__body-nav-item {
        margin: 0 15px;
    }

    &__bottom {
        position: relative;
        z-index: 10;
        border-top: 1px solid #e7e7e7;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, .12);

        .container {
            display: flex;
            justify-content: space-between;
        }
    }

    &__bottom-section {
        border-left: 1px solid #e7e7e7;
        border-right: 1px solid #e7e7e7;
        padding: 0 15px;
        display: flex;
        align-items: center;
    }

    &__catalog-button {
        height: 100%;
        font-weight: 500;
        font-size: 18px;
        color: #474747;
        display: flex;
        align-items: center;

        svg {
            width: 19px;
            height: 17px;
            color: var(--theme_color_darker);
        }

        span {
            display: inline-block;
            margin-left: 15px;
        }
    }

    &__bottom-nav {
        flex: 1 1 auto;
    }

    &__bottom-nav-list {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
    }

    &__bottom-nav-item {
        padding: 0 20px;
    }

    &__bottom-nav-link {
        display: inline-block;
        position: relative;
        padding: 13px 0;

        &::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 0;
            background-color: var(--hover_color);
            border-radius: 10px;
            transition: height .2s ease;
        }
    }

    &__bottom-nav-link:hover {
        color: var(--hover_color);

        &::after {
            height: 3px;
            color: var(--hover_color);
            bottom: -1px;
        }
    }

    &__auth {
        padding: 0 60px;
    }

    &__auth-text {
        margin-left: 6px;

        button {
            display: inline;
        }
    }

    @media (max-width: 1209px) {
        &__bottom-section:first-child {
            border-left-width: 0;
            padding-left: 0;
        }

        &__bottom-section:last-child {
            border-right-width: 0;
            padding-right: 0;
        }

        &__auth {
            padding: 0 20px;
        }
    }

    @media (max-width: 992px) {
        &__logo-title {
            &::after {
                display: none;
            }
        }

        &__logo-description {
            display: none;
        }
    }
}

@media (max-width: 949px) {
    .header {
        position: relative;
        height: var(--header_height);

        &__mobile {
            position: fixed;
            width: 100%;
            z-index: 200;
            box-shadow: 0px 0px 11px rgba(0, 0, 0, .05);
        }

        &__mobile-top {
            position: relative;
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 0 0 var(--border_radius) var(--border_radius);
            padding: 13px 15px;
            transition: border-radius .3s;
        }

        .__shown-menu &__mobile-top {
            border-radius: 0 0 var(--border_radius) 0;
        }

        &__mobile-burger {
            width: 23px;
            height: 23px;
            transform: translateY(4px);
            margin-right: 16px;
        }

        &__mobile-search {
            width: 23px;
            height: 23px;
        }

        &__mobile-top-nav {
            display: flex;
            align-items: center;
            margin-left: 60px;
            flex: 1 1 auto;
            color: #000;

            .header {
                &__logo-main {
                    svg {
                        display: none;
                    }
                }

                &__logo-title {
                    color: #000;
                }
            }

            .cricler-wrapper {
                margin: 0 8px;
            }
        }

        &__mobile-top-logo {
            flex: 1 1 auto;
        }

        &__logo-title {
            margin: 0 auto;
        }

        &__mobile-top-icon {
            margin: 0 8px;

            &:last-child {
                margin-right: 0;
            }
        }

        &__mobile-menu {
            position: fixed;
            left: -100vw;
            top: var(--header_height);
            background-color: #fff;
            border-radius: 0 0 var(--border_radius) var(--border_radius);
            width: 100%;
            max-width: 300px;
            height: calc(100% - var(--header_height));
            overflow: auto;
            transition: left .3s;
        }

        .__shown-menu &__mobile-menu {
            left: 0;
        }

        &__mobile-menu-list {
            padding-bottom: 35px;
        }

        &__mobile-menu-item {
            border-top: 1px solid #ededed;
            display: flex;
            align-items: center;
            padding: 10px 20px;

            &:first-child {
                border-top-width: 0;
            }

            svg {
                margin-right: 20px;
                width: 20px;
                height: 20px;
                color: #c0c0c0;
            }
        }

        &__mobile-menu-item--back {
            svg {
                color: var(--theme_color);
                transform: rotate(180deg);
            }
        }

        &__mobile-menu-item--back &__mobile-menu-item-text {
            font-weight: 700;
            font-size: 18px;
        }

        &__catalog-button {
            svg {
                display: none;
            }

            span {
                margin-left: 0;
            }
        }

        &__mobile-menu-item-text {
            font-size: 12px;
        }

        &__mobile-menu-item-text &__bottom-nav-link {
            padding: 0;

            &:hover {
                &::after {
                    display: none;
                }
            }
        }

        &__mobile-menu-item--bolder &__mobile-menu-item-text,
        &__mobile-menu-item--bolder &__catalog-button {
            font-weight: 500;
            font-size: 14px;
            color: #000;
        }

        &__auth-text {
            margin-left: 0;
        }

        &__mobile-menu-item--phone &__phone-text {
            margin-left: 0;
        }
    }

    // search
    .header {
        .text-input {
            &__input {
                padding: 0;
                height: 0;
                width: 0;
                opacity: 0;
                border-radius: 0;
                border: 0;
            }

            &__icon {
                color: #000;
                position: relative;
                left: 0;
            }
        }

        .text-input.__focused {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: #fff;
            border-radius: 0 0 var(--border_radius) var(--border_radius);
            z-index: 50;
            padding: 0 10px;

            .text-input {
                &__wrapper {
                    height: 100%;
                }

                &__input {
                    width: 100%;
                    height: 100%;
                    opacity: 1;
                    padding: 0 15px;
                }
            }
        }
    }
}

@media (max-width: 359px) {
    .header {
        &__mobile-top-nav {
            margin-left: 25px;
        }
    }
}
</style>