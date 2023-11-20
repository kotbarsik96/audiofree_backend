<template>
    <ComponentWithGallery tag="section" class="wls-advantages-section section section--theme-colored" :imageTag="imageTag"
        v-model="gallery">
        <div class="container">
            <h3 class="section-title section-title--centered">
                <div>
                    Чем беспроводные лучше?
                </div>
                <div class="section-title__highlighted">
                    Все о преимуществах наушников Bluetooth
                </div>
            </h3>
            <div class="wls-advantages-section__body">
                <ol class="wls-advantages-section__list">
                    <li class="wls-advantages-section__item">
                        <span class="wls-advantages-section__item-dot"></span>
                        <h5 class="wls-advantages-section__item-title">
                            Отсутствие проводов
                        </h5>
                        <p class="wls-advantages-section__item-text">
                            Наушники Bluetooth обеспечивают свободу движения и избавляют от мешающих проводов, позволяя вам
                            наслаждаться музыкой или звонками без ограничений
                        </p>
                    </li>
                    <li class="wls-advantages-section__item">
                        <span class="wls-advantages-section__item-dot"></span>
                        <h5 class="wls-advantages-section__item-title">
                            Удобство
                        </h5>
                        <p class="wls-advantages-section__item-text">
                            Благодаря беспроводной технологии, наушники Bluetooth обеспечивают максимальное удобство
                            использования, позволяя вам забыть о неудобствах, связанных с проводами
                        </p>
                    </li>
                    <li class="wls-advantages-section__item">
                        <span class="wls-advantages-section__item-dot"></span>
                        <h5 class="wls-advantages-section__item-title">
                            Красивый аксессуар
                        </h5>
                        <p class="wls-advantages-section__item-text">
                            Наушники Bluetooth не только предоставляют вам отличный звук, но и служат стильным аксессуаром,
                            подчеркивающим ваш индивидуальный стиль и вкус
                        </p>
                    </li>
                </ol>
                <div class="wls-advantages-section__picture">
                    <div class="wls-advantages-section__picture-container">
                        <ImagePicture v-if="gallery[0]" :obj="gallery[0]"></ImagePicture>
                    </div>
                </div>
                <ol class="wls-advantages-section__list">
                    <li class="wls-advantages-section__item">
                        <span class="wls-advantages-section__item-dot"></span>
                        <h5 class="wls-advantages-section__item-title">
                            Без ограничений
                        </h5>
                        <p class="wls-advantages-section__item-text">
                            С Bluetooth-наушниками вы освобождаетесь от физических ограничений, позволяя вам свободно
                            перемещаться, будь то в городе, в спортзале или дома
                        </p>
                    </li>
                    <li class="wls-advantages-section__item">
                        <span class="wls-advantages-section__item-dot"></span>
                        <h5 class="wls-advantages-section__item-title">
                            Функциональность
                        </h5>
                        <p class="wls-advantages-section__item-text">
                            Наушники Bluetooth предлагают широкий спектр функций, включая управление музыкой, ответы на
                            звонки, встроенный микрофон и другие возможности, сделавшие их неотъемлемой частью вашей
                            повседневной жизни
                        </p>
                    </li>
                    <li class="wls-advantages-section__item">
                        <span class="wls-advantages-section__item-dot"></span>
                        <h5 class="wls-advantages-section__item-title">
                            Экономная батарея
                        </h5>
                        <p class="wls-advantages-section__item-text">
                            Благодаря передовым технологиям управления энергией, Bluetooth-наушники способны обеспечивать
                            стабильную связь без значительного влияния на заряд батареи ваших устройств
                        </p>
                    </li>
                </ol>
            </div>
        </div>
    </ComponentWithGallery>
</template>

<script>
import ComponentWithGallery from '@/components/misc/ComponentWithGallery.vue'

export default {
    name: 'WirelessAdvantagesSection',
    components: {
        ComponentWithGallery
    },
    props: {
        imageTag: String,
    },
    data() {
        return {
            gallery: []
        }
    }
}
</script>

<style lang="scss">
.wls-advantages-section {
    --until_counter: 56px;

    padding-top: 110px;
    padding-bottom: 37px;

    &__body {
        margin-top: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        counter-reset: num;
    }

    &__list {
        flex: 0 0 325px;
        list-style-type: none;
        padding-left: var(--until_counter);
    }

    &__item {
        position: relative;
        list-style-type: none;
        padding-top: 10px;
        padding-left: 48px;
        padding-bottom: 40px;
        border-left: 1px solid rgba(225, 215, 255, .3);

        &::before {
            content: counter(num, decimal-leading-zero);
            counter-increment: num;
            position: absolute;
            top: 16px;
            left: calc(var(--until_counter) * -1);
            font-weight: 900;
            color: #6a5c8f;
        }
    }

    &__list:last-child &__item:last-child {
        border-left-width: 0px;
    }

    &__item-dot {
        width: 46px;
        height: 46px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        left: 0;
        top: 0;
        transform: translateX(-50%);

        &::before {
            content: "";
            border-radius: 50%;
            background-color: var(--theme_color);
            width: 10px;
            height: 10px;
        }
    }

    &__item-title {
        font-size: 24px;
        line-height: 28px;
        font-weight: 400;
        margin-bottom: 10px;
    }

    &__item-text {
        font-size: 12px;
        line-height: 14px;
    }

    &__picture {
        width: 515px;
        height: 515px;
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        border-radius: 50%;
        border: 1px solid rgba(225, 215, 255, .15);
    }

    &__picture-container {
        padding: 25px;
        border: 1px solid rgba(225, 215, 255, .3);
        border-radius: 50%;
        width: 100%;
        height: 100%;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;

        &::before {
            content: "";
            position: absolute;
            background: rgba(255, 255, 255, .15);
            width: calc(100% - 46px);
            height: calc(100% - 46px);
            border-radius: 50%;
            z-index: 50;
        }

        picture {
            width: 100%;
            height: 100%;
            padding: 15px;
        }

        img {
            object-fit: contain;
        }
    }

    @media (max-width: 1179px) {
        &__picture {
            width: 40vw;
            height: 40vw;
        }
    }

    @media (max-width: 1099px) {
        --until_counter: 46px;

        &__picture {
            width: 35vw;
            height: 35vw;
            padding: 15px;
        }
    }

    @media (max-width: 1019px) {
        &__picture {
            width: 30vw;
            height: 30vw;
        }
    }

    @media (min-width: 950px) {
        &__list:last-child {
            padding-left: 0;
            padding-right: var(--until_counter);
        }

        &__list:last-child &__item {
            padding-left: 0;
            padding-right: 48px;

            &:not(:last-child) {
                border-left: none;
                border-right: 1px solid rgba(225, 215, 255, .3);
            }

            &:nth-child(2n) {
                padding-left: 13px;
            }

            &::before {
                right: calc(var(--until_counter) * -1);
                left: auto;
            }
        }

        &__list:last-child &__item-dot {
            left: auto;
            right: 0;
            transform: translateX(50%);
        }

        &__item:last-child {
            border-left-width: 0;
        }
    }

    @media (max-width: 949px) {
        padding-top: 35px;
        padding-bottom: 60px;

        &__body {
            flex-direction: column;
        }

        &__picture {
            order: 1;
            width: 400px;
            height: 400px;
            margin-bottom: 11px;
        }

        &__list {
            width: 345px;

            &:first-child {
                order: 2;
            }

            &:last-child {
                order: 3;
            }
        }
    }

    @media (max-width: 429px) {
        &__picture {
            width: 90vw;
            height: 90vw;
        }
    }

    @media (max-width: 369px) {
        &__list {
            padding-left: 20px;
            flex: 0 0 100%;
            width: auto;

            &::before {
                display: none;
            }
        }
    }
}
</style>