<template>
    <div class="notification">
        <div class="notification__close">
            <button class="close-button" type="button" @click="removeNotification"></button>
        </div>
        <div class="notification__body" v-html="message"></div>
        <div class="notification__progress-bar" ref="progressBar">
            <div class="notification__progress-scale" ref="progressScale"></div>
        </div>
    </div>
</template>

<script>
import { useNotificationsStore } from '@/stores/notifications.js'
import { gsap } from 'gsap'

export default {
    name: 'NotificationItem',
    props: {
        timeout: {
            type: Number,
            default: 2000
        },
        message: [String, Number],
    },
    mounted() {
        const progressScale = this.$refs.progressScale
        gsap.from(progressScale, {
            scaleX: 0,
            duration: this.timeout / 1000,
            ease: 'none'
        })
        this.setTimeoutFunc = setTimeout(this.removeNotification, this.timeout)
    },
    beforeUnmount() {
        clearTimeout(this.setTimeoutFunc)
    },
    data() {
        return {
            notificationId: null
        }
    },
    methods: {
        removeNotification() {
            const store = useNotificationsStore()
            store.removeNotification(this.notificationId)
        }
    }
}
</script>

<style lang="scss">
.notification {
    margin-top: 10px;
    background-color: #fff;
    border-radius: var(--border_radius);

    &__close {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 15px 15px 5px 15px;
    }

    &__body {
        font-size: 19px;
        text-align: center;
        padding: 0 15px 15px 15px;
    }

    &__progress-bar {
        position: relative;
        width: 100%;
        height: 7px;
        background-color: #bdbdbd;
        border-radius: 0 0 var(--border_radius) var(--border_radius);
        overflow: hidden;
    }

    &__progress-scale {
        position: absolute;
        top: 0;
        left: 0;
        background-color: var(--theme_color);
        width: 100%;
        height: 100%;
        transform-origin: left center;
    }
}
</style>