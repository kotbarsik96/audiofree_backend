<template>
    <div class="modal auth-modal">
        <LoadingScreen v-if="isLoading"></LoadingScreen>
        <div class="modal__close">
            <button class="close-button" type="button" @click="removeModal"></button>
        </div>
        <div class="modal__tabs" v-if="!greetingMessage">
            <button class="modal__tab" :class="{ '__active': type === 'login' }" type="button" @click="type = 'login'">
                Вход
            </button>
            <button class="modal__tab" :class="{ '__active': type === 'register' }" type="button"
                @click="type = 'register'">
                Регистрация
            </button>
        </div>
        <h3 v-if="title" class="modal__title">
            {{ title }}
        </h3>
        <div class="modal__body">
            <Transition name="modal-tabs-body" mode="out-in">
                <AuthGreetModalBody v-if="greetingMessage" :message="greetingMessage"></AuthGreetModalBody>
                <component v-else :is="modalBodyComponent" @load-start="startLoad" @load-end="endLoad" @greet="greetUser">
                </component>
            </Transition>
        </div>
    </div>
</template>

<script>
import LoginModalBody from './LoginModalBody.vue'
import RegisterModalBody from './RegisterModalBody.vue'
import AuthGreetModalBody from './AuthGreetModalBody.vue'
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import { useModalsStore } from '@/stores/modals.js'
import { alignModal } from '@/assets/js/scripts.js'

export default {
    name: 'AuthModal',
    components: {
        LoginModalBody,
        RegisterModalBody,
        AuthGreetModalBody,
        LoadingScreen
    },
    props: {
        title: {
            type: String,
            default: 'Авторизация'
        },
        defaultType: String,
    },
    data() {
        return {
            type: 'register', // | 'login'
            modalId: null,
            isLoading: false,
            greetingMessage: ''
        }
    },
    mounted() {
        this.type = this.defaultType
        this.onResize()
        window.addEventListener('resize', this.onResize)
    },
    computed: {
        modalBodyComponent() {
            switch (this.type) {
                case 'register':
                default:
                    return RegisterModalBody
                case 'login':
                    return LoginModalBody
            }
        }
    },
    methods: {
        removeModal() {
            const modalsStore = useModalsStore()
            modalsStore.removeModal(this.modalId)
        },
        startLoad() {
            this.isLoading = true
        },
        endLoad() {
            this.isLoading = false
        },
        onResize() {
            alignModal(this.$el)
        },
        greetUser(message) {
            this.greetingMessage = message
            setTimeout(this.removeModal, 2000)
        }
    }
}
</script>

<style lang="scss">
.auth-modal {
    &__error {
        font-size: 21px;
        color: var(--error_color);
        margin: 0 auto 20px auto;
        padding: 0 10px;
        text-align: center;
    }
}
</style>