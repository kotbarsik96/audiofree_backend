<template>
    <div class="modal register-modal">
        <div class="modal__close" @click="$emit('close')">
            <div class="close-button"></div>
        </div>
        <h3 v-if="title" class="modal__title">
            {{ title }}
        </h3>
        <div class="modal__body">
            
        </div>
    </div>
</template>

<script>
export default {
    name: 'RegisterModal',
    emits: [
        'close'
    ],
    props: {
        title: {
            type: String,
            default: 'Регистрация'
        }
    }
}
</script>

<style></style>