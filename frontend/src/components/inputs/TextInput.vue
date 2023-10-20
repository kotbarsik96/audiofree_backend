<template>
    <div class="text-input" :class="[containerClass, { '__focused': isFocused }]" :style="{
        'width': computedWidth,
        'height': computedHeight
    }">
        <label v-if="$slots.label" class="text-input__label" :for="id">
            <slot name="label"></slot>
        </label>
        <div class="text-input__wrapper" :class="wrapperClass">
            <div v-if="$slots.icon" class="text-input__icon" ref="icon">
                <slot name="icon"></slot>
            </div>
            <input class="text-input__input" :class="inputClass" :style="{
                'padding-left': `${iconWidth}px` || null,
                'padding-right': `${modsButtonWidth}px` || null,
            }" v-model="value" :placeholder="placeholder" :type="type" :name="inputName" :id="id"
                :autocomplete="autocomplete" :maxlength="maxlength" ref="input" @focus="onFocus" @blur="onBlur"
                @input="onInput" @keyup.enter="onEnterKeyup">
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
import { nextTick } from 'vue'

export default {
    name: 'TextInput',
    emits: ['update:modelValue', 'input', 'keyup-enter'],
    props: {
        inputName: {
            type: String,
            required: true
        },
        modelValue: String,
        placeholder: String,
        type: {
            type: String,
            default: 'text'
        },
        id: String,
        containerClass: [String, Array, Object],
        wrapperClass: [String, Array, Object],
        inputClass: [String, Array, Object],
        autocomplete: [String, Boolean],
        /* e.g.: a,g,\+,3, не забывать экранировать спецсимволы: \-, \+ */
        allowSymbols: {
            type: String,
            validator(value) {
                value.match(/^(.,)+.?$/)
            }
        },
        numberonly: Boolean,
        /* e.g.: gi */
        regexpFlags: {
            type: String,
            validator(valueRaw) {
                const flags = ['g', 'i', 'm', 's', 'u', 'y']
                const value = valueRaw.split('')
                    .filter(s => flags.includes(s))
                    .join('')

                return valueRaw.length === value.length
            }
        },
        width: String,
        height: String,
        // если numberonly, max === максимальное число; иначе === максимальная длина строки
        max: [Number, String],
        /* представляют собой методы String, например, toLocaleString. Можно передать массив, можно передать строку вида "toLocaleString|trim" */
        modifiers: [String, Array]
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
        scopeSymbolsRegexp() {
            if (!this.numberonly && !this.allowSymbols)
                return null

            let regexpStr = ''
            if (this.numberonly)
                regexpStr += '0-9'

            if (this.allowSymbols)
                regexpStr += this.allowSymbols

            if (regexpStr.length > 0)
                regexpStr = `[^${regexpStr}]`

            if (this.regexpFlags)
                return new RegExp(regexpStr, this.regexpFlags)

            return new RegExp(regexpStr)
        },
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
        maxlength() {
            if (!this.max)
                return null

            if (this.numberonly) {
                const length = this.max.toString().length
                return length + Math.floor(length / 3)
            }
            else
                return this.max
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
        onEnterKeyup(event) {
            event.preventDefault()
            event.stopPropagation()
        },
        onInput(event) {
            const input = event.target

            if (this.maxlength) {
                const value = input.value.replace(/\D/g, '')
                if (this.numberonly && parseInt(value) > parseInt(this.max))
                    input.value = this.max
            }

            this.doScopeSymbols(input)
            this.doApplyModifiers(input)

            this.$emit('input')
        },
        doScopeSymbols(input = this.$refs.input) {
            if (!this.scopeSymbolsRegexp)
                return

            nextTick().then(() => input.value = input.value.replace(this.scopeSymbolsRegexp, ''))
        },
        doApplyModifiers(input = this.$refs.input) {
            if (!this.modifiers)
                return

            if (this.modifiers.length < 0)
                return

            const array = Array.isArray(this.modifiers)
                ? this.modifiers
                : this.modifiers.split('|')

            array.forEach(modifier => {
                let value = input.value
                const method = value[modifier]
                if (typeof method !== 'function')
                    return

                if (modifier === 'toLocaleString') {
                    value = parseInt(value.replace(/\s/g, ''))
                    if (isNaN(value))
                        return
                }

                // без nextTick input.value как будто откатывается назад на один шаг
                nextTick().then(() => input.value = value[modifier]())
            })
        }
    },
    watch: {
        value() {
            this.$emit('update:modelValue', this.value)
        }
    },
}
</script>

<style lang="scss">
.text-input {
    max-width: 300px;

    &__label {
        display: inline-block;
        margin-bottom: 5px;
        margin-left: 5px;
        font-size: 13px;
        line-height: 15px;
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