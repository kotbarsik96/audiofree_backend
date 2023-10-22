<template>
    <div class="images-gallery">
        <Transition name="grow">
            <div class="error images-gallery__error" v-if="errorMessage">
                {{ errorMessage }}
            </div>
        </Transition>
        <ul class="images-gallery__list">
            <LoadingScreen v-if="isLoading"></LoadingScreen>
            <TransitionGroup name="grow">
                <li class="images-gallery__item" v-for="obj in modelValue" :key="obj.id">
                    <div class="images-gallery__image-container">
                        <button class="images-gallery__remove" type="button" @click="removeImage(obj)">
                            <TrashCanCircleIcon></TrashCanCircleIcon>
                        </button>
                        <img class="images-gallery__image" :src="getSrc(obj.path)" :alt="obj.id">
                    </div>
                    <div class="images-gallery__image-path">
                        {{ obj.path }}
                    </div>
                </li>
            </TransitionGroup>
            <li>
                <button class="images-gallery__add-image" type="button" @click="openExplorer">
                    <PlusCircleIcon></PlusCircleIcon>
                </button>
            </li>
        </ul>
        <input type="file" accept="image/png, image/jpeg" name="image" multiple ref="input" @change="onChange">
    </div>
</template>

<script>
import axios from 'axios'
import LoadingScreen from '@/components/page/LoadingScreen.vue'

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
        }
    },
    components: {
        LoadingScreen
    },
    data() {
        return {
            error: '',
            errors: [],
            isLoading: false
        }
    },
    computed: {
        errorMessage() {
            if (this.error.length > 0)
                return this.error

            if (Array.isArray(this.errors.image) && this.errors.image[0])
                return this.errors.image[0]

            return ''
        }
    },
    methods: {
        openExplorer() {
            this.$refs.input.click()
        },
        nullifyErrors() {
            this.errors = []
            this.error = ''
        },
        async onChange() {
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
        nullifyFileList() {
            const dt = new DataTransfer()
            this.$refs.input.files = dt.files
        },
        getSrc(path) {
            return `${import.meta.env.VITE_LINK}${path}`
        }
    }
}
</script>

<style lang="scss">
.images-gallery {
    --background_color: #f4f4f4;

    &__list {
        position: relative;
        background-color: var(--background_color);
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(0, 200px));
        grid-gap: 30px;
        align-items: center;
        max-width: 700px;
    }

    &__image-container {
        width: 200px;
        height: 200px;
        position: relative;
        border: 1px solid rgba(0, 0, 0, .25);
    }

    &__remove {
        position: absolute;
        right: 5px;
        top: 5px;
        width: 25px;
        height: 25px;

        svg {
            color: var(--error_color);
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
        width: 40px;
        height: 40px;

        svg {
            width: 100%;
            height: 100%;
        }
    }

    input {
        display: none;
    }
}
</style>