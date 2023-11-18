<template>
    <picture>
        <source :srcset="supportsWebp ? webpSrc : noWebpSrc" :media="media">
        <source v-for="obj in mediaSources" :srcset="getSrcset(obj)" :media="obj.media">
        <img :src="noWebpSrc" :alt="alt">
    </picture>
</template>

<script>
import { mapState } from 'pinia'
import { useIndexStore } from '@/stores/'
import { getImagePath } from '@/assets/js/methods.js'

function checkMediaRegexp(media) {
    if (!media.match(/\(min-width: \d+px\)|\(max-width: \d+px\)/))
        return false
    return true
}

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
        },
        media: {
            type: String,
            validator(media) {
                return checkMediaRegexp(media)
            }
        },
        /* mediaSources[i]: {
            media: '(max-width: 799px)',
            webpPath: String,
            path: String,
            obj: Object,
            т.е. то же самое, что пропсы для этого компонента
        } */
        mediaSources: {
            type: Array,
            validator(array) {
                for (let obj of array) {
                    if (typeof obj.media !== 'string')
                        return false
                    if(!checkMediaRegexp(obj.media))
                        return false

                    if (obj.webpPath && typeof obj.webpPath !== 'string')
                        return false
                    if (obj.path && typeof obj.path !== 'string')
                        return false

                    if (obj.obj && typeof obj.obj !== 'object')
                        return false
                }

                return true
            }
        }
    },
    computed: {
        ...mapState(useIndexStore, ['supportsWebp']),
        noWebpSrc() {
            if (this.path)
                return getImagePath(this.path)

            return getImagePath(this.obj)
        },
        webpSrc() {
            if (this.webpPath)
                return getImagePath(this.webpPath, 'webp')

            return getImagePath(this.obj, 'webp')
        }
    },
    methods: {
        getSrcset(obj) {
            if (this.supportsWebp) {
                if (obj.webpPath)
                    return getImagePath(obj.webPath, 'webp')

                return getImagePath(obj.obj, 'webp')
            } else {
                if (obj.path)
                    return getImagePath(obj.webPath)

                return getImagePath(obj.obj)
            }
        }
    }
}
</script>