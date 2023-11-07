<template>
    <div class="textarea text-input">
        <label v-if="$slots.label" class="input-label" :for="id">
            <slot name="label"></slot>
        </label>
        <div class="textarea__wrapper text-input__wrapper">
            <div v-if="$slots.icon" class="text-input__icon" ref="icon">
                <slot name="icon"></slot>
            </div>
            <textarea class="textarea__input text-input__input" :style="textareaStyle" :name="name" :id="id"
                :value="modelValue" :placeholder="placeholder" @input="onInput"></textarea>
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
    name: 'TextareaWrapper',
    props: {
        modelValue: {
            type: [String, Number],
            default: ''
        },
        name: {
            type: String,
            required: true
        },
        id: String,
        placeholder: String,
    },
    emits: ['update:modelValue'],
    data() {
        return {
            iconWidth: 15
        }
    },
    computed: {
        textareaStyle() {
            return {
                'padding-left': `${this.iconWidth}px`
            }
        }
    },
    metohds: {
        onInput(event) {
            this.$emit('update:modelValue', event.target.value)
        }
    }
}
</script>

<style lang="scss">
.textarea {
    &__input {
        resize: none;
        min-height: 142px;
    }
}
</style>