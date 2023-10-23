<template>
    <div class="text-input" :class="[containerClass, { '__focused': isFocused }]" :style="{
        'width': computedWidth,
        'height': computedHeight
    }">
        <label v-if="$slots.label" class="input-label" :for="id">
            <slot name="label"></slot>
        </label>
        <div class="text-input__wrapper" :class="wrapperClass">
            <div v-if="$slots.icon" class="text-input__icon" ref="icon">
                <slot name="icon"></slot>
            </div>
            <TextInput v-model="value" :class="inputClass" ref="input" :style="inputStyle" :name="name"
                :placeholder="placeholder" :type="type" :id="id" :autocomplete="autocomplete" :allowSymbols="allowSymbols"
                :numberonly="numberonly" :regexpFlags="regexpFlags" :max="max" :modifiers="modifiers"></TextInput>
            <div v-if="$slots.modsButton" class="text-input__mods" ref="modsButton">
                <slot name="modsButton"></slot>
            </div>
        </div>
        <Transition name="grow">
            <div v-if="$slots.error" class="text-input__error">
                <slot name="error"></slot>
            </div>
        </Transition>
    </div>
</template>

<script>
import TextInput from '@/components/inputs/TextInput.vue'

export default {
    name: 'TextInputWrapper',
    emits: ['update:modelValue', 'input', 'keyup-enter'],
    props: {
        name: String,
        modelValue: [String, Number],
        placeholder: String,
        type: String,
        id: String,
        containerClass: [String, Array, Object],
        wrapperClass: [String, Array, Object],
        inputClass: [String, Array, Object],
        autocomplete: [String, Boolean],
        allowSymbols: String,
        numberonly: Boolean,
        regexpFlags: String,
        width: String,
        height: String,
        max: [Number, String],
        modifiers: [String, Array]
    },
    components: {
        TextInput
    },
    data() {
        return {
            value: '',
            isFocused: false,
            modsButtonWidth: 15,
            iconWidth: 15
        }
    },
    mounted() {
        if (this.$slots.icon || this.$slots.modsButton) {
            this.onResize()
            window.addEventListener('resize', this.onResize)
        }
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.onResize)
    },
    computed: {
        computedWidth() {
            let width = this.width
            if (!width)
                return null

            if (!width.match(/px$/))
                width += 'px'

            return width
        },
        computedHeight() {
            let height = this.height
            if (!height)
                return null

            if (!height.match(/px$/))
                height += 'px'

            return height
        },
        inputStyle() {
            return {
                'padding-left': `${this.iconWidth}px` || null,
                'padding-right': `${this.modsButtonWidth}px` || null,
            }
        }
    },
    methods: {
        onResize() {
            this.handleModsButtonWidth()
            this.handleIconWidth()
        },
        onFocus() {
            this.isFocused = true
            this.doApplyModifiers()
        },
        onBlur() {
            this.isFocused = false
            this.doApplyModifiers()
        },
        focus() {
            this.$refs.input.focus()
        },
        handleIconWidth() {
            const icon = this.$refs.icon
            if (!icon) {
                this.iconWidth = 10
                return
            }

            const width = icon.offsetWidth
            const left = parseInt(getComputedStyle(icon).left.replace(/\D/g, ''))
            this.iconWidth = width + left + 15
        },
        handleModsButtonWidth() {
            const button = this.$refs.modsButton
            if (!button) {
                this.modsButtonWidth = 10
                return
            }

            const width = button.offsetWidth
            const right = parseInt(getComputedStyle(button).right.replace(/\D/g, ''))
            this.modsButtonWidth = width + right + 15
        },
    },
    watch: {
        value() {
            this.$emit('update:modelValue', this.value)
        },
        modelValue(){
            this.value = this.modelValue
        }
    },
}
</script>

<style lang="scss">
.text-input {
    max-width: 300px;

    &--full {
        max-width: none;
    }

    &__wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    &__input {
        border: 1px solid #d9d9d9;
        border-radius: var(--border_radius);
        padding: 10px 15px 10px 50px;
        font-size: 15px;
        width: 100%;
        font-weight: 400;

        &::placeholder {
            color: #b3b3b3;
        }
    }

    &--round &__input {
        border-radius: 23px;
    }

    &__icon {
        position: absolute;
        left: 15px;
        color: var(--theme_color);
        font-size: 23px;

        svg {
            max-width: 23px;
            max-height: 23px;
        }
    }

    &__mods {
        position: absolute;
        right: 15px;

        svg {
            max-width: 25px;
            max-height: 25px;
        }

        div {
            cursor: pointer;
            position: relative;
            top: 2px;
        }
    }

    &__password-visibility-button {
        overflow: hidden;
    }

    &__error {
        color: var(--error_color);
        font-size: 16px;
        margin-top: 10px;
        padding-left: 25px;
    }
}
</style>