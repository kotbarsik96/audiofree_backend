<template>
    <Transition name="notifications-list">
        <div v-if="notifications.length > 0" class="notifications-list">
            <TransitionGroup name="notifications-list">
                <component v-for="notification of notifications" :key="notification.id" :is="notification.component">
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
    max-height: 50vh;
    overflow: auto;

    @media (max-width: 767px){
        max-height: 40vh;
        padding-left: 15px;
        padding-right: 15px;
        
    }

    @media (max-width: 529px){
        right: 0;
        left: 0;
        width: auto;
    }
}

/* <СТИЛИ>================================================================================ */

/* <АНИМАЦИИ>================================================================================ */
.notifications-list-enter-active,
.notifications-list-leave-active,
.notifications-list-move {
    transition: all .5s;
}

.notifications-list-enter-from,
.notifications-list-leave-to {
    transform: translateY(100vw);
}

.notifications-list-enter-to,
.notifications-list-leave-from {
    transform: translateY(0);
}

/* <АНИМАЦИИ>================================================================================ */</style>