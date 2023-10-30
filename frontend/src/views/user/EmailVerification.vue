<template>
    <div class="account__container account__container--min-height" @keyup.enter="onEnterKeyup">
        <Transition name="grow">
            <div v-if="error" class="error error--mb-20">
                {{ error }}
            </div>
        </Transition>
        <Transition name="fade-in" mode="out-in">
            <div v-if="codeSent">
                <div class="inputs-flex inputs-flex--center">
                    <TextInputWrapper v-model="verificationCode" @update:modelValue="nullifyErrors()" name="email-verification" id="email-verification" placeholder="Код подтверждения"> 
                        <template v-slot:label>
                            Код подтверждения
                        </template>
                    </TextInputWrapper>
                </div>
                <div class="account__buttons">
                    <button class="button button--colored" type="button" @click="verifyCode">
                        Подтвердить код
                    </button>
                </div>
            </div>
            <div class="account__buttons" v-else>
                <button class="button button--colored" type="button" @click="sendCode">
                    Выслать код подтверждения на почту
                </button>
            </div>
        </Transition>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import { handleAjaxError } from '@/assets/js/scripts.js'
import { useIndexStore } from '@/stores/'
import { useNotificationsStore } from '@/stores/notifications.js'
import { mapState } from 'pinia'
import axios from 'axios'

export default {
    name: 'EmailVerification',
    components: {
        TextInputWrapper
    },
    data() {
        return {
            codeSent: false,
            isLoading: false,
            verificationCode: '',
            error: ''
        }
    },
    computed: {
        ...mapState(useIndexStore, ['emailVerified'])
    },
    methods: {
        ifEmailVerified(){
            if(this.emailVerified)
                this.$router.push({ name: 'AccountInfo' })
        },
        async checkIfCodeSent() {
            const link = import.meta.env.VITE_EMAIL_VERIFICATION_SENT_CHECK_LINK
            const store = useIndexStore()

            try {
                store.toggleLoading('checkIfVerificationEmailCodeSent', true)
                const res = await axios.get(link)
                if (res.data.success)
                    this.codeSent = true
                else
                    this.codeSent = false
            } catch (err) {
                this.codeSent = false
            }

            store.toggleLoading('checkIfVerificationEmailCodeSent', false)
        },
        async sendCode() {
            const link = import.meta.env.VITE_EMAIL_VERIFY_LINK
            const store = useIndexStore()

            try {
                store.toggleLoading('emailVerificationCodeSent', true)
                const res = await axios.get(link)
                if (res.data.success) {
                    this.codeSent = true

                    if (res.data.message)
                        useNotificationsStore().addNotification({
                            message: res.data.message
                        })
                } else
                    this.codeSent = false
            } catch (err) {
                handleAjaxError(err, this)
                this.codeSent = false
            }

            store.toggleLoading('emailVerificationCodeSent', false)
        },
        async verifyCode() {
            if(!this.verificationCode.trim())
                return

            const link = `${import.meta.env.VITE_EMAIL_VERIFY_LINK}${this.verificationCode}`
            const store = useIndexStore()

            try {
                store.toggleLoading('emailVerification', true)
                const res = await axios.get(link)
                if (res.data.message) {
                    useNotificationsStore().addNotification({
                        message: res.data.message
                    })
                }
                if(res.data.success) {
                    this.ifEmailVerified()
                    store.checkAuth()
                    this.$router.push({ name: 'AccountInfo' })
                }
            } catch (err) {
                handleAjaxError(err, this)
            }

            store.toggleLoading('emailVerification', false)
        },
        onEnterKeyup(){
            if(this.isLoading)
                return

            if(this.codeSent)
                this.verifyCode()
            else 
                this.sendCode()
        },
        nullifyErrors(){
            this.error = ''
        }
    },
    watch: {
        emailVerified(){
            this.ifEmailVerified()
        }
    },
    async mounted() {
        this.ifEmailVerified()
        this.checkIfCodeSent()
    },
}
</script>

<style></style>