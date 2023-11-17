<template>
    <div class="images-gallery">
        <Transition name="grow">
            <div class="error images-gallery__error" v-if="errorMessage">
                {{ errorMessage }}
            </div>
        </Transition>
        <Transition name="grow">
            <div class="images-gallery__buttons" v-if="modelValue.length > 0">
                <button class="images-gallery__remove-multiple" v-if="selectedItems.length > 0 && modelValue.length > 0"
                    type="button" @click.stop="removeSelected">
                    <TrashCanCircleIcon></TrashCanCircleIcon>
                </button>
            </div>
        </Transition>
        <ul class="images-gallery__list" ref="galleryList">
            <LoadingScreen v-if="isLoading"></LoadingScreen>
            <TransitionGroup name="grow">
                <li class="images-gallery__item" :class="{ '__selected': selectedItems.includes(obj.id) }"
                    v-for="obj in modelValue" :key="obj.id" :data-id="obj.id" ref="galleryItem"
                    @click="onItemPointerdown($event, obj.id)">
                    <div class="images-gallery__image-container">
                        <button class="images-gallery__remove" type="button" @click="removeImage(obj)">
                            <TrashCanCircleIcon></TrashCanCircleIcon>
                        </button>
                        <button class="images-gallery__edit" type="button" @click="openExplorer(obj)">
                            <PencilIcon></PencilIcon>
                        </button>
                        <ImagePicture class="images-gallery__image" :obj="obj" :alt="obj.id.toString()"></ImagePicture>
                    </div>
                    <div class="images-gallery__image-original-name">
                        <span>
                            {{ obj.original_name }}
                        </span>
                        <span v-if="obj.uploader_email">
                            <br>
                            ({{ obj.uploader_email }})
                        </span>
                    </div>
                </li>
            </TransitionGroup>
            <li class="images-gallery__add-image">
                <button type="button" @click="onAddClick()">
                    <PlusCircleIcon></PlusCircleIcon>
                </button>
            </li>
        </ul>
        <input type="file" accept="image/png, image/jpeg" name="image" :multiple="!updatingImage" ref="input"
            @change="onChange">
    </div>
</template>

<script>
import axios from 'axios'
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import { nextTick } from 'vue'
import { handleAjaxError } from '@/assets/js/scripts.js'
import { useModalsStore } from '@/stores/modals.js'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import { h } from 'vue'

export default {
    name: 'ImagesGallery',
    props: {
        modelValue: {
            type: Array,
            validator(arr) {
                for (let obj of arr) {
                    if (!obj.id || typeof obj.id !== 'number')
                        return false
                    if (!obj.original_name || typeof obj.original_name !== 'string')
                        return false
                }
                return true
            },
            default: []
        },
        isSubgallery: Boolean,
        selected: Array,
        singleSelect: Boolean,
    },
    components: {
        LoadingScreen
    },
    emits: ['update:modelValue', 'update:selected'],
    data() {
        return {
            error: '',
            errors: [],
            isLoading: false,
            selectedItems: [],
            updatingImage: null
        }
    },
    methods: {
        nullifyFileList() {
            const dt = new DataTransfer()
            this.$refs.input.files = dt.files
        },
        nullifyErrors() {
            this.errors = []
            this.error = ''
        },
        onDocumentClick(event) {
            const gallItem = event.target.closest('.images-gallery__item')
            if (!gallItem)
                this.selectedItems = []
        },
        onItemPointerdown(event, imageId) {
            const index = this.selectedItems.indexOf(imageId)

            if (!event.ctrlKey || this.singleSelect)
                this.selectedItems = []

            if (index >= 0) {
                this.selectedItems.splice(index, 1)
            } else {
                this.selectedItems.push(imageId)
            }
        },
        onAddClick() {
            if (this.isSubgallery) {
                const component = h(ConfirmModal, {
                    onlyConfirm: true,
                    confirmProps: {
                        text: 'Загрузить из галереи на сайте',
                        callback: this.createModalGallery
                    },
                    confirmButtons: [
                        {
                            text: 'Загрузить с устройства',
                            callback: () => this.openExplorer()
                        }
                    ]
                })
                useModalsStore().addModal({ component })
            } else
                this.openExplorer()
        },
        async createModalGallery() {
            const callback = (modalCtx, selectedIds, selectedGallery) => {
                if (selectedIds.length < 1)
                    return

                const updatedModelValue = this.concatToModelValue({ result: selectedGallery })
                this.$emit('update:modelValue', updatedModelValue)
            }

            useModalsStore().addModal({
                component: 'GalleryModal',
                props: {
                    title: 'Выбрать изображение',
                    withPagination: true,
                    confirmData: { callback }
                },
            })
        },
        openExplorer(obj = null) {
            this.updatingImage = obj
            // nextTick(), чтобы успел примениться атрибут multiple
            nextTick().then(() => this.$refs.input.click())
        },
        concatToModelValue(resData) {
            const array = Array.isArray(resData.result) ? resData.result : [resData]
            return this.modelValue
                .filter(obj => !array.find(o => o.id === obj.id))
                .concat(array)
        },
        async onChange() {
            if (this.updatingImage) {
                this.updateImage()
                return
            }

            this.nullifyErrors()
            this.isLoading = true

            const files = this.$refs.input.files
            const data = new FormData()
            for (let file of files) {
                data.append('images[]', file)
            }

            try {
                const res = await axios.post(import.meta.env.VITE_IMAGE_LOAD_LINK, data)
                if (res.data.error)
                    throw new Error({ response: res })

                const updatedModelValue = this.concatToModelValue(res.data)
                this.$emit('update:modelValue', updatedModelValue)
            } catch (err) {
                handleAjaxError(err, this)
            }

            this.nullifyFileList()
            this.isLoading = false
        },
        async updateImage() {
            if (!this.updatingImage)
                return

            this.isLoading = true
            this.nullifyErrors()

            const image = this.$refs.input.files[0]
            const data = new FormData()
            data.append('image', image)

            const link = `${import.meta.env.VITE_IMAGE_UPDATE_LINK}${this.updatingImage.id}`
            try {
                const res = await axios.post(link, data)
                const updatedModelValue = this.concatToModelValue(res.data)
                this.$emit('update:modelValue', updatedModelValue)
            } catch (err) {
                handleAjaxError(err, this)
            }

            this.nullifyFileList()
            this.isLoading = false
            this.updatingImage = null
        },
        async removeSelected() {
            this.nullifyErrors()

            const updatedModelValue = this.modelValue
                .filter(obj => !this.selectedItems.includes(obj.id))
            // если подгалерея - уберет только из подгалереи
            if (this.isSubgallery) {
                this.untagImages(this.selectedItems)
                this.$emit('update:modelValue', updatedModelValue)
            }
            // в обычной галерее пошлет запрос на бекенд на удаление изображения с сервера
            else {
                this.isLoading = true

                const link = `${import.meta.env.VITE_IMAGE_DELETE_LINK}`
                try {
                    const res = await axios.delete(link, {
                        data: {
                            idsList: this.selectedItems
                        }
                    })
                    if (!res.data.success)
                        throw new Error()

                    this.$emit('update:modelValue', updatedModelValue)
                } catch (err) {
                    handleAjaxError(err, this)
                }

                this.isLoading = false
            }

            this.selectedItems = []
        },
        async removeImage(obj) {
            const updatedModelValue = this.modelValue.filter(o => o.id !== obj.id)
            // в подгалерее удалит только из подгалереи, НЕ удалит с сервера
            if (this.isSubgallery) {
                this.untagImages([obj.id])
                this.$emit('update:modelValue', updatedModelValue)
                return
            }

            // в обычной галерее пошлет запрос на удаление на сервер
            this.nullifyErrors()
            this.isLoading = true

            const link = `${import.meta.env.VITE_IMAGE_DELETE_LINK}${obj.id}`
            try {
                const res = await axios.delete(link)
                if (!res.data.success)
                    throw new Error()

                this.$emit('update:modelValue', updatedModelValue)
            } catch (err) {
                handleAjaxError(err, this)
            }

            this.isLoading = false
        },
        async untagImages(idsList) {
            const link = import.meta.env.VITE_IMAGES_TAG_LINK

            try {
                const res = await axios.post(link, {
                    images: idsList,
                    tag: null
                })
            } catch (err) { }
        }
    },
    computed: {
        errorMessage() {
            if (this.error.length > 0)
                return this.error

            if (Array.isArray(this.errors.image) && this.errors.image[0])
                return this.errors.image[0]

            return ''
        },
        selectedItemsData() {
            return this.selectedItems
                .map(id => this.modelValue.find(obj => obj.id === id))
                .filter(obj => obj)
        },
    },
    watch: {
        selectedItems: {
            deep: true,
            handler() {
                this.$emit('update:selected', this.selectedItems)
            }
        }
    },
    mounted() {
        document.addEventListener('click', this.onDocumentClick)
    },
    beforeUnmount() {
        document.removeEventListener('click', this.onDocumentClick)
    }
}
</script>

<style lang="scss">
.images-gallery {
    --background_color: #f4f4f4;
    position: relative;
    display: inline-block;
    min-width: 700px;

    &__list {
        overflow: hidden;
        position: relative;
        background-color: var(--background_color);
        padding: 50px 20px 20px 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(0, 200px));
        grid-gap: 30px;
        max-width: 700px;
        width: 100%;
        min-height: 280px;
    }

    &__error {
        margin-bottom: 5px;
    }

    &__item {
        cursor: default;

        &.__selected {
            background-color: rgba(0, 84, 180, 0.4);
        }
    }

    &__image-container {
        width: 200px;
        height: 200px;
        position: relative;
        border: 1px solid rgba(0, 0, 0, .25);
    }

    &__remove {
        right: 5px;
        color: var(--error_color);
    }

    &__edit {
        right: 40px;
        color: var(--theme_color);
    }

    &__remove,
    &__edit {
        position: absolute;
        top: 5px;
        width: 25px;
        height: 25px;

        svg {
            color: inherit;
            width: 100%;
            height: 100%;
        }
    }

    &__buttons {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        position: absolute;
        top: 0;
        width: 100%;
        padding: 10px;
        z-index: 20;

        button {
            display: block;
            width: 30px;
            height: 30px;
        }

        svg {
            width: 100%;
            height: 100%;
        }
    }

    &__remove-multiple {
        svg {
            color: var(--error_color);
        }
    }

    &__image {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    &__image-original-name {
        margin-top: 5px;
        font-size: 16px;
        line-height: 19px;
        text-align: center;
        word-wrap: break-word;
    }

    &__add-image {
        align-self: center;

        button {
            width: 40px;
            height: 40px;

            svg {
                width: 100%;
                height: 100%;
            }
        }
    }

    input {
        display: none;
    }

    @media (max-width: 719px) {
        min-width: unset;
    }

    @media (max-width: 499px) {
        &__list {
            padding: 50px 10px 10px 10px;
            grid-template-columns: repeat(auto-fit, minmax(0, 125px));
            grid-gap: 15px;
            min-height: 170px;
        }

        &__image-container {
            width: 115px;
            height: 115px;
        }
    }

    @media (max-width: 399px) {
        &__list {
            justify-content: center;
        }
    }
}
</style>