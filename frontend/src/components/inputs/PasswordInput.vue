<!-- Это просто TextInput, но со слотами, нужными для функционала поля пароля -->
<template>
    <TextInputWrapper v-model="value" :name="name" :placeholder="placeholder" :type="passwordInputType"
        :autocomplete="autocomplete">
        <template v-if="$slots.label" v-slot:label>
            <slot name="label"></slot>
        </template>
        <template v-slot:icon>
            <KeyIcon></KeyIcon>
        </template>
        <template v-slot:modsButton>
            <div class="slide-above-parent" @click="onPasswordClick">
                <Transition name="slide-above" mode="out-in">
                    <EyeClosedIcon v-if="passwordVisible"></EyeClosedIcon>
                    <EyeIcon v-else></EyeIcon>
                </Transition>
            </div>
        </template>
        <template v-if="passwordError" v-slot:error>
            {{ passwordError }}
        </template>
    </TextInputWrapper>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'

export default {
    name: 'PasswordInput',
    emits: ['update:modelValue'],
    components: {
        TextInputWrapper
    },
    props: {
        modelValue: [String, Number],
        autocomplete: String,
        name: String,
        placeholder: String,
        passwordError: String
    },
    data() {
        return {
            value: '',
            passwordVisible: false,
        }
    },
    computed: {
        passwordInputType() {
            return this.passwordVisible
                ? 'text' : 'password'
        },
    },
    watch: {
        value() {
            this.$emit('update:modelValue', this.value)
        }
    },
    methods: {
        onPasswordClick() {
            this.passwordVisible = !this.passwordVisible
        }
    }
}
</script>