<template>
    <div class="card icon-info-card" :class="{ 'icon-info-card--centered': isCentered }">
        <div class="card__container icon-info-card__container">
            <div class="icon-info-card__icon-container">
                <slot name="icon"></slot>
            </div>
            <h6 class="icon-info-card__title">
                <slot name="title"></slot>
            </h6>
            <p class="icon-info-card__text">
                <slot></slot>
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'IconedInfoCard',
    props: {
        isCentered: Boolean
    }
}
</script>

<style lang="scss">
.icon-info-card {
    color: #525252;
    flex: 0 1 300px;
    margin: 0 15px 25px 15px;

    &::before {
        box-shadow: 0px 0px 19px #DFDFDF;
    }

    &--centered {
        text-align: center;
    }

    &__container {
        height: 100%;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, .05);
        padding: 35px 30px;
    }

    &--centered &__icon-container {
        margin-left: auto;
        margin-right: auto;
    }

    &__icon-container {
        border-radius: 50%;
        background-color: #f3f3f3;
        width: 90px;
        height: 90px;
        padding: 23px;
        margin-bottom: 30px;

        svg,
        picture img {
            max-width: 53px;
            max-height: 53px;
        }

        img {
            object-fit: contain;
        }
    }

    &__title {
        font-weight: 500;
        font-size: 18px;
        line-height: 21px;
        margin-bottom: 25px;
    }

    &__text {
        font-size: 14px;
        line-height: 20px;
    }

    @media (max-width: 669px){
        flex: 1 1 35%;
        margin: 0 5px 25px 5px;
    }

    @media (max-width: 459px){
        &__container {
            padding: 20px 10px 25px 10px;
        }
    }

    @media (max-width: 369px){
        flex: 0 0 100%;
        margin: 0 0 25px 0;
    }
}
</style>