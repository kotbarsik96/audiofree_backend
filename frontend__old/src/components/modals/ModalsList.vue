<template>
    <Transition name="modals-list">
        <div class="modals-list">
            <Transition name="modal">
                <component :is="firstModal.component" @close="removeFirstModal"></component>
            </Transition>
        </div>
    </Transition>
</template>

<script>
import RegisterModal from './RegisterModal.vue';
import { mapState, mapActions } from 'pinia';
import { useModalsStore } from '@/stores/modals.js';

export default {
    name: 'ModalsList',
    components: {
        RegisterModal
    },
    data() {
        return {

        }
    },
    computed: {
        ...mapState(useModalsStore, ['modals']),
        firstModal() {
            return this.modals[0] || null;
        }
    },
    methods: {
        ...mapActions(useModalsStore, ['removeFirstModal'])
    }
}
</script>

<style>
.modals-list-enter-active,
.modals-list-leave-active,
.modal-enter-active,
.modal-leave-active {
    transition: all .3s;
}

.modals-list-enter-from,
.modals-list-leave-to,
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modals-list-enter-to,
.modals-list-leave-from,
.modal-enter-to,
.modal-leave-from {
    opacity: 1;
}
</style>