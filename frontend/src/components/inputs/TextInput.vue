<template>
    <div class="text-input" :class="[containerClass, { '__focused': isFocused }]">
        <div class="text-input__wrapper" :class="wrapperClass">
            <div v-if="$slots.icon" class="text-input__icon">
                <slot name="icon"></slot>
            </div>
            <input class="text-input__input" :class="inputClass"
                :style="{ 'padding-right': `${modsButtonWidth}px` || null }" v-model="value" :placeholder="placeholder"
                :type="type" :name="inputName" :id="id" :autocomplete="autocomplete" ref="input" @input="$emit('input')" @keyup.enter="onEnterKeyup">
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
        autocomplete: [String, Boolean]
    },
    data() {
        return {
            value: '',
            isFocused: false,
            modsButtonWidth: 0
        }
    },
    mounted() {
        this.$refs.input.addEventListener('focus', this.onFocus)
        this.$refs.input.addEventListener('blur', this.onBlur)

        if (this.$slots.modsButton) {
            this.handleModsButtonWidth()
            window.addEventListener('resize', this.handleModsButtonWidth)
        }
    },
    beforeUnmount(){
        window.removeEventListener('resize', this.handleModsButtonWidth)
    },
    methods: {
        onFocus() {
            this.isFocused = true
        },
        onBlur() {
            this.isFocused = false
        },
        focus() {
            this.$refs.input.focus()
        },
        handleModsButtonWidth() {
            const button = this.$refs.modsButton
            const width = button.offsetWidth
            const right = parseInt(getComputedStyle(button).right.replace(/\D/g, ''))
            this.modsButtonWidth = width + right + 15
        },
        onEnterKeyup(event){
            event.preventDefault()
            event.stopPropagation()
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