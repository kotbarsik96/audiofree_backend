<template>
    <section class="audiofree-advantages-section section">
        <div class="container">
            <h3 class="section-title section-title--centered">
                <div>
                    Где выгодно купить качественные наушники?
                </div>
                <div class="section-title__highlighted">
                    Интернет-магазин AudioFree - магазин хороших покупок
                </div>
            </h3>
            <div class="section__text section__text--centered mb-50">
                Мы подбираем наушники не просто так: ассортимент формируется исходя из спроса и новинок рынка, с учетом
                требований к качеству и по наличию популярных, топовых, классических позиций
            </div>
            <div class="info-cards">
                <IconedInfoCard isCentered>
                    <template v-slot:icon>
                        <InfoDeliveryIcon></InfoDeliveryIcon>
                    </template>
                    <template v-slot:title>
                        Доставка 1-2 дня
                    </template>
                    Команда "AudioFree" быстро соберет и доставит Вашу посылку - от 1 до 2 дней по всей Росcии в зависимости
                    от адреса
                </IconedInfoCard>
                <IconedInfoCard isCentered>
                    <template v-slot:icon>
                        <InfoMoneyIcon></InfoMoneyIcon>
                    </template>
                    <template v-slot:title>
                        Предоплата 0 руб.
                    </template>
                    Никаких предоплат при доставке по Москве и Санкт-Петербургу - платите при получении, когда
                    удостоверитесь в качестве покупки
                </IconedInfoCard>
                <IconedInfoCard isCentered>
                    <template v-slot:icon>
                        <InfoShoppingIcon></InfoShoppingIcon>
                    </template>
                    <template v-slot:title>
                        0 рисков при покупке
                    </template>
                    При покупке в "Audio Free"
                    Вы обязательно получите чек
                    и гарантийный талон. Мы всегда соблюдаем все права потребителей
                </IconedInfoCard>
            </div>
        </div>
    </section>
</template>

<script>
import IconedInfoCard from '@/components/cards/info/IconedInfoCard.vue'
import InfoDeliveryIcon from '@/components/icons/info/InfoDeliveryIcon.vue'
import InfoMoneyIcon from '@/components/icons/info/InfoMoneyIcon.vue'
import InfoShoppingIcon from '@/components/icons/info/InfoShoppingIcon.vue'

export default {
    name: 'AudiofreeAdvantagesSection',
    components: {
        IconedInfoCard,
        InfoDeliveryIcon,
        InfoMoneyIcon,
        InfoShoppingIcon
    }
}
</script>

<style lang="scss">
.audiofree-advantages-section {
    padding-top: 100px;
    padding-bottom: 80px;

    @media (max-width: 949px){
        padding-top: 50px;
        padding-bottom: 45px;
    }
}
</style>