<template>
    <div class="card striped-info-card">
        <div class="card__container striped-info-card__container">
            <h6 class="striped-info-card__title">
                <slot name="title"></slot>
            </h6>
            <p class="striped-info-card__text">
                <slot></slot>
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'StripedInfoCard',
}
</script>

<style lang="scss">
.striped-info-card {
    --border_radius: 7.5px;

    color: #494949;
    flex: 1 1 300px;
    margin: 0 15px 15px 0;

    &::before,
    &__container {
        border-radius: var(--border_radius);
        box-shadow: 0px 0px 9px rgba(0, 0, 0, .1);
    }

    &::before {
        transform: translateY(11px) scale(0.96);
    }

    &__container {
        position: relative;
        min-height: 166px;
        padding: 20px 55px 15px 40px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;

        &::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 12px;
            background-color: var(--theme_color);
            border-radius: var(--border_radius);
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
    }

    &__title {
        font-size: 24px;
        line-height: 30px;
        font-weight: 500;
        color: var(--theme_color);
        margin-bottom: 10px;
    }

    &__text {
        font-size: 14px;
        line-height: 16px;
        font-weight: 500;
    }

    @media (max-width: 949px){
        &__container{ 
            min-height: 120px;
            padding: 30px 40px 30px 40px;
        }
    }
}
</style>