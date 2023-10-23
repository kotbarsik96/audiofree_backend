<template>
    <div class="image-load">
        <h4 v-if="$slots.title" class="image-load__title">
            <slot name="title"></slot>
        </h4>
        <Transition name="grow">
            <div v-if="error" class="error image-load__error--top">
                {{ error }}
            </div>
        </Transition>
        <div class="image-load__wrapper" @click="openExplorer">
            <LoadingScreen v-if="isLoading"></LoadingScreen>
            <Transition name="scale-up" mode="out-in">
                <div v-if="modelValue" class="image-load__container">
                    <button class="image-load__remove" type="button" @click.stop="removeImage">
                        <TrashCanCircleIcon></TrashCanCircleIcon>
                    </button>
                    <img class="image-load__image" :src="src" :alt="alt">
                </div>
                <div v-else class="image-load__icon">
                    <PlusCircleIcon></PlusCircleIcon>
                </div>
            </Transition>
            <input type="file" accept="image/png, image/jpeg" ref="input" @change="onChange">
        </div>
        <Transition name="grow">
            <div v-if="errors.image" class="error">
                {{ errors.image[0] }}
            </div>
        </Transition>
    </div>
</template>

<script>
import axios from 'axios'
import LoadingScreen from '@/components/page/LoadingScreen.vue'

export default {
    name: 'ImageLoad',
    emits: ['update:modelValue', 'update:id'],
    props: {
        modelValue: {
            type: String,
            required: true
        },
        id: {
            type: Number,
            required: true
        },
        alt: {
            type: String,
            default: 'Загруженное изображение'
        },
    },
    components: {
        LoadingScreen
    },
    data() {
        return {
            error: '',
            errors: {
                image: ''
            },
            isLoading: false
        }
    },
    computed: {
        src(){
            return `${import.meta.env.VITE_LINK}${this.modelValue}`
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
            this.isLoading = true

            this.nullifyErrors()
            const file = this.$refs.input.files[0]
            if (!file)
                return

            const data = new FormData()
            data.append('image', file)
            try {
                const res = await axios.post(import.meta.env.VITE_IMAGE_LOAD_LINK, data)
                if (res.data.path) {
                    this.$emit('update:modelValue', `${import.meta.env.VITE_LINK}${res.data.path}`)
                    this.$emit('update:id', res.data.id)
                }
            } catch (err) {
                const errorsList = err.response.data.errors
                if (errorsList && typeof errorsList === 'object')
                    this.errors = errorsList
                else
                    this.error = 'Произошла ошибка'
            }

            this.isLoading = false
            this.nullifyFileList()
        },
        async removeImage() {
            this.isLoading = true
            this.nullifyErrors()

            const path = this.modelValue.replace(import.meta.env.VITE_LINK, '')
            try {
                const res = await axios.delete(import.meta.env.VITE_IMAGE_DELETE_LINK, {
                    data: { path }
                })
                if (res.data.success)
                    this.$emit('update:modelValue', '')
                this.$emit('update:id', 0)
            } catch (err) {
                this.error = err.response.data.error
            }

            this.nullifyFileList()
            this.isLoading = false
        },
        nullifyFileList() {
            const dt = new DataTransfer()
            this.$refs.input.files = dt.files
        },
    }
}
</script>

<style lang="scss">
.image-load {
    position: relative;
    width: 400px;

    &__title {
        font-size: 21px;
        line-height: 24px;
        font-weight: 500;
        margin-bottom: 5px;
    }

    &__error--top {
        margin-bottom: 5px;
    }

    &__wrapper {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #F4F4F4;
        position: relative;
        width: 100%;
        height: 227px;
    }

    &__container {
        position: relative;
        width: 100%;
        height: 100%;
    }

    &__remove {
        position: absolute;
        right: -45px;
        top: 0px;
        width: 35px;
        height: 35px;
        color: var(--error_color);

        svg {
            width: 100%;
            height: 100%;
        }
    }

    &__icon {
        width: 51px;
        height: 51px;
        color: #000;

        svg {
            width: 100%;
            height: 100%;
        }
    }

    &__image {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    input {
        display: none;
    }
}
</style>