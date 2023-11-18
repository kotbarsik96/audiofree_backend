<template>
    <component class="with-gallery" :is="tag" @click="onClick">
        <Transition name="grow">
            <button v-if="showOpenButton && isAdmin" class="button button--second-color button with-gallery__edit"
                type="button" @click="openGallery">
                Открыть галерею
            </button>
        </Transition>
        <slot></slot>
    </component>
</template>

<script>
import { mapState } from 'pinia'
import { useIndexStore } from '@/stores/'
import { useModalsStore } from '@/stores/modals.js'
import { useNotificationsStore } from '@/stores/notifications.js'
import GalleryModal from '@/components/modals/GalleryModal.vue'
import { h } from 'vue'
import axios from 'axios'

export default {
    name: 'ComponentWithGallery',
    emits: ['update:modelValue'],
    props: {
        tag: String,
        imageTag: {
            type: String,
            required: true
        },
        modalTitle: String,
        isSingleSelect: Boolean,
        modelValue: Array
    },
    data() {
        return {
            showOpenButton: false,
            isLoading: false,
            gallery: []
        }
    },
    computed: {
        ...mapState(useIndexStore, ['isAdmin', 'isMobileBrowser']),
    },
    methods: {
        onClick(event) {
            const callback = () => {
                if (this.isLoading)
                    return

                this.showOpenButton = !this.showOpenButton
            }

            if (this.isMobileBrowser) {
                this.userTapped = true
                this.$el.addEventListener('click', callback)
                setTimeout(() => {
                    this.$el.removeEventListener('click', callback)
                }, 500);
            } else {
                if (event.ctrlKey)
                    callback()
            }
        },
        openGallery() {
            if (!this.isAdmin || this.isLoading)
                return

            const callback = async (modalWindowCtx, selectedIds, selectedItems) => {
                const link = import.meta.env.VITE_IMAGES_TAG_LINK

                try {
                    const images = modalWindowCtx.gallery
                        .map(obj => obj.id)
                    const res = await axios.post(link, {
                        images,
                        tag: this.imageTag
                    })
                    if (!res.data.success)
                        throw new Error()

                    this.loadGallery()

                    useNotificationsStore().addNotification({
                        message: 'Галерея сохранена'
                    })
                } catch (err) {
                    useNotificationsStore().addNotification({
                        message: 'Произошла ошибка при сохранении галереи'
                    })
                }
            }

            const component = h(GalleryModal, {
                title: this.modalTitle || 'Галерея',
                singleSelect: Boolean(this.isSingleSelect),
                confirmData: {
                    text: 'Сохранить',
                    callback,
                },
                imageTag: this.imageTag,
                isSubgallery: true,
                withPagination: false,
                propsGallery: this.gallery,
                onUpdateGallery: () => {
                    this.loadGallery()
                },
                alwaysActiveConfirm: true
            })

            useModalsStore().addModal({ component })
        },
        async loadGallery() {
            const link = import.meta.env.VITE_GALLERY_GET_LINK
            this.isLoading = true

            try {
                const res = await axios.get(link, { params: { tag: this.imageTag } })
                if (!Array.isArray(res.data.result))
                    throw new Error()

                this.gallery = res.data.result
            } catch (err) { }

            this.isLoading = false
        },
    },
    watch: {
        gallery: {
            deep: true,
            handler() {
                this.$emit('update:modelValue', this.gallery)
            }
        }
    },
    created() {
        this.loadGallery()
    }
}
</script>

<style lang="scss">
.with-gallery {
    position: relative;

    &__edit {
        position: absolute;
        z-index: 15;
        top: 10px;
        right: 10px;
        font-size: 16px;
    }
}
</style>