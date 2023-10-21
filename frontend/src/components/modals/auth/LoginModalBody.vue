<template>
    <form @submit.prevent @input="removeErrorsOnInput" @keyup.enter="login">
        <Transition name="grow">
            <div v-if="errorMessage" class="auth-modal__error">
                {{ errorMessage }}
            </div>
        </Transition>
        <TextInputWrapper class="text-input--full" v-model="email" name="email" placeholder="Email">
            <template v-slot:icon>
                <MailIcon></MailIcon>
            </template>
            <template v-slot:error v-if="errors.email">
                {{ errors.email[0] }}
            </template>
        </TextInputWrapper>
        <PasswordInput class="text-input--full" name="password" placeholder="Пароль" autocomplete="password" :passwordError="passwordError"
            v-model="password">
        </PasswordInput>
        <div class="modal__buttons">
            <button class="modal__button link" type="button" @click="resetPassword">
                Забыли пароль?
            </button>
            <button class="modal__button button button--colored" type="submit" @click.prevent="login">
                Войти
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
    name: 'LoginModalBody',
    emits: ['load-start', 'load-end', 'greet'],
    components: {
        TextInputWrapper,
        PasswordInput
    },
    data() {
        return {
            email: '',
            password: '',
            errors: {},
            errorMessage: ''
        }
    },
    computed: {
        passwordInputType() {
            return this.passwordVisible
                ? 'text' : 'password'
        },
        passwordError() {
            return this.errors.password
                ? this.errors.password[0]
                : ''
        }
    },
    methods: {
        removeErrorsOnInput,
        async login() {
            this.$emit('load-start')

            const store = useIndexStore()
            try {
                const res = await store.login({
                    email: this.email,
                    password: this.password
                })

                if (res.success) {
                    this.$emit('greet', res.message)
                }
            } catch (err) {
                if (err.response.data.errors)
                    this.errors = err.response.data.errors
                if(err.response.data.error)
                    this.errorMessage = err.response.data.error
            }

            this.$emit('load-end')
        },
        resetPassword() {

        }
    }
}
</script>