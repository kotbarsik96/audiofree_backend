<template>
    <component :is="tag" class="spoiler" :class="{ '__shown': isBodyShown }">
        <button class="spoiler__button" type="button" @click="toggle">
            <slot name="buttonContent"></slot>
        </button>
        <Transition :css="false" @before-enter="onBeforeEnter" @enter="onEnter" @leave="onLeave">
            <component v-show="isBodyShown" :is="spoilerBodyTag" class="spoiler__body" :class="spoilerBodyClass"
                ref="spoilerBody">
                <slot></slot>
            </component>
        </Transition>
    </component>
</template>

<script>
import { gsap } from 'gsap'
import { getHeight } from '@/assets/js/scripts.js'

function tagValidator(value) {
    const availableTags = [
        'div',
        'ul',
        'li'
    ]
    return availableTags.includes(value)
}
export default {
    name: 'SpoilerElem',
    emits: ['hide', 'show'],
    props: {
        isShownDefault: Boolean,
        tag: {
            type: String,
            default: 'div',
            validator: tagValidator
        },
        spoilerBodyTag: {
            type: String,
            default: 'div',
            validator: tagValidator
        },
        spoilerBodyClass: String
    },
    data() {
        return {
            isBodyShown: false
        }
    },
    mounted() {
        this.isShownDefault
            ? this.show()
            : this.hide()
    },
    methods: {
        toggle() {
            this.isBodyShown
                ? this.hide()
                : this.show()
        },
        async show() {
            this.$emit('show')
            this.isBodyShown = true
        },
        async hide() {
            this.$emit('hide')
            this.isBodyShown = false
        },
        onBeforeEnter(el) {
            gsap.set(el, {
                maxHeight: '0px',
                padding: '0px',
                margin: '0px'
            })
        },
        onEnter(el, done) {
            const maxHeight = `${getHeight(el)}px`
            gsap.set(el, { overflow: 'hidden' })
            gsap.to(el, {
                maxHeight,
                duration: .3,
                onComplete: () => {
                    el.style.removeProperty('overflow')
                    done()
                }
            })
        },
        onLeave(el, done) {
            gsap.set(el, { overflow: 'hidden' })
            gsap.to(el, {
                maxHeight: '0px',
                padding: '0px',
                margin: '0px',
                duration: .3,
                onComplete: () => {
                    el.style.removeProperty('overflow')
                    done()
                }
            })
        },
    }
}
</script>

<style></style>