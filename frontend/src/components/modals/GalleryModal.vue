<template>
    <div class="modal gallery-modal">
        <div class="modal__close">
            <button class="close-button" type="button" @click="removeModal()"></button>
        </div>
        <h3 class="modal__title">
            {{ title || 'Галерея' }}
        </h3>
        <component :is="imagesGalleryComponent" :isSubgallery="isSubgallery" :singleSelect="singleSelect"
            :imageTag="imageTag" noRouter v-model="gallery" v-model:selected="selected" @update:modelValue="onGalleryUpdate"></component>
        <div class="modal__buttons">
            <button class="modal__button button button--colored" type="button" @click="onConfirmClick(confirmData)"
                :disabled="selected.length < 1 && !alwaysActiveConfirm">
                {{ confirmData.text || 'Принять выбранные изображения' }}
            </button>
            <button class="modal__button button button--colored" v-for="obj in confirmButtons" type="button"
                @click="onConfirmClick(obj)" :disabled="selected.length < 1">
                {{ obj.text || 'Принять' }}
            </button>
        </div>
    </div>
</template>

<script>
import { useModalsStore } from '@/stores/modals.js'
import ImagesGallery from '@/components/inputs/images/ImagesGallery.vue'
import ImagesGalleryPageable from '@/components/inputs/images/ImagesGalleryPageable.vue'
import { delay } from '@/assets/js/scripts.js'


export default {
    name: 'GalleryModal',
    components: {
        ImagesGalleryPageable,
        ImagesGallery
    },
    props: {
        modalId: [String, Number],
        title: String,
        confirmData: Object,
        singleSelect: Boolean,
        /* элементы массива - то же самое, что и confirmData. Используется, когда нужно несколько кнопок подтверждения с разными callback'ами */
        confirmButtons: {
            type: Array,
            default: []
        },
        imageTag: String,
        isSubgallery: Boolean,
        withPagination: Boolean,
        propsGallery: Array,
        alwaysActiveConfirm: Boolean
    },
    emits: ['updateGallery'],
    data() {
        return {
            selected: [],
            gallery: []
        }
    },
    methods: {
        removeModal() {
            const modalsStore = useModalsStore()
            modalsStore.removeModal(this.modalId)
        },
        onConfirmClick(confirmData) {
            if (confirmData && typeof confirmData.callback === 'function') {
                confirmData.callback(
                    this,
                    [...this.selected],
                    this.selected.map(id => this.gallery.find(obj => obj.id === id))
                )
            }
            this.removeModal()
        },
        onGalleryUpdate(){
            this.$emit('updateGallery', this.gallery)
        }
    },
    computed: {
        imagesGalleryComponent() {
            if (this.withPagination)
                return ImagesGalleryPageable

            return ImagesGallery
        }
    },
    mounted() {
        const observer = new MutationObserver(async () => {
            await delay(500)
            window.dispatchEvent(new Event('resize'))
        })
        observer.observe(this.$el, { childList: true, subtree: true })

        if(this.propsGallery)
            this.gallery = this.propsGallery

        if (!this.withPagination && !this.propsGallery)
            this.loadGallery()
    }
}
</script>

<style lang="scss">
.gallery-modal {
    align-self: flex-start;

    .modal__buttons {
        margin-top: 10px;
    }

    .images-gallery {
        width: 100%;
    }
}
</style>