<template>
    <div class="code-input">
        <label class="input-label" v-if="$slots.label" :for="id">
            <slot name="label"></slot>
        </label>
        <div class="code-input__wrapper">
            <TextInput class="code-input__input" v-model="value" :id="id" :name="name" :placeholder="placeholder"
                :type="type" :allowSymbols="allowSymbols" :numberonly="numberonly" :regexpFlags="regexpFlags" :max="max"
                :modifiers="modifiers" :mask="mask" :maskSymbol="maskSymbol"></TextInput>
            <button class="code-input__button" type="button" @click="$emit('button-click', value)">
                {{ button }}
            </button>
        </div>
        <Transition name="grow">
            <div class="code-input__error error" v-if="$slots.error">
                <slot name="error"></slot>
            </div>
        </Transition>
    </div>
</template>

<script>
import TextInput from '@/components/inputs/TextInput.vue'

export default {
    name: 'CodeInput',
    emits: ['button-click'],
    components: {
        TextInput
    },
    props: {
        button: String,
        id: String,
        name: String,
        placeholder: String,
        type: String,
        allowSymbols: String,
        numberonly: Boolean,
        regexpFlags: String,
        max: [Number, String],
        modifiers: [String, Array],
        mask: String,
        maskSymbol: String,
    },
    data() {
        return {
            value: ''
        }
    }
}
</script>

<style lang="scss">
.code-input {
    --border_color: #dadada;

    .input-label {
        display: block;
        font-size: 13px;
        line-height: 15px;
        margin-bottom: 10px;
        color: #b9b9b9;
    }

    &__wrapper {
        display: flex;
        border-radius: var(--border_radius);
        border: 1px solid var(--border_color);
    }

    &__input {
        flex: 0 0 61%;
        border-right: 1px solid var(--border_color);
        border-width: 0px;
        padding-left: 17px;
    }

    &__button {
        flex: 1 1 auto;
        background-color: var(--theme_color);
        font-size: 14px;
        line-height: 16px;
        padding: 11px 9px 11px 14px;
        font-weight: 700;
        color: #fff;
        transition-property: color, background-color;
        transition-duration: .3s;
        border-radius: var(--border_radius);
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;

        &:hover {
            background-color: var(--theme_color_darker);
        }
    }

    &__error {
        margin-top: 11px;
        // font-size: 14px;
        // line-height: 16px;
    }
}
</style>