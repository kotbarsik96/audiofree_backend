<template>
    <li class="step-info-card card" :class="{ '--last': isLastCart }">
        <div class="step-info-card__container card__container">
            <div class="step-info-card__top">
                <div class="step-info-card__number"></div>
                <div class="step-info-card__chevron">
                    <ChevronIcon></ChevronIcon>
                </div>
            </div>
            <div class="step-info-card__body">
                <slot></slot>
            </div>
        </div>
    </li>
</template>

<script>
export default {
    name: 'StepInfoCard',
    props: {
        isLastCart: Boolean
    }
}
</script>

<style lang="scss">
.step-info-card {
    flex: 0 1 260px;
    margin: 0 10px 15px 10px;

    &__container {
        height: 100%;
    }

    &::before {
        transform: translateY(11px) scale(0.96);
    }

    &__top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #ebebeb;
    }

    &__number {
        border-radius: 50%;
        padding: 20px 15px 15px 15px;
        box-shadow: 0px 4px 9px rgba(0, 0, 0, .09);
        display: flex;
        align-items: center;
        justify-content: center;

        &::before {
            /* ДЛЯ ul: counter-reset: step-info-card */
            content: counter(step-info-card, decimal-leading-zero);
            counter-increment: step-info-card;
            color: var(--theme_color);
            font-weight: 900;
            font-size: 20px;
            line-height: 20px;
        }
    }

    &__chevron {
        color: var(--theme_color);

        svg {
            width: 11px;
            height: 19px;
        }
    }

    &.--last &__chevron {
        transform: rotate(90deg);
    }

    &__body {
        padding: 30px 30px 15px 20px;
    }

    @media (max-width: 1149px){
        flex: 0 1 430px;
    }

    @media (max-width: 939px){
        flex: 0 1 47%;
    }

    @media (max-width: 767px){
        &::before{
            display: none;
        }
    }

    @media (max-width: 699px){
        flex: 0 0 100%;

        &__chevron {
            transform: rotate(90deg);
        }
    }
}
</style>