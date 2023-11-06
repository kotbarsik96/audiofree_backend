<template>
    <PageWrapper>
        <LoadingScreen v-if="isPageLoading" position="fixed" squareSize="150px"></LoadingScreen>
        <RouterView v-slot="{ Component }">
            <Transition name="page-transition" :mode="transitionMode">
                <component :is="Component" :key="routeKey" @updateRouteKey="routeKey < 2 ? routeKey++ : routeKey--">
                </component>
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
import LoadingScreen from "@/components/page/LoadingScreen.vue"
import '@/assets/scss/styles.scss'
import { mapState } from 'pinia'
import { useIndexStore } from '@/stores/'
import axios from 'axios'

axios.defaults.withCredentials = true

export default {
    components: {
        PageWrapper,
        NotificationsList,
        ModalsList,
        LoadingScreen
    },
    data() {
        return {
            routeKey: 1
        }
    },
    async created() {
        await useIndexStore().checkAuth()
    },
    computed: {
        ...mapState(useIndexStore, ['isUserLogged', 'isPageLoading']),
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
        animation: appear .5s ease;
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
        animation: leave .5s ease-in;
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