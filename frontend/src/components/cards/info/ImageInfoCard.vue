<template>
    <div class="card image-info-card">
        <div class="card__container image-info-card__container">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ImageInfoCard',
}
</script>

<style lang="scss">
.image-info-card {
    flex: 0 1 270px;
    margin: 0 15px 15px 0;

    &__container {
        padding: 10px 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        min-height: 144px;

        &::before {
            transform: translate(11px) scale(0.96);
        }

        img,
        picture,
        svg {
            max-width: 225px;
            max-height: 100%;
            object-fit: contain;
        }

        span {
            display: block;
            font-size: 30px;
            line-height: 40px;
            font-weight: 500;
            color: #666666;
            margin: 10px;
        }
    }
}
</style>