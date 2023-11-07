<template>
    <div @keyup.enter="save">
        <Transition name="grow">
            <div v-if="error" class="error error--mb-20">
                {{ error }}
            </div>
        </Transition>
        <div class="inputs-flex">
            <TextInputWrapper v-model="input.email" name="email" id="email" placeholder="Email">
                <template v-slot:label>Адрес эл.почты</template>
                <template v-if="errors.email" v-slot:error>
                    {{ errors.email[0] }}
                </template>
            </TextInputWrapper>
            <TextInputWrapper v-model="input.phone_number" mask="phone" numberonly
                name="phone_number" id="phone_number" placeholder="+7 (999) 999 - 99 - 99">
                <template v-slot:label>Номер телефона</template>
                <template v-if="errors.phone_number" v-slot:error>
                    {{ errors.phone_number[0] }}
                </template>
            </TextInputWrapper>
        </div>
        <div class="inputs-flex">
            <TextInputWrapper v-model="input.name" name="name" id="name" placeholder="Имя">
                <template v-slot:label>Ваше имя</template>
                <template v-if="errors.email" v-slot:error>
                    {{ errors.email[0] }}
                </template>
            </TextInputWrapper>
            <TextInputWrapper v-model="input.surname" name="surname" id="surname" placeholder="Фамилия">
                <template v-slot:label>Ваша фамилия</template>
            </TextInputWrapper>
            <TextInputWrapper v-model="input.patronymic" name="patronymic" id="patronymic" placeholder="Отчество">
                <template v-slot:label>Ваше отчество</template>
            </TextInputWrapper>
        </div>
        <div class="inputs-flex">
            <TextInputWrapper v-model="input.location" name="location" id="location" placeholder="Населенный пункт">
                <template v-slot:label>Ваш насленный пункт</template>
                <template v-if="errors.location" v-slot:error>
                    {{ errors.location[0] }}
                </template>
            </TextInputWrapper>
            <TextInputWrapper v-model="input.street" name="street" id="street" placeholder="Улица">
                <template v-slot:label>Улица</template>
            </TextInputWrapper>
            <TextInputWrapper v-model="input.house" name="house" id="house" placeholder="Дом">
                <template v-slot:label>Дом</template>
            </TextInputWrapper>
        </div>
        <div class="account__buttons">
            <button class="button button--colored" type="button" @click="save">
                Сохранить
            </button>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import { handleAjaxError } from '@/assets/js/scripts.js'
import { useIndexStore } from '@/stores/'
import { useNotificationsStore } from '@/stores/notifications.js'
import axios from 'axios'

export default {
    name: 'AccountInfo',
    components: {
        TextInputWrapper
    },
    props: {
        user: Object
    },
    data() {
        return {
            input: {
                email: '',
                name: '',
                surname: '',
                patronymic: '',
                phone_number: '',
                location: '',
                street: '',
                house: ''
            },
            error: '',
            errors: []
        }
    },
    methods: {
        async save() {
            const link = import.meta.env.VITE_USER_UPDATE_LINK
            const store = useIndexStore()
            store.toggleLoading('saveAccountInfo', true)

            try {
                const res = await axios.post(link, {
                    phone_number: this.input.phone_number.replace(/[^\+0-9]/g, '') || null,
                    email: this.input.email,
                    name: this.input.name,
                    surname: this.input.surname || null,
                    patronymic: this.input.patronymic || null,
                    location: this.input.location || null,
                    street: this.input.street || null,
                    house: this.input.house || null,
                })

                await store.checkAuth()
                if (res.data.id) {
                    useNotificationsStore()
                        .addNotification({ message: 'Изменения сохранены', timeout: 2500 })
                }
            } catch (err) {
                handleAjaxError(err, this)
            }

            store.toggleLoading('saveAccountInfo', false)
        },
        fillInputs() {
            this.input.email = this.user.email || ''
            this.input.name = this.user.name || ''
            this.input.surname = this.user.surname || ''
            this.input.patronymic = this.user.patronymic || ''
            this.input.phone_number = this.user.phone_number || ''
            this.input.location = this.user.location || ''
            this.input.street = this.user.street || ''
            this.input.house = this.user.house || ''
        }
    },
    watch: {
        user() {
            this.fillInputs()
        }
    },
    mounted(){
        this.fillInputs()
    }
}
</script>