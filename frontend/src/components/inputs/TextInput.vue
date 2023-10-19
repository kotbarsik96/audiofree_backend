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
                :autocomplete="autocomplete" ref="input" @input="onInput" @keyup.enter="onEnterKeyup">
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
        this.$refs.input.addEventListener('focus', this.onFocus)
        this.$refs.input.addEventListener('blur', this.onBlur)

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
        }
    },
    methods: {
        onResize() {
            this.handleModsButtonWidth()
            this.handleIconWidth()
        },
        onFocus() {
            this.isFocused = true
        },
        onBlur() {
            this.isFocused = false
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
            console.log(this.scopeSymbolsRegexp);
            if (this.scopeSymbolsRegexp) {
                event.target.value = event.target.value.replace(this.scopeSymbolsRegexp, '')
            }
            this.$emit('input')
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
    &__wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    &__input {
        border: 1px solid #d9d9d9;
        border-radius: 23px;
        padding: 10px 15px 10px 50px;
        font-size: 15px;
        width: 100%;
        font-weight: 400;

        &::placeholder {
            color: #b3b3b3;
        }
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