<template>
    <main class="warranty-page">
        <div class="container">
            <div class="page-heading">
                <div class="breadcrumbs">
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Home' }">
                        Главная
                    </RouterLink>
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Warranty' }">
                        Гарантия и возврат
                    </RouterLink>
                </div>
                <h1 class="page-title">
                    Гарантия и возврат
                </h1>
            </div>
            <div class="info-cards">
                <IconedInfoCard isBigger noIconBackground>
                    <template v-slot:icon>
                        <InfoTextIcon></InfoTextIcon>
                    </template>
                    <template v-slot:title>
                        Гарантийный талон
                    </template>
                    <p>
                        Вы можете принять решение о возврате любого изделия в течение гарантийного срока, установленного
                        производителем на каждую конкретную модель.
                    </p>
                    <p>
                        Началом гарантийного срока считается момент получения заказа, если сохранены товарный вид и
                        потребительские свойства товаров,
                        а также документ, подтверждающий факт
                        и условия покупки.
                    </p>
                </IconedInfoCard>
                <IconedInfoCard isBigger noIconBackground>
                    <template v-slot:icon>
                        <InfoCalendarIcon></InfoCalendarIcon>
                    </template>
                    <template v-slot:title>
                        Возврат в течении 14 дней
                    </template>
                    <p>
                        Возврат без указания причин или по причине неподходящего размера, цвета, типа осуществляется в
                        течение 14 (четырнадцати) календарных дней с момента фактического получения Товара непосредственно
                        от продавца или лиц представляющих Продавца.
                    </p>
                    <p>
                        Возврату подлежит только стоимость товаров. Стоимость доставки не компенсируется.
                    </p>
                </IconedInfoCard>
                <IconedInfoCard isBigger noIconBackground>
                    <template v-slot:icon>
                        <InfoRefreshIcon></InfoRefreshIcon>
                    </template>
                    <template v-slot:title>
                        Возврат брака
                    </template>
                    <p>
                        При обнаружении брака в процессе использования купленного устройства
                        Вы вправе предъявить претензию, если брак обнаружен в течение гарантийного срока.
                    </p>
                    <p>
                        Если обнаружился брак, стоимость доставки также подлежит возврату
                    </p>
                </IconedInfoCard>
            </div>
            <h5 class="page-title page-title--regular">
                Как оформить возврат?
            </h5>
            <ul class="step-cards">
                <StepInfoCard>
                    Аккуратно упаковать товар
                    в упаковку в полной комплектации ( товары, поставляемые в комплекте, необходимо возвращать также
                    в комплекте).
                </StepInfoCard>
                <StepInfoCard>
                    Заполнить заявление о возврате (прикладывается к заказу или отправляется менеджером отдела контроля
                    качества).
                </StepInfoCard>
                <StepInfoCard>
                    Приложить копию кассового чека, указать способ возврата денежных средств.
                </StepInfoCard>
                <StepInfoCard isLastCart>
                    Согласовать с менеджером отдела контроля качества способ доставки возвращаемого
                    или обмениваемого товара.
                </StepInfoCard>
            </ul>
        </div>
    </main>
</template>

<script>
import IconedInfoCard from '@/components/cards/info/IconedInfoCard.vue'
import StepInfoCard from '@/components/cards/info/StepInfoCard.vue'
import InfoTextIcon from '@/components/icons/info/InfoTextIcon.vue'
import InfoCalendarIcon from '@/components/icons/info/InfoCalendarIcon.vue'
import InfoRefreshIcon from '@/components/icons/info/InfoRefreshIcon.vue'

export default {
    name: 'WarrantyPage',
    components: {
        IconedInfoCard,
        StepInfoCard,
        InfoTextIcon,
        InfoCalendarIcon,
        InfoRefreshIcon
    }
}
</script>

<style lang="scss">
.warranty-page {
    padding-top: 65px;
    padding-bottom: 70px;

    .page-heder {
        margin-bottom: 65px;
        
        .page-title {
            margin-bottom: 0;
        }
    }

    .info-cards {
        margin-bottom: 65px;
    }

    .page-title {
        margin-bottom: 30px;
    }

    @media (max-width: 949px) {
        padding-top: 30px;
        padding-bottom: 70px;

        .info-cards {
            margin-bottom: 45px;
        }
    }
}
</style>