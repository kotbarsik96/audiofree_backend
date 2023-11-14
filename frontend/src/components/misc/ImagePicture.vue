<template>
    <picture>
        <source :srcset="supportsWebp ? webpSrc : noWebpSrc">
        <img :src="noWebpSrc" :alt="alt">
    </picture>
</template>

<script>
import { mapState } from 'pinia'
import { useIndexStore } from '@/stores/'
import { getImagePath } from '@/assets/js/methods.js'

export default {
    name: 'ImagePicture',
    props: {
        webpPath: String,
        path: String,
        /* в случае, если не передан webpPath или path, будет искать в объекте ключи (последовательность такая, как описано тут):
            1. для браузеров без поддержки webp: 'image_path', 'path', 'image',
            2. для браузеров с webp: 'image_webp_path', 'webp_path' 'image_webp'. Если не найдено webp изображения, будет идти также, как по noWebpSrc
        ЗАТЕМ, применит метод getImagePath() к полученному свойству объекта, чтобы получить полный путь

        обычно такой объект прилетает с бека, например, при получении товаров
            */
        obj: Object,
        alt: {
            type: String,
            default: ''
        }
    },
    computed: {
        ...mapState(useIndexStore, ['supportsWebp']),
        noWebpSrc() {
            if (this.path)
                return this.path

            return getImagePath(this.obj)
        },
        webpSrc() {
            if (this.webpPath)
                return this.webpPath

            return getImagePath(this.obj, 'webp')
        }
    },
}
</script>

<style></style>