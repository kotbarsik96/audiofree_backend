<template>
    <div class="auth-modal__greet">
        {{ message }}
    </div>
</template>

<script>
export default {
    name: 'AuthGreetModalBody',
    props: {
        message: String
    }
}
</script>

<style>
    .auth-modal__greet {
        font-size: 24px;
        font-weight: 500;
        color: var(--theme_color);
        text-align: center;
        margin-bottom: 20px;
        padding: 0 15px;
    }
</style>