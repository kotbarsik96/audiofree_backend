<template>
    <div class="card info-card"
        :class="{ 'info-card--big': cardType.match(/big/i), 'info-card--bordered-side': cardType.match(/bordered/i) }">
        <div class="card__bottom card__side"></div>
        <div class="card__container card__side info-card__container">
            <div class="info-card__image info-card__image--ellipse" v-if="$slots.image">
                <slot name="image"></slot>
            </div>
            <div class="info-card__message">
                <div class="info-card__title" v-if="$slots.title">
                    <slot name="title"></slot>
                </div>
                <div class="info-card__text" v-if="$slots.body">
                    <slot name="body"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "InfoCard",
    props: {
        cardType: {
            type: String,
            default: "default" || "big",
        },
    },
};
</script>

<style></style>