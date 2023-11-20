<template>
    <main class="contacts-page">
        <div class="container">
            <div class="breadcrumbs">
                <RouterLink class="breadcrumbs__link link" :to="{ name: 'Home' }">
                    Главная
                </RouterLink>
                <RouterLink class="breadcrumbs__link link" :to="{ name: 'Contacts' }">
                    Контакты
                </RouterLink>
            </div>
            <div class="contacts-page__card card">
                <div class="contacts-page__card-container card__container">
                    <div class="contacts-page__contacts">
                        <h1 class="page-title">Контакты</h1>
                        <ul class="contacts-page__contact-items-list">
                            <li class="contacts-page__contact-item">
                                <div class="contact-block">
                                    <a class="circle-wrapper circle-wrapper--green" href="tel:81111111">
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
                            <li class="contacts-page__contact-item">
                                <div class="contact-block">
                                    <div class="circle-wrapper circle-wrapper--green circle-wrapper--not-interactive">
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
                            <li class="contacts-page__contact-item">
                                <div class="contact-block">
                                    <div class="circle-wrapper circle-wrapper--green circle-wrapper--not-interactive">
                                        <MailIcon></MailIcon>
                                    </div>
                                    <div class="contact-block__text">
                                        <div class="contact-block__top-text">
                                            По всем вопросам пишите:
                                        </div>
                                        <div class="contact-block__value">
                                            <a class="link" href="mailto:kotbarsik96@mail.ru">kotbarsik96@mail.ru</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <form class="contacts-page__form form">
                        <h3 class="form__title">
                            Напишите нам:
                        </h3>
                        <p class="form__about">
                            Заполните форму, наши специалисты, свяжутся с Вами в течении 15 минут
                        </p>
                        <div class="form__body">
                            <TextInputWrapper name="write-back-name" id="write-back-name" placeholder="Введите ваше имя">
                            </TextInputWrapper>
                            <TextInputWrapper mask="phone" name="write-back-phone" id="write-back-phone"
                                placeholder="Введите ваш телефон"></TextInputWrapper>
                            <button class="button button--colored" type="submit" @click.prevent>
                                Отправить
                            </button>
                        </div>
                        <div class="form__agreement">
                            Нажимая кнопку "Отправить" Вы даёте свое согласие на обработку введенной персональной информации
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'

export default {
    name: 'ContactsPage',
    components: {
        TextInputWrapper
    }
}
</script>

<style lang="scss">
.contacts-page {
    padding-top: 65px;
    padding-bottom: 70px;

    &__card {
        .page-title {
            padding-left: 30px;
        }
    }

    &__card-container {
        display: flex;
        justify-content: space-between;
        padding: 30px 30px 30px 0;
    }

    &__contacts {
        flex: 0 0 435px;
    }

    &__contact-item {
        padding: 20px 0 25px 30px;
        border-bottom: 1px solid #e0e0e0;

        &:last-child {
            border-bottom: none;
        }
    }

    &__form {
        flex: 0 0 265px;
    }

    @media (max-width: 949px) {
        padding-top: 30px;
        padding-bottom: 25px;
    }

    @media (max-width: 849px){
        .breadcrumbs {
            padding: 0 10px;
        }

        .container {
            padding: 0;
        }

        &__card {
            .page-title {
                padding-left: 10px;
            }

            &::before {
                display: none;
            }
        }

        &__card-container {
            padding: 0;
            display: block;
            box-shadow: none;
        }

        &__contact-item {
            padding-left: 10px;
        }

        &__form {
            max-width: 350px;
            margin: 30px auto 0 auto;
            padding: 0 10px;
        }
    }
}
</style>