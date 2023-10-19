<template>
    <TextInput v-model="value" :placeholder="placeholder" :type="type" :inputName="inputName" :id="id"
        containerClass="search-input" wrapperClass="search-input__wrapper" inputClass="search-input__input" ref="rootComp">
        <template v-slot:icon>
            <SearchIcon @click="focus"></SearchIcon>
        </template>
    </TextInput>
</template>

<script>
import TextInput from '@/components/inputs/TextInput.vue'
import SearchIcon from '@/components/icons/SearchIcon.vue'

export default {
    name: 'HeaderSearchInput',
    components: {
        TextInput,
        SearchIcon
    },
    emits: ['update:modelValue'],
    props: {
        placeholder: String,
        type: {
            type: String,
            default: 'text'
        },
        inputName: {
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