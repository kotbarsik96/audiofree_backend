<template>
    <div class="wrapper">
        <LoadingScreen v-if="isAppLoading"></LoadingScreen>
        <PageHeader></PageHeader>
        <slot></slot>
        <PageFooter></PageFooter>
    </div>
</template>

<script>
import PageHeader from "@/components/page/PageHeader.vue";
import PageFooter from "@/components/page/PageFooter.vue";
import LoadingScreen from "@/components/page/LoadingScreen.vue";

export default {
    name: "PageWrapper",
    components: {
        PageHeader,
        PageFooter,
        LoadingScreen,
    },
    data() {
        return {
            isAppLoading: false,
        };
    },
    computed: {

    },
    created() {
        this.isAppLoading = true;
    },
    mounted() {
        setTimeout(() => {
            this.isAppLoading = false;
        }, 1000);
    },
};
</script>

<style></style>