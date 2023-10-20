<template>
    <TextInputWrapper class="text-input--round" v-model="value" :placeholder="placeholder" :type="type" :name="name" :id="id"
        containerClass="search-input" wrapperClass="search-input__wrapper" inputClass="search-input__input" ref="rootComp">
        <template v-slot:icon>
            <SearchIcon @click="focus"></SearchIcon>
        </template>
    </TextInputWrapper>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import SearchIcon from '@/components/icons/SearchIcon.vue'

export default {
    name: 'HeaderSearchInput',
    components: {
        TextInputWrapper,
        SearchIcon
    },
    emits: ['update:modelValue'],
    props: {
        placeholder: String,
        type: {
            type: String,
            default: 'text'
        },
        name: {
            type: String,
            required: true
        },
        id: String
    },
    data() {
        return {
            value: ''
        }
    },
    methods: {
        focus(){
            this.$refs.rootComp.focus()
        }
    }
}
</script>