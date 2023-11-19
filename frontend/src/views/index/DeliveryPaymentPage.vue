<template>
    <main class="delivery-payment-page">
        <div class="container">
            <div class="page-heading">
                <div class="breadcrumbs">
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Home' }">
                        Главная
                    </RouterLink>
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'DeliveryPayment' }">
                        Доставка и оплата
                    </RouterLink>
                </div>
                <h1 class="page-title">
                    Доставка и оплата
                </h1>
            </div>
            <div class="info-cards">
                <IconedInfoCard>
                    <template v-slot:icon>
                        <InfoShoppingIcon></InfoShoppingIcon>
                    </template>
                    <template v-slot:title>
                        Принимаем заявки 24/7
                    </template>
                    Принимаем ваши заявки на сайте круглосуточно
                </IconedInfoCard>
                <IconedInfoCard>
                    <template v-slot:icon>
                        <InfoSupportIcon></InfoSupportIcon>
                    </template>
                    <template v-slot:title>
                        Время работы
                    </template>
                    <p>Пн-Пт с 10:00 до 19:00</p>
                    <p>Сб-Вс с 11:00 до 17:00</p>
                </IconedInfoCard>
                <IconedInfoCard>
                    <template v-slot:icon>
                        <InfoDeliveryIcon></InfoDeliveryIcon>
                    </template>
                    <template v-slot:title>
                        Время доставки
                    </template>
                    Курьерская доставка “до дверей” ежедневно, с 10:00 до 22:00
                </IconedInfoCard>
                <IconedInfoCard>
                    <template v-slot:icon>
                        <InfoSupermarketIcon></InfoSupermarketIcon>
                    </template>
                    <template v-slot:title>
                        Самовывоз
                    </template>
                    Самовывоз из выдачи заказов, в часы работы выбранного пункта
                </IconedInfoCard>
            </div>
            <div class="info-cards">
                <StripedInfoCard>
                    <template v-slot:title>
                        Бесплатно
                    </template>
                    При заказе на сумму от 3 000 руб
                </StripedInfoCard>
                <StripedInfoCard>
                    <template v-slot:title>
                        От 300 руб
                    </template>
                    При заказе на сумму до 3 000 руб
                </StripedInfoCard>
                <StripedInfoCard>
                    <template v-slot:title>
                        Бесплатно
                    </template>
                    Самовывоз из пункта выдачи заказов
                </StripedInfoCard>
            </div>
            <h2 class="page-title">
                Оплата при получении
            </h2>
            <div class="info-cards">
                <ImageInfoCard>
                    <InfoWalletIcon></InfoWalletIcon>
                    <span>Наличные</span>
                </ImageInfoCard>
                <ImageInfoCard>
                    <InfoVisaIcon></InfoVisaIcon>
                </ImageInfoCard>
                <ImageInfoCard>
                    <InfoMastercardIcon></InfoMastercardIcon>
                </ImageInfoCard>
                <ImageInfoCard>
                    <InfoMirIcon></InfoMirIcon>
                </ImageInfoCard>
            </div>
            <div class="delivery-payment-page__companies">
                <h2 class="page-title">
                    Работаем со всеми проверенными компаниями
                </h2>
                <ListTable :items="listTableItems"></ListTable>
            </div>
        </div>
    </main>
</template>

<script>
import IconedInfoCard from '@/components/cards/info/IconedInfoCard.vue'
import StripedInfoCard from '@/components/cards/info/StripedInfoCard.vue'
import ImageInfoCard from '@/components/cards/info/ImageInfoCard.vue'
import ListTable from '@/components/tables/ListTable.vue'
import InfoShoppingIcon from '@/components/icons/info/InfoShoppingIcon.vue'
import InfoSupportIcon from '@/components/icons/info/InfoSupportIcon.vue'
import InfoDeliveryIcon from '@/components/icons/info/InfoDeliveryIcon.vue'
import InfoSupermarketIcon from '@/components/icons/info/InfoSupermarketIcon.vue'
import InfoWalletIcon from '@/components/icons/info/InfoWalletIcon.vue'
import InfoVisaIcon from '@/components/icons/info/InfoVisaIcon.vue'
import InfoMastercardIcon from '@/components/icons/info/InfoMastercardIcon.vue'
import InfoMirIcon from '@/components/icons/info/InfoMirIcon.vue'

export default {
    name: 'DeliveryPaymentPage',
    components: {
        IconedInfoCard,
        StripedInfoCard,
        ImageInfoCard,
        ListTable,
        InfoShoppingIcon,
        InfoSupportIcon,
        InfoDeliveryIcon,
        InfoSupermarketIcon,
        InfoWalletIcon,
        InfoVisaIcon,
        InfoMastercardIcon,
        InfoMirIcon,
    },
    data() {
        return {
            listTableItems: [
                { string: 'ООО «СДЭК»', isBold: true },
                { string: 'ООО «Деловые Линии»', isBold: true },
                'Flip post',
                'ООО «М-Транс-Лайн»',
                'ООО «Экспресс-Почта»',
                'ОО «ВЛК- Карго»',
                'ООО «Аэрокарго»',
                'ООО «ЖелДорЭкспедиция»',
                'ЗАО «Беломортранс»',
                'ООО «Карго Логистика»',
                'ООО «Аэродар»',
                'Экспресс доставка «DPD»',
                'ООО «Аэрогруз»',
                'ООО «Энергия»',
                'ООО «Байкал-Сервис»',
                'ООО «КИТ»',
                'ООО «Центр Авто Перевозок»',
                'ООО «Мэйджор Экспресс»',
                'ООО «СПСР-Экспресс»',
                '«Экспресс-Авто»',
                'ООО «ЖелДорАльянс»',
                'ООО «ЖелДорЭкспедиция»',
                'ООО «Пони Экспресс»',
                'ООО «Автотрейдинг»',
                '«EMS Russian Post»',
                'ООО «УралТранСервис»',
            ]
        }
    }
}
</script>

<style lang="scss">
.delivery-payment-page {
    padding: 70px 0;

    .page-title {
        margin-bottom: 40px;
    }

    .info-cards {
        margin-bottom: 30px;
    }

    @media (max-width: 949px) {
        padding: 30px 0 50px 0;

        .page-title {
            margin-bottom: 20px;
        }

        .info-cards {
            margin-bottom: 10px;
        }
    }
}
</style>