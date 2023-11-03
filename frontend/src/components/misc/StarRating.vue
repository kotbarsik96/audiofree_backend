<template>
    <div class="star-rating" :class="{ 'star-rating--interactive': interactive }">
        <div class="star-rating__stars star-rating__stars--empty">
            <div v-for="star in stars" class="star-rating__star">
                <StarIcon v-on="interactive ? { click: () => setValue(star) } : {}"></StarIcon>
            </div>
        </div>
        <div class="star-rating__stars star-rating__stars--active" :style="{ 'width': activeStarsWidth }">
            <div v-for="star in stars" class="star-rating__star star-rating__star--active">
                <StarIcon></StarIcon>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'StarRating',
    emits: ['update-rating'],
    props: {
        stars: {
            type: Number,
            required: true
        },
        rating: {
            type: Number,
            default: 0
        },
        interactive: Boolean,
    },
    data() {
        return {
            value: this.rating
        }
    },
    computed: {
        activeStarsWidth() {
            return `${this.value / (this.stars / 100)}%`
        }
    },
    methods: {
        setValue(value) {
            if (!this.interactive)
                return
            if (value > this.stars)
                return

            this.value = value
        },
        removeValue() {
            this.value = 0
        }
    },
    watch: {
        value() {
            this.$emit('update-rating', this.value)
        },
        rating(){
            this.value = this.rating
        }
    }
}
</script>

<style lang="scss">
.star-rating {
    position: relative;
    display: inline-block;

    &__stars {
        display: inline-flex;
        align-items: center;
    }

    &--interactive &__stars--empty {
        cursor: pointer;
    }

    &__stars--active {
        position: absolute;
        pointer-events: none;
        overflow: hidden;
        width: 0;
        top: 0;
        left: 0;
    }

    &__star {
        width: 14px;
        height: 14px;
        color: #d4d4d4;
        margin-right: 5px;
        flex: 0 0 auto;

        &:last-child {
            margin-right: 0;
        }

        svg {

            width: 100%;
            height: 100%;
        }
    }

    &__star--active {
        color: #ffc107;
    }
}
</style>