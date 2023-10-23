<template>
    <div class="select" :class="{ '__shown': isShown }" :style="{ '--transition_dur': `${duration}s` }">
        <button v-if="$slots.label" class="input-label" type="button" @click="toggleSelect">
            <slot name="label"></slot>
        </button>
        <div class="select__wrapper">
            <button class="select__value" type="button" @click="toggleSelect">
                <span class="select__value-text" :class="{ 'select__value-text--empty': !value }">
                    {{ value || 'Выберите значение' }}
                </span>
                <span class="select__value-icon">
                    <ChevronIcon></ChevronIcon>
                </span>
            </button>
            <Transition :css="false" @before-enter="onBeforeEnter" @enter="onEnter" @leave="onLeave">
                <ul v-show="isShown" class="select__list">
                    <li v-for="itemValue in values" class="select__item">
                        <label class="select__item-label">
                            <input type="radio" :name="name" :value="itemValue" v-model="value" ref="input">
                            <span>
                                {{ itemValue }}
                            </span>
                        </label>
                    </li>
                </ul>
            </Transition>
        </div>
    </div>
</template>

<script>
import { gsap } from 'gsap'
import { getHeight } from '@/assets/js/scripts.js'
import { nextTick } from 'vue'

export default {
    name: 'SelectValue',
    emits: ['update:modelValue'],
    props: {
        name: {
            type: String,
            required: true
        },
        values: {
            type: Array,
            required: true
        },
        modelValue: [String, Number]
    },
    data() {
        return {
            value: '',
            isShown: false,
            duration: 0.3
        }
    },
    mounted() {
        window.addEventListener('click', this.onDocumentClick)
    },
    beforeUnmount() {
        window.removeEventListener('click', this.onDocumentClick)
    },
    methods: {
        toggleSelect() {
            this.isShown = !this.isShown
        },
        onDocumentClick(event) {
            if (event.target === this.$el || event.target.closest('.select') === this.$el)
                return

            this.isShown = false
        },
        onBeforeEnter(el) {
            gsap.set(el, {
                maxHeight: 0,
                margin: 0,
                padding: 0,
                overflow: 'hidden'
            })
        },
        onEnter(el, done) {
            const maxHeight = `${getHeight(el)}px`
            gsap.to(el, {
                maxHeight,
                duration: this.duration,
                onComplete: done
            })
        },
        onLeave(el, done) {
            gsap.to(el, {
                maxHeight: '0px',
                opacity: '0px',
                padding: '0px',
                margin: '0px',
                duration: this.duration,
                onComplete: done
            })
        }
    },
    watch: {
        value() {
            this.$emit('update:modelValue', this.value)
            this.isShown = false
        },
        modelValue(){
            this.value = this.modelValue
        },
        isShown() {
            if (this.isShown) {
                const input = this.$refs.input.find(i => i.value === this.value)
                if (input)
                    nextTick().then(() => input.checked = true)
            }
        }
    }
}
</script>

<style lang="scss">
.select {
    --border_color: #dadada;

    position: relative;
    width: 270px;
    z-index: 25;

    &__wrapper {
        position: relative;
    }

    &__value {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        padding: 10px 35px 10px 20px;
        font-size: 16px;
        line-height: 19px;
        border: 1px solid var(--border_color);
        background-color: #fff;
        border-radius: var(--border_radius);
        transition-property: border-radius;
        transition-duration: calc(var(--transition_dur) * 2);
    }

    &.__shown &__value {
        border-bottom-width: 0px;
        border-radius: var(--border_radius) var(--border_radius) 0 0;
        transition-duration: var(--transition_dur);
    }

    &__value-text {
        font-size: 16px;
        line-height: 19px;
        color: #474747;

        &--empty {
            color: #bababa;
            font-weight: 400;
        }
    }

    &__value-icon {
        position: absolute;
        right: 15px;
        display: inline-block;
        transform: translateY(2px);

        svg {
            transform: rotate(90deg);
            display: inline-block;
            width: 14px;
            height: 14px;
            transition-property: transform;
            transition-duration: var(--transition_dur);
        }
    }

    &.__shown &__value-icon {
        svg {
            transform: rotate(-90deg);
        }
    }

    &__list {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        border-bottom-left-radius: var(--border_radius);
        border-bottom-right-radius: var(--border_radius);
        border: 1px solid var(--border_color);
        border-top-width: 0px;
        background-color: #fff;
    }

    &.__shown &__list {
        border-top-width: 1px;
    }

    &__item {
        width: 100%;
        border-bottom: 1px solid var(--border_color, transparent);

        &:last-child {
            border-bottom-width: 0px;
        }
    }

    &__item-label {
        display: block;
        width: 100%;

        input {
            display: none;
        }

        input:checked+span {
            background-color: var(--admin_panel_color);
            color: #fff;
        }
    }

    &__item-label>span {
        display: block;
        width: 100%;
        padding: 10px 20px;
        font-size: 16px;
        line-height: 19px;
        font-weight: 400;
        transition-property: background-color, color;
        transition-duration: var(--transition_dur);

        &:hover {
            background-color: var(--theme_color);
            color: #fff;
        }
    }
}
</style>