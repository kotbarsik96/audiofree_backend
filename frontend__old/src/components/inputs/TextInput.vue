<template>
    <div class="text-input">
        <div class="text-input__wrapper">
            <input class="text-input__input" v-model="value" :placeholder="placeholder" :type="type" :name="inputName"
                :id="id">
        </div>
    </div>
</template>

<script>
export default {
    name: 'TextInput',
    emits: ['update:modelValue'],
    props: {
        modelValue: String,
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
    }
}
</script>

<style></style>