<template>
    <label class="checkbox">
        <input type="checkbox" :checked="checked" :name="name" :value="value" @change="onChange">
        <span class="checkbox__box"></span>
        <span class="checkbox__text" v-if="$slots.default">
            <slot></slot>
        </span>
    </label>
</template>

<script>
export default {
    name: 'CheckboxLabel',
    props: {
        name: {
            type: String,
            required: true
        },
        value: {
            type: [String, Number],
            required: true
        },
        modelValue: Array,
        checked: Boolean,
    },
    emits: ['update:modelValue'],
    data() {
        return {
            selected: []
        }
    },
    methods: {
        onChange(event) {
            this.updateModelValue(event.target.checked)
        },
        updateModelValue(isChecked = false) {
            if (!Array.isArray(this.modelValue))
                return

            let updated
            if (isChecked)
                updated = [...this.modelValue, this.value]
            else
                updated = this.modelValue.filter(value => value !== this.value)

            this.$emit('update:modelValue', updated)
        }
    },
    watch: {
        modelValue() {
            if (!Array.isArray(this.modelValue))
                return false

            if (this.modelValue.includes(this.value))
                this.selected = [this.value]
            else
                this.selected = []
        }
    },
}
</script>

<style lang="scss">
.checkbox {
    display: flex;
    align-items: center;

    &__box {
        width: 20px;
        height: 20px;
        border-radius: 2px;
        border: 1px solid rgba(0, 0, 0, .35);
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        transition-property: background-color;
        transition-duration: .2s;
    }

    &__text {
        margin-left: 11px;
    }

    input {
        display: none;
    }

    input:checked+&__box {
        background-color: var(--theme_color);

        &::before {
            content: "";
            background-image: url('/img/icons/checkmark.svg');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            position: absolute;
            width: 15px;
            height: 15px;
        }
    }
}
</style>