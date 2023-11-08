<template>
    <div class="quantity-input">
        <label class="quantity-input__label input-label input-label--medium" v-if="$slots.default" :for="id">
            <slot></slot>
        </label>
        <div class="quantity-input__container">
            <button class="quantity-input__button quantity-input__button--less" type="button" @click="decrease">
                <ChevronIcon></ChevronIcon>
            </button>
            <TextInput class="quantity-input__input" :style="{ width: `${inputWidth}px` }" v-model="value" :name="name"
                :id="id" :max="max" numberonly @change="onChange"></TextInput>
            <button class="quantity-input__button quantity-input__button--more" type="button" @click="increase">
                <ChevronIcon></ChevronIcon>
            </button>
            <div class="quantity-input__invisible" ref="invisible">
                {{ value }}
            </div>
        </div>
    </div>
</template>

<script>
import TextInput from '@/components/inputs/TextInput.vue'
import { isNumeric } from '@/assets/js/scripts.js'

export default {
    name: 'QuantityInput',
    components: {
        TextInput
    },
    props: {
        name: String,
        id: String,
        min: {
            type: Number,
            default: 0
        },
        max: Number,
        modelValue: [String, Number],
    },
    emits: ['update:modelValue'],
    data() {
        return {
            value: '',
            inputWidth: '',
        }
    },
    methods: {
        increase() {
            this.value = parseInt(this.value)
            if (isNaN(this.value)) {
                this.value = this.min
                return
            }
            if (isNumeric(this.max)) {
                if (this.value + 1 > this.max)
                    return
            }

            this.value = this.value + 1
        },
        decrease() {
            this.value = parseInt(this.value)
            if (isNaN(this.value)) {
                this.value = this.min
                return
            }
            if (this.value - 1 < this.min)
                return

            this.value = this.value - 1
        },
        async adjustWidth() {
            await this.$nextTick()
            this.inputWidth = this.$refs.invisible.offsetWidth
        },
        onChange() {
            this.value = parseInt(this.value)
            if (isNaN(this.value))
                this.value = this.min

            if (this.value < this.min)
                this.value = this.min

            if (isNumeric(this.max) && this.value > this.max)
                this.value = this.max
        }
    },
    watch: {
        value() {
            this.adjustWidth()
            this.$emit('update:modelValue', parseInt(this.value))
        },
        modelValue: {
            deep: true,
            handler(newValue, oldValue) {
                if (newValue === oldValue)
                    return

                this.value = newValue
            }
        }
    },
    mounted() {
        if (this.modelValue)
            this.value = this.modelValue
        else
            this.value = this.min
    }
}
</script>

<style lang="scss">
.quantity-input {
    --input_font_size: 19px;
    display: inline-block;

    &__container {
        display: inline-flex;
        border: 1px solid #e3e3e3;
        border-radius: var(--border_radius);
        height: 40px;
    }

    &__button {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 13px 18px;
        border-radius: var(--border_radius);

        svg {
            width: 7px;
            height: 13px;
            color: #767676;
        }
    }

    &__button--less {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;

        svg {
            transform: rotate(180deg);
        }
    }

    &__button--more {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    &__input {
        box-sizing: content-box;
        border-right: 1px solid #f5f5f5;
        border-left: 1px solid #f5f5f5;
        border-radius: 0;
        border-top: 0;
        border-bottom: 0;
        padding: 0 15px;
        font-size: var(--input_font_size);
        flex: 1 1 auto;
        text-align: center;
    }

    &__invisible {
        position: absolute;
        z-index: -999;
        right: 200vw;
        opacity: 0;
        font-size: var(--input_font_size);
        display: inline-block;
    }
}
</style>