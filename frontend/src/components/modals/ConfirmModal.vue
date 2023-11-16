<template>
    <div class="modal confirm-modal">
        <div class="modal__close">
            <button class="close-button" type="button" @click="removeModal(declineData)"></button>
        </div>
        <h3 v-if="title" class="modal__title">
            {{ title }}
        </h3>
        <div class="modal__body">
            <div v-if="text" class="confirm-modal__text" v-html="text"></div>
            <div v-if="nestedComponentsList.length > 0" class="confirm-modal__nested-component">
                <component v-for="comp in nestedComponentsList" :is="comp" ref="nestedComponentRef"></component>
            </div>
            <div class="modal__buttons confirm-modal__buttons">
                <button class="modal__button button button--colored" type="button" @click="removeModal(confirmData)">
                    {{ confirmData.text || defaultConfirmText }}
                </button>
                <button v-for="otherConfirmData in confirmButtons" class="modal__button button button--colored"
                    type="button" @click="removeModal(otherConfirmData)">
                    {{ otherConfirmData.text || defaultConfirmText }}
                </button>
                <button class="modal__button button" v-if="!onlyConfirm" type="button" @click="removeModal(declineData)">
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
        nestedComponent: Object, // h(NestedComponentName, nestedComponentProps = {}, () => nestedComponentSlot),
        nestedComponents: Array, // элементы массива - то же, что и nestedComponent
        /* ЕСЛИ В confirmProps ИЛИ declineProps передаются callbackArgs, ТО НУЖНО УЧИТЫВАТЬ, ЧТО ПРИ ВЫЗОВЕ МЕТОДА ПЕРВЫМ В ЭТОТ МЕТОД БУДЕТ ПЕРЕДАН КОНТЕКСТ МОДАЛЬНОГО ОКНА this */
        confirmProps: Object,
        declineProps: Object,
        /* массив объектов, подобных this.confirmProps (когда нужно несколько подтверждающих кнопок) */
        confirmButtons: Array,
        modalId: [String, Number],
        onlyConfirm: Boolean
    },
    data() {
        return {
            defaultConfirmText: 'Подтвердить',
            defaultDeclineText: 'Отменить',
            closed: false
        }
    },
    computed: {
        confirmData() {
            return this.confirmProps || { text: this.defaultConfirmText }
        },
        declineData() {
            if (this.onlyConfirm && !Array.isArray(this.confirmButtons))
                return { callback: this.confirmData.callback }

            return this.declineProps || { text: this.defaultDeclineText }
        },
        nestedComponentsList() {
            const arr = []
            if (Array.isArray(this.nestedComponents))
                arr.push(...this.nestedComponents)
            if (this.nestedComponent)
                arr.push(this.nestedComponent)

            return arr
        }
    },
    methods: {
        removeModal(data) {
            const modalsStore = useModalsStore()
            modalsStore.removeModal(this.modalId)
            this.applyCallbacks(data)
        },
        applyCallbacks(data) { // data === this.confirmData|this.declineData
            if (this.closed)
                return

            this.closed = true
            const callback = data.callback
            const callbackArgs = Array.isArray(data.callbackArgs)
                ? data.callbackArgs
                : []
            callbackArgs.unshift(this)
            if (typeof callback === 'function') {
                callback(...callbackArgs)
            }
        },
        onModalDeleted(event) {
            if (!event)
                return

            if (event.detail && event.detail.id) {
                if (event.detail.id === this.modalId)
                    this.applyCallbacks(this.declineData)
            }
        }
    },
    created() {
        document.addEventListener('modal-deleted', this.onModalDeleted)
    },
    beforeUnmount() {
        document.removeEventListener('modal-deleted', this.onModalDeleted)
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

    &__text {
        text-align: center;
        font-size: 16px;
        line-height: 18px;
        padding: 0 15px;
    }
}
</style>