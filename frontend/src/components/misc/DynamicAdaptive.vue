<template>
    <component :is="tagName" ref="el">
        <slot></slot>
    </component>
</template>

<script>
import { findClosest } from '@/assets/js/scripts.js'

export default {
    name: 'DynamicAdaptive',
    props: {
        query: {
            type: String,
            required: true,
            validator(value) {
                return value.match(/(max-width\: \d+px)|(min-width\: \d+px)/)
            }
        },
        destinationSelector: {
            type: String,
            required: true,
            validator(value) {
                return value.match(/(^\.)|(^\#)|(\[.+\])/)
            }
        },
        maxParentsCount: Number,
        tagName: {
            type: String,
            default: 'div'
        },
        className: [String, Object, Array]
    },
    data() {
        return {
            matchesQuery: false,
            origParentNode: null,
            anchor: document.createElement('div'),
            usedDestination: null
        }
    },
    computed: {
        mediaQuery() {
            let queryValue = this.query
            if (!this.query.match(/\(.+\)/))
                queryValue = `(${queryValue})`

            return window.matchMedia(queryValue)
        }
    },
    mounted() {
        this.anchor.style.display = 'none'
        this.origParentNode = this.$refs.el.parentNode
        this.mediaQuery.addEventListener('change', this.onMediaChange)
        this.onMediaChange()
    },
    methods: {
        onMediaChange() {
            if (this.mediaQuery.matches) {
                this.matchesQuery = true
                this.moveToDestination()
            } else {
                this.matchesQuery = false
                this.returnToOrigParent()
            }
        },
        moveToDestination() {
            this.usedDestination = findClosest(this.$refs.el, this.destinationSelector, this.maxParentsCount)
            if (!this.$refs.el)
                return

            this.$refs.el.replaceWith(this.anchor)
            if (!this.usedDestination)
                return

            this.usedDestination.after(this.$refs.el)
            this.usedDestination.remove()
            this.usedDestination.classList
                .forEach(className => this.$refs.el.classList.add(className))
        },
        returnToOrigParent() {
            if (this.usedDestination) {
                if (this.$refs.el)
                    this.$refs.el.after(this.usedDestination)
                this.usedDestination.classList
                    .forEach(className => this.$refs.el.classList.remove(className))
            }

            this.anchor.replaceWith(this.$refs.el)
            this.usedDestination = null
        }
    },
}
</script>