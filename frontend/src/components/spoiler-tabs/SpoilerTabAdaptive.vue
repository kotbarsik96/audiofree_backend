<template>
    <component :is="currentComponent" :content="content" isAccordeon></component>
</template>

<script>
import SpoilerTiles from './SpoilerTiles.vue'
import TabTiles from './TabTiles.vue'
import { setMatchMedia } from '@/assets/js/methods.js'

export default {
    name: 'SpoilerTiles',
    components: {
        SpoilerTiles,
        TabTiles
    },
    props: {
        spoilerMaxMedia: {
            type: String,
            default: '919'
        },
        isAccordeon: Boolean,
        content: Array
    },
    data() {
        return {
            matchMediaMatches: {
                max: {}
            }
        }
    },
    computed: {
        currentComponent() {
            if (this.matchMediaMatches.max[this.spoilerMaxMedia])
                return SpoilerTiles
            else
                return TabTiles
        }
    },
    methods: {
        setMatchMedia
    },
    mounted() {
        this.matchMediaMatches.max[this.spoilerMaxMedia] = false
        this.setMatchMedia()
    }
}
</script>

<style></style>