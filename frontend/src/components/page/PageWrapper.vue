<template>
    <div class="wrapper">
        <PageHeader></PageHeader>
        <div class="content">
            <slot></slot>
        </div>
        <PageFooter></PageFooter>
    </div>
</template>

<script>
import PageHeader from '@/components/page/PageHeader.vue'
import PageFooter from '@/components/page/PageFooter.vue'

export default {
    name: 'PageWrapper',
    components: {
        PageHeader,
        PageFooter,
    },
    data() {
        return {
           
        }
    },
    computed: {

    },
};
</script>

<style></style>