<template>
    <div class="auth-modal__reset-password" @keyup.enter="sendNewPassword">
        <Transition name="grow">
            <div v-if="errorMessage" class="error">
                {{ errorMessage }}
            </div>
        </Transition>
        <Transition name="modal-tabs-body" mode="out-in">
            <div v-if="emailSent" class="auth-modal__reset-password-text">
                Новый пароль был отправлен на указанный адрес email
            </div>
            <TextInputWrapper class="text-input--full" v-else name="reset-password" v-model="emailValue" @update:modelValue="nullifyErrors" id="reset-password"
                placeholder="Email">
                <template v-slot:label>
                    Email, указанный при регистрации
                </template>
            </TextInputWrapper>
        </Transition>
        <TransitionGroup name="modal-tabs-body" tag="div" class="modal__buttons">
            <button v-if="!emailSent" :key="'button_send_new'" class="modal__button button button--colored" type="button" @click="sendNewPassword">
                Отправить новый пароль
            </button>
            <button :key="'turn_back'" class="button" type="button" @click="$emit('go-back')">
                Вернуться
            </button>
        </TransitionGroup>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import { handleAjaxError } from '@/assets/js/scripts.js'
import axios from 'axios'

export default {
    name: 'AuthResetPasswordModalBody',
    props: {
        email: String
    },
    emits: ['change-loading-state', 'go-back'],
    components: {
        TextInputWrapper
    },
    data() {
        return {
            emailValue: '',
            error: '',
            errors: [],
            emailSent: false
        }
    },
    computed: {
        errorMessage() {
            if (this.error)
                return this.error
            if (this.errors[0]) {
                const values = Object.values(this.errors[0])
                if (values[0])
                    return values[0][0]
            }
        }
    },
    methods: {
        async sendNewPassword() {
            const link = import.meta.env.VITE_AUTH_RESET_PASSWORD_LINK

            try {
                this.$emit('change-loading-state', true)
                const res = await axios.post(link, { email: this.emailValue })
                if (res.data.success)
                    this.emailSent = true
            } catch (err) {
                handleAjaxError(err, this)
            }

            this.$emit('change-loading-state', false)
        },
        nullifyErrors(){
            this.error = ''
            this.errors = []
        }
    },
    mounted() {
        this.emailValue = this.email.trim()
    },
}
</script>

<style lang="scss">
.auth-modal {
    &__reset-password {
        font-size: 24px;
        font-weight: 500;
        text-align: center;
        margin-bottom: 20px;
        padding: 0 15px;
    }

    &__reset-password-text {
        font-size: 18px;
        margin-bottom: 20px;
    }

    .error {
        margin-bottom: 20px;
    }
}
</style>