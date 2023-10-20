<template>
    <form @submit.prevent @input="onFormInput" @keyup.enter="register">
        <Transition name="grow">
            <div v-if="errorMessage" class="auth-modal__error">
                {{ errorMessage }}
            </div>
        </Transition>
        <TextInputWrapper v-model="name" name="name" placeholder="Имя">
            <template v-slot:error v-if="errors.name">
                {{ errors.name[0] }}
            </template>
        </TextInputWrapper>
        <TextInputWrapper v-model="email" name="email" placeholder="Email">
            <template v-slot:icon>
                <MailIcon></MailIcon>
            </template>
            <template v-slot:error v-if="errors.email">
                {{ errors.email[0] }}
            </template>
        </TextInputWrapper>
        <PasswordInput name="password" placeholder="Пароль" autocomplete="new-password" :passwordError="passwordError"
            v-model="password">
        </PasswordInput>
        <PasswordInput name="password_confirmation" placeholder="Подтверждение пароля" autocomplete="new-password"
            :passwordError="passwordConfirmationError" v-model="password_confirmation">
        </PasswordInput>
        <div class="modal__buttons">
            <button class="modal__button button button--colored" type="submit" @click.prevent="register">
                Зарегистрироваться
            </button>
        </div>
    </form>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import PasswordInput from '@/components/inputs/PasswordInput.vue'
import { useIndexStore } from '@/stores/'
import { removeErrorsOnInput } from '@/assets/js/scripts'

export default {
    name: 'RegisterModalBody',
    emits: ['load-start', 'load-end', 'greet'],
    components: {
        TextInputWrapper,
        PasswordInput
    },
    data() {
        return {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            errors: {},
            errorMessage: ''
        }
    },
    computed: {
        passwordError() {
            return this.errors.password
                ? this.errors.password[0]
                : ''
        },
        passwordConfirmationError() {
            return this.errors.password_confirmation
                ? this.errors.password_confirmation[0]
                : ''
        }
    },
    methods: {
        removeErrorsOnInput,
        async register() {
            this.$emit('load-start')

            const store = useIndexStore()
            try {
                const res = await store.register({
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation
                })

                if (res.success)
                    this.$emit('greet', res.message)
            } catch (err) {
                if (err.response.data.errors)
                    this.errors = err.response.data.errors
                
                if(err.response.data.error)
                    this.errorMessage = err.response.data.error
            }

            this.$emit('load-end')
        },
        onFormInput(event) {
            this.removeErrorsOnInput(event)
        },
        onPasswordClick() {
            this.passwordVisible = !this.passwordVisible
        },
    },
}
</script>

<style></style>