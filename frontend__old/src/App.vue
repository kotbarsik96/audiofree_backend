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
import PageWrapper from '@/components/page/PageWrapper.vue';
import NotificationsList from '@/components/notifications/NotificationsList.vue';
import ModalsList from '@/components/modals/ModalsList.vue';
import '@/assets/scss/styles.scss';

export default {
    components: {
        PageWrapper,
        NotificationsList,
        ModalsList
    },
    data() {
        return {
        }
    },
    computed: {
        transitionMode() {
            return "out-in";
        },
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