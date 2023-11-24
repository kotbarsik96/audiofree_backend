<template>
    <picture :class="{ '__empty': !wasIntersected }">
        <source :srcset="supportsWebp ? webpSrc : noWebpSrc" :media="media">
        <source v-for="obj in mediaSourcesComputed" :srcset="getSrcset(obj)" :media="obj.media">
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
        /* будет добавляться после названия, перед расширением */
        suffix: String,
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
                    if (!checkMediaRegexp(obj.media))
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
        },
        /* позволяет задать дополнительные требования для ленивой загрузки. Только при удовлетворении всех требований и this.wasIntersected = true будут выставлены src и srcset */
        lazyLoadConditions: Object
    },
    computed: {
        ...mapState(useIndexStore, ['supportsWebp']),
        isVisible() {
            if (this.lazyLoadConditions && typeof this.lazyLoadConditions === 'object') {
                for (let bool of Object.values(this.lazyLoadConditions)) {
                    if (!bool)
                        return false
                }
            }
            return this.wasIntersected
        },
        noWebpSrc() {
            if (!this.isVisible)
                return '#'

            if (this.path)
                return getImagePath(this.path, { suffix: this.suffix })

            return getImagePath(this.obj, { suffix: this.suffix })
        },
        webpSrc() {
            if (!this.isVisible)
                return '#'

            if (this.webpPath)
                return getImagePath(this.webpPath, { extension: 'webp', suffix: this.suffix })

            return getImagePath(this.obj, { extension: 'webp', suffix: this.suffix })
        },
        mediaSourcesComputed() {
            if (!this.wasIntersected)
                return []

            return this.mediaSources
        },
    },
    data() {
        return {
            wasIntersected: false,
            observer: null,
        }
    },
    methods: {
        getSrcset(obj) {
            if (this.supportsWebp) {
                if (obj.webpPath)
                    return getImagePath(obj.webPath, { extension: 'webp', suffix: this.suffix })

                return getImagePath(obj.obj, { extension: 'webp', suffix: this.suffix })
            } else {
                if (obj.path)
                    return getImagePath(obj.webPath, { suffix: this.suffx })

                return getImagePath(obj.obj, { suffix: this.suffx })
            }
        },
        onIntersect(entries) {
            entries.forEach(entry => {
                if (!entry.isIntersecting)
                    return

                this.wasIntersected = true
                this.observer.unobserve(this.$el)
                this.observer = null
            })
        }
    },
    mounted() {
        this.observer = new IntersectionObserver(this.onIntersect, {
            rootMargin: "250px 100px 450px 100px"
        })
        this.observer.observe(this.$el)
    }
}
</script>