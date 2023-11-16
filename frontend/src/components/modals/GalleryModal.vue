<template>
    <div class="modal gallery-modal">
        <div class="modal__close">
            <button class="close-button" type="button" @click="removeModal()"></button>
        </div>
        <h3 class="modal__title">
            {{ title || 'Галерея' }}
        </h3>
        <ImagesGalleryPageable v-model="gallery" v-model:selected="selected" :singleSelect="singleSelect" noRouter></ImagesGalleryPageable>
        <div class="modal__buttons">
            <button class="modal__button button button--colored" type="button" @click="onConfirmClick"
                :disabled="selected.length < 1">
                Принять выбранные изображения
            </button>
        </div>
    </div>
</template>

<script>
import { useModalsStore } from '@/stores/modals.js'
import ImagesGalleryPageable from '@/components/inputs/images/ImagesGalleryPageable.vue'
import { delay } from '@/assets/js/scripts.js'

export default {
    name: 'GalleryModal',
    props: {
        modalId: [String, Number],
        title: String,
        confirmData: Object,
        singleSelect: Boolean
    },
    data() {
        return {
            selected: [],
            gallery: []
        }
    },
    components: {
        ImagesGalleryPageable
    },
    methods: {
        removeModal() {
            const modalsStore = useModalsStore()
            modalsStore.removeModal(this.modalId)
        },
        onConfirmClick() {
            if (this.confirmData && typeof this.confirmData.callback === 'function') {
                this.confirmData.callback(
                    this,
                    [...this.selected],
                    this.selected.map(id => this.gallery.find(obj => obj.id === id))
                )
            }
            this.removeModal()
        }
    },
    mounted() {
        const observer = new MutationObserver(async () => {
            await delay(500)
            window.dispatchEvent(new Event('resize'))
        })
        observer.observe(this.$el, { childList: true, subtree: true })
    }
}
</script>

<style lang="scss">
.gallery-modal {
    .modal__buttons {
        margin-top: 10px;
    }
}
</style>