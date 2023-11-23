<template>
    <div @keyup.enter="save">
        <Transition name="grow">
            <div v-if="error" class="error error--mb-20">
                {{ error }}
            </div>
        </Transition>
        <form class="inputs-flex inputs-flex--center">
            <input type="text" style="display: none;" autocomplete="username">
            <PasswordInput v-model="input.currentPassword" autocomplete="password" @update:modelValue="nullifyErrors" name="current_password" id="current_password"
                placeholder="Текущий пароль" :passwordError="errorMessages.password">
                <template v-slot:label>
                    Текущий пароль
                </template>
            </PasswordInput>
            <PasswordInput v-model="input.newPassword" @update:modelValue="nullifyErrors" name="new_password" id="new_password" autocomplete="new-password" placeholder="Новый пароль"
                :passwordError="errorMessages.new_password">
                <template v-slot:label>
                    Новый пароль
                </template>
            </PasswordInput>
            <PasswordInput v-model="input.newPasswordConfirmation" @update:modelValue="nullifyErrors" name="new_password_confirmation"
                id="new_password_confirmation" autocomplete="new-password" placeholder="Новый пароль еще раз"
                :passwordError="errorMessages.new_password_confirmation">
                <template v-slot:label>
                    Новый пароль еще раз
                </template>
            </PasswordInput>
        </form>
        <div class="account__buttons">
            <button class="button button--colored" type="button" @click="save">
                Сохранить
            </button>
        </div>
    </div>
</template>

<script>
import PasswordInput from '@/components/inputs/PasswordInput.vue'
import { handleAjaxError } from '@/assets/js/scripts.js'
import { useIndexStore } from '@/stores/'
import { useNotificationsStore } from '@/stores/notifications.js'
import axios from 'axios'

export default {
    name: 'ChangePassword',
    components: {
        PasswordInput
    },
    props: {
        user: Object
    },
    data() {
        return {
            error: '',
            errors: {},
            input: {
                currentPassword: '',
                newPassword: '',
                newPasswordConfirmation: ''
            }
        }
    },
    computed: {
        errorMessages() {
            const obj = {}
            if (this.errors.password)
                obj.password = this.errors.password[0]
            if (this.errors.new_password)
                obj.new_password = this.errors.new_password[0]
            if (this.errors.new_password_confirmation)
                obj.new_password_confirmation = this.errors.new_password_confirmation[0]

            return obj
        }
    },
    methods: {
        async save() {
            const link = import.meta.env.VITE_AUTH_CHANGE_PASSWORD_LINK
            const store = useIndexStore()
            store.toggleLoading('changePassword', true)

            try {
                const res = await axios.post(link, {
                    password: this.input.currentPassword,
                    new_password: this.input.newPassword,
                    new_password_confirmation: this.input.newPasswordConfirmation,
                })

                if (res.data.message) {
                    useNotificationsStore().addNotification({
                        message: res.data.message
                    })
                    for(let key in this.input) {
                        this.input[key] = ''
                    }
                }
            } catch (err) {
                handleAjaxError(err, this)
            }

            store.toggleLoading('changePassword', false)
        },
        nullifyErrors() {
            this.error = ''
            this.errors = []
        }
    }
}
</script>

<style></style>