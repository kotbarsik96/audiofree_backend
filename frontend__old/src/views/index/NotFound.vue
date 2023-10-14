<template>
    <main class="not-found">
        <div class="container">
            <template v-if="isProductsLoadError">
                <h1 class="not-found__title">
                    К сожалению, произошла ошибка на сайте. Попробуйте зайти
                    позднее
                </h1>
            </template>
            <template v-else>
                <h1 class="not-found__title">
                    К сожалению, такой страницы не найдено
                </h1>
                <p class="not-found__paragraph">
                    <router-link :to="{ name: 'home' }" class="not-found__link"
                        >Перейти на главную</router-link
                    >
                </p>
                <p class="not-found__paragraph" v-if="previousPage">
                    <a class="not-found__link"
                        >или вернуться на предыдущую страницу</a
                    >
                </p>
            </template>
        </div>
    </main>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    name: "NotFound",
    computed: {
        ...mapGetters(["isProductsLoadError"]),
    },
};
</script>