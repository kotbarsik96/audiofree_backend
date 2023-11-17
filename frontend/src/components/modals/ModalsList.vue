<template>
    <Transition name="modals-list">
        <div v-if="firstModal" class="modals-list" ref="modalsList">
            <TransitionGroup name="modal">
                <component v-for="(modal, index) in modals" v-show="index === 0" :is="modal.component" :modalId="modal.id"
                    :key="modal.id" ref="modalComponent"></component>
            </TransitionGroup>
        </div>
    </Transition>
</template>

<script>
import RegisterModal from '@/components/modals/auth/AuthModal.vue'
import { mapState, mapActions } from 'pinia'
import { useModalsStore } from '@/stores/modals.js'
import { nextTick } from 'vue'
import { useIndexStore } from '@/stores/'

export default {
    name: 'ModalsList',
    components: {
        RegisterModal
    },
    data() {
        return {
            observer: null,
            refreshKey: 1,
            alignTimeout: null
        }
    },
    created() {
        document.addEventListener('keyup', (event) => {
            if (event.key === 'Escape')
                this.removeModal()
        })
    },
    computed: {
        ...mapState(useModalsStore, ['modals']),
        firstModal() {
            this.onResize()
            return this.modals[0] || null;
        }
    },
    methods: {
        ...mapActions(useModalsStore, ['removeModal']),
        onResize() {
            if(this.alignTimeout)
                clearTimeout(this.alignTimeout)

            this.alignTimeout = setTimeout(this.alignModals, 500);
        },
        async alignModals() {
            await nextTick()
            if (!this.$refs.modalsList)
                return

            const modalEl = this.$refs.modalsList.querySelector('.modal')
            const height = modalEl.offsetHeight
            const windowHeight = document.documentElement.clientHeight
            if (height > windowHeight - 40)
                modalEl.style.alignSelf = 'flex-start'
            else
                modalEl.style.alignSelf = 'center'
        }
    },
    watch: {
        async firstModal() {
            if (this.firstModal) {
                useIndexStore().toggleScroll('modals-list', true, true)
            } else {
                useIndexStore().toggleScroll('modals-list', false)
            }
        },
        modals: {
            deep: true,
            handler() {
                setTimeout(() => {

                }, 500);
            }
        }
    },
    mounted() {
        window.addEventListener('resize', this.onResize)
        document.addEventListener('click', this.onResize)
        this.onResize()
    }
}
</script>

<style lang="scss">
/* <TRANSITIONS>================================================================================ */
.modals-list {
    --trans_dur: .3s;
}

.modals-list-enter-active,
.modals-list-leave-active,
.modal-enter-active,
.modal-leave-active {
    transition: all var(--trans_dur);
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

/* <TRANSITIONS>================================================================================ */

/* <MODALS-LIST>================================================================================ */
.modals-list {
    position: fixed;
    z-index: 999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    overflow: auto;
    background-color: rgba(0, 0, 0, .5);
}

/* <MODALS-LIST>================================================================================ */

/* <MODAL-STYLES>=============================================================================== */
.modal {
    background-color: #fff;
    border-radius: 9px;
    overflow: hidden;
    max-width: 100%;
    min-width: 500px;
    position: absolute;

    &__close {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 3px 10px;
        width: 100%;
        border: 1px solid #e7e7e7;
    }

    &__tabs {
        display: flex;
    }

    &__tab {
        flex: 1 1 33%;
        padding: 10px;
        background-color: #bbbbbb;
        color: #ffffff;
        font-weight: 500;
        font-size: 16px;
        transition-property: background-color, color;
        transition-duration: var(--trans_dur);
    }

    &__tab.__active {
        background-color: #fff;
        color: #414141;
    }

    &__title {
        font-size: 19px;
        line-height: 24px;
        font-weight: 500;
        margin: 10px auto;
        padding: 0 10px;
        text-align: center;
    }

    &__body {
        padding: 10px;
    }

    &__buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    &__button {
        margin-bottom: 10px;
    }

    .text-input {
        margin-bottom: 15px;
    }

    @media (max-width: 519px) {
        min-width: 0;
        width: 100%;
    }
}

/* <MODAL-STYLES>=============================================================================== */
</style>