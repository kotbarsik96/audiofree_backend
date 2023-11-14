<template>
    <div class="images-gallery">
        <Transition name="grow">
            <div class="error images-gallery__error" v-if="errorMessage">
                {{ errorMessage }}
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
                    <div class="images-gallery__image-path">
                        {{ obj.path }}
                    </div>
                </li>
            </TransitionGroup>
            <li class="images-gallery__add-image">
                <button type="button" @click="openExplorer(null)">
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

export default {
    name: 'ImagesGallery',
    props: {
        /* modelValue[i]: { id: Number, path: String,  } */
        modelValue: {
            type: Array,
            required: true,
            validator(arr) {
                for (let obj of arr) {
                    if (!obj.id || typeof obj.id !== 'number')
                        return false
                    if (!obj.path || typeof obj.path !== 'string')
                        return false
                }
                return true
            }
        },
    },
    components: {
        LoadingScreen
    },
    data() {
        return {
            error: '',
            errors: [],
            isLoading: false,
            selectedItems: [],
            updatingImage: null
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
        }
    },
    methods: {
        onDocumentClick(event) {
            const gallItem = event.target.closest('.images-gallery__item')
            if (!gallItem)
                this.selectedItems = []
        },
        onItemPointerdown(event, imageId) {
            const index = this.selectedItems.indexOf(imageId)

            if (!event.ctrlKey)
                this.selectedItems = []

            if (index >= 0) {
                this.selectedItems.splice(index, 1)
            } else {
                this.selectedItems.push(imageId)
            }
        },
        openExplorer(obj = null) {
            this.updatingImage = obj
            // nextTick(), чтобы успел примениться атрибут multiple
            nextTick().then(() => this.$refs.input.click())
        },
        nullifyErrors() {
            this.errors = []
            this.error = ''
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
                if (Array.isArray(res.data.images))
                    this.$emit('update:modelValue', this.modelValue.concat(res.data.images))
                if (res.data.error) {
                    if (typeof res.data.error === 'string')
                        this.error = res.data.error
                    else if (typeof res.data.error === 'object')
                        this.errors = res.data.error
                }
            } catch (err) {
                const data = err.response.data
                if (data.errors && typeof data.errors === 'object')
                    this.errors = data.errors

                if (data.error)
                    this.error = data.error
            }

            this.nullifyFileList()
            this.isLoading = false
        },
        async removeImage(obj) {
            this.nullifyErrors()
            this.isLoading = true
            const link = `${import.meta.env.VITE_IMAGE_DELETE_LINK}${obj.id}`
            try {
                const res = await axios.delete(link)
                if (res.data.success) {
                    const updatedModelValue = [...this.modelValue]
                    const index = updatedModelValue.findIndex(o => o.id === obj.id)
                    if (index >= 0) {
                        updatedModelValue.splice(index, 1)
                        this.$emit('update:modelValue', updatedModelValue)
                    }
                }
            } catch (err) {
                const data = err.response.data
                if (data.error)
                    this.error = data.error
                else if (data.errors && typeof data.errors === 'object')
                    this.errors = data.errors
                else
                    this.error = 'Произошла ошибка'
            }

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
                if (res.data.id) {
                    const updatedModelValue = this.modelValue.map(obj => {
                        if (obj.id !== res.data.id)
                            return obj
                        return res.data
                    })
                    this.$emit('update:modelValue', updatedModelValue)
                }
            } catch (err) {
                const data = err.response.data
                if (data.error)
                    this.error = data.error

                if (data.errors)
                    this.errors = data.errors
            }

            this.nullifyFileList()
            this.isLoading = false
            this.updatingImage = null
        },
        nullifyFileList() {
            const dt = new DataTransfer()
            this.$refs.input.files = dt.files
        },
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

    &__list {
        overflow: hidden;
        position: relative;
        background-color: var(--background_color);
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(0, 200px));
        grid-gap: 30px;
        max-width: 700px;
        min-height: 280px;
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

    &__image {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    &__image-path {
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

    @media (max-width: 499px) {
        &__list {
            padding: 10px;
            grid-template-columns: repeat(auto-fit, minmax(0, 125px));
            grid-gap: 15px;
            min-height: 170px;
        }

        &__image-container {
            width: 125px;
            height: 125px;
        }
    }
}
</style>