<template>
    <div class="popup card" ref="popup">
        <div class="popup__bottom card__bottom card__side"></div>
        <div class="popup__container card__container card__side" ref="popupContainer">
            <div class="popup__timeline" ref="timeline"></div>
            <div class="popup__cancel" @click="removeNotification(popupData.id)">
                <div class="popup__cancel-button close-button"></div>
            </div>
            <p class="popup__note" v-for="(message, index) in popupMessage" :key="index" :ref="`note-${index}`">{{
                insertMessage(message, index) }}</p>
        </div>
    </div>
</template>

<script>
import { gsap } from "gsap";

export default {
    name: "PopupNotification",
    props: {
        popupData: Object,
    },
    data() {
        return {
            timetolive: 3000, // по умолчанию, может быть перезаписано
        };
    },
    computed: {
        //   ...mapGetters(["notificationsList"]),
    },
    methods: {
        setTimelineAnimation() {
            const tline = this.$refs.timeline;
            if (tline) {
                this.$nextTick().then(() => {
                    const timetolive = this.timetolive / 1000;
                    const popupWidth = this.$refs.popupContainer.offsetWidth;

                    gsap.to(tline, {
                        duration: timetolive,
                        width: popupWidth,
                        ease: "none",
                    });
                });
            }
        },
    },
    mounted() {
        this.setTimelineAnimation();
    },
};
</script>