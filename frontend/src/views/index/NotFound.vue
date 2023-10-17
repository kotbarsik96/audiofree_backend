<template>
    <div class="not-found">
        <div class="container">
            <p class="not-found__message">
                К сожалению, запрашиваемая страница не найдена
            </p>
            <p class="not-found__link-container">
                <RouterLink :to="{ name: 'Home' }" class="link">
                    Перейти на главную
                </RouterLink>
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NotFound',
}
</script>

<style lang="scss">
.not-found {
    text-align: center;
    display: flex;
    align-items: center;
    font-weight: 500;
    min-height: 25vh;

    &__message {
        font-size: 26px;
        line-height: 50px;
    }

    &__link-container {
        font-size: 24px;

        .link:not(:hover) {
            color: var(--theme_color);
        }
    }
}
</style>