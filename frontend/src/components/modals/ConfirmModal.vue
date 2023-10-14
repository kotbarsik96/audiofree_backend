<template>
    <div class="modal confirm-modal">
        <div class="modal__close">
            <button class="close-button" type="button" @click="removeModal"></button>
        </div>
        <h3 v-if="title" class="modal__title">
            {{ title }}
        </h3>
        <div class="modal__body">
            <div v-if="text" class="confirm-modal__text" v-html="text"></div>
            <div v-if="nestedComponent" class="confirm-modal__nested-component">
                <component :is="nestedComponent"></component>
            </div>
            <div class="modal__buttons confirm-modal__buttons">
                <button class="modal__button button button--colored" type="button" @click="onButtonClick(confirmData)">
                    {{ confirmData.text || defaultConfirmText }}
                </button>
                <button class="modal__button button" type="button" @click="onButtonClick(declineData)">
                    {{ declineData.text || defaultDeclineText }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { useModalsStore } from '@/stores/modals.js'

export default {
    name: 'ConfirmModal',
    props: {
        title: String,
        text: [String, Number],
        nestedComponent: Object, // h(NestedComponentName, nestedComponentProps = {})
        confirmProps: Object,
        declineProps: Object
    },
    data() {
        return {
            defaultConfirmText: 'Подтвердить',
            defaultDeclineText: 'Отменить',
            modalId: null
        }
    },
    computed: {
        confirmData() {
            return this.confirmProps || { text: this.defaultConfirmText }
        },
        declineData() {
            return this.declineProps || { text: this.defaultDeclineText }
        }
    },
    methods: {
        removeModal() {
            const modalsStore = useModalsStore()
            modalsStore.removeModal(this.modalId)
        },
        onButtonClick(data) { // data === this.confirmData|this.declineData
            const callback = data.callback
            const callbackArgs = Array.isArray(data.callbackArgs)
                ? data.callbackArgs
                : []
            if (typeof callback === 'function') {
                callback(...callbackArgs)
            }
            this.removeModal()
        },
    }
}
</script>

<style lang="scss">
.confirm-modal {
    &__buttons.modal__buttons {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;

        .button {
            margin: 10px;
        }
    }
}
</style>