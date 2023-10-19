<template>
    <PageWrapper>
        <RouterView v-slot="{ Component }">
            <Transition name="page-transition" :mode="transitionMode">
                <component :is="Component"></component>
            </Transition>
        </RouterView>
        <NotificationsList></NotificationsList>
        <ModalsList></ModalsList>
    </PageWrapper>
</template>

<script>
import PageWrapper from '@/components/page/PageWrapper.vue'
import NotificationsList from '@/components/notifications/NotificationsList.vue'
import ModalsList from '@/components/modals/ModalsList.vue'
import '@/assets/scss/styles.scss'
import { mapState } from 'pinia'
import { useIndexStore } from '@/stores/'
import axios from 'axios'

axios.defaults.withCredentials = true

export default {
    components: {
        PageWrapper,
        NotificationsList,
        ModalsList
    },
    async mounted() {
        const store = useIndexStore()
        store.checkAuth()
    },
    data() {
        return {
        }
    },
    computed: {
        ...mapState(useIndexStore, ['isUserLogged']),
        transitionMode() {
            return "out-in";
        },
    },
    watch: {
        isUserLogged(newValue, oldValue) {
            // изменений не произошло
            if (newValue === oldValue)
                return

            // произошел вход
            if (!oldValue && newValue) {
            }

            // произошел выход
            if (oldValue && !newValue) {
                const requiresAuthOrAdmin = this.$route.meta.requiresAdmin
                    || this.$route.meta.requiresAuth
                if (!this.isUserLogged && requiresAuthOrAdmin)
                    this.$router.push({ name: 'Home' })
            }
        }
    }
}
</script>

<style lang="scss">
.page-transition {
    &-enter-active {
        animation: appear 1s ease;
    }

    @keyframes appear {
        0% {
            opacity: 0;
            transform: translate(0, 100vh);
        }

        100% {
            opacity: 1;
            transform: translate(0, 0);
        }
    }

    &-leave-active {
        animation: leave 1s ease-in;
    }

    @keyframes leave {
        0% {
            opacity: 1;
            transform: translate(0, 0);
        }

        100% {
            opacity: 0;
            transform: translate(0, -100vh);
        }
    }
}
</style>