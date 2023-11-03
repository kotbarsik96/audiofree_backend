<template>
    <label class="radio">
        <input type="radio" ref="input" :checked="checked" :name="name" :value="value" @change="onChange">
        <span class="radio__circle"></span>
        <span class="radio__text" v-if="$slots.default">
            <slot></slot>
        </span>
    </label>
</template>

<script>
export default {
    name: 'RadioLabel',
    props: {
        name: {
            type: String,
            required: true
        },
        value: {
            type: [String, Number],
            required: true
        },
        modelValue: [String, Number],
        checked: Boolean,
    },
    emits: ['update:modelValue', 'update:checked'],
    methods: {
        onChange(event) {
            if (event.target.checked)
                this.$emit('update:modelValue', this.value)
        }
    },
}
</script>

<style lang="scss">
.radio {
    display: flex;
    align-items: center;

    &__circle {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 1px solid #D8D8D8;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        margin-right: 11px;

        &::before {
            content: "";
            display: block;
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: transparent;
            transition-property: background-color;
            transition-duration: .2s;
        }
    }

    input {
        display: none;
    }

    input:checked+&__circle::before {
        background-color: var(--theme_color);
    }
}
</style>