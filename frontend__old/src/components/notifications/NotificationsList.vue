<template>
    <TransitionGroup name="popup-card">
        <!-- <PopupNotification v-for="popup in notificationsList" :key="popup.id" :popupData="popup"></PopupNotification> -->
    </TransitionGroup>
</template>

<script>
import PopupNotification from "@/components/notifications/PopupNotification.vue";

export default {
    name: "NotificationsList",
    components: { PopupNotification },
};
</script>

<style>
.popup-card-enter-active,
.popup-card-leave-active {
    transition: all 0.35s;
}

.popup-card-enter-from {
    transform: translate(0, 100vh);
}

.popup-card-enter-to {
    transform: translate(0, var(--popupTranslate));
}

.popup-card-leave-to {
    transform: translate(150%, 0);
}
</style>