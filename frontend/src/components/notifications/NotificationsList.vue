<template>
    <Transition name="notifications-list">
        <div v-if="notifications.length > 0" class="notifications-list">
            <TransitionGroup name="notifications-list-item">
                <component v-for="notification of notifications" :key="notification.id" :is="notification.component"
                    :notificationId="notification.id">
                </component>
            </TransitionGroup>
        </div>
    </Transition>
</template>

<script>
import { useNotificationsStore } from '@/stores/notifications.js'
import { mapState } from 'pinia'

export default {
    name: 'NotificationsList',
    computed: {
        ...mapState(useNotificationsStore, ['notifications'])
    }
}
</script>

<style lang="scss">
/* <СТИЛИ>================================================================================ */
.notifications-list {
    z-index: 800;
    position: fixed;
    right: 20px;
    bottom: 20px;
    width: 500px;
    max-height: 75vh;
    overflow: auto;

    @media (max-width: 767px) {
        max-height: 40vh;
        padding-left: 15px;
        padding-right: 15px;
    }

    @media (max-width: 529px) {
        right: 0;
        left: 0;
        width: auto;
    }
}

/* <СТИЛИ>================================================================================ */

/* <АНИМАЦИИ>================================================================================ */
.notifications-list-enter-active,
.notifications-list-leave-active,
.notifications-list-move,
.notifications-list-item-enter-active,
.notifications-list-item-leave-active,
.notifications-list-item-move {
    transition: all .2s;
}

.notifications-list-enter-from,
.notifications-list-leave-to {
    transform: translateY(100vh);
}

.notifications-list-enter-to,
.notifications-list-leave-from {
    transform: translateY(0);
}

.notifications-list-item-enter-from,
.notifications-list-item-leave-to {
    transform: translateX(-100%);
}

.notifications-list-item-enter-to,
.notifications-list-item-leave-from {
    transform: translateX(0);
}

/* <АНИМАЦИИ>================================================================================ */
</style>