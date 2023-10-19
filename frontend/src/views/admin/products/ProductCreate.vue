<template>
    <div class="admin-page__creation">
        <Transition name="grow">
            <div v-if="errorMessage" class="admin-page__error">
                {{ errorMessage }}
            </div>
        </Transition>
        <div class="inputs-grid">
            <TextInput inputName="name" id="name" placeholder="Название товара">
                <template v-slot:label>Название товара</template>
                <template v-if="errors.name">
                    {{ errors.name }}
                </template>
            </TextInput>
        </div>
        <div class="inputs-grid">
            <TextInput inputName="price" id="price" width="160px" placeholder="Название товара">
                <template v-slot:label>Название товара</template>
                <template v-if="errors.name">
                    {{ errors.name }}
                </template>
            </TextInput>
        </div>
    </div>
</template>

<script>
import TextInput from '@/components/inputs/TextInput.vue';

export default {
    name: 'ProductCreate',
    components: {
        TextInput
    },
    data(){
        return {
            errors: {},
            errorMessage: ''
        }
    },
}
</script>

<style></style>