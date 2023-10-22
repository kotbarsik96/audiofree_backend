<template>
    <div class="admin-page__creation">
        <Transition name="grow">
            <div v-if="errorMessage" class="admin-page__error">
                {{ errorMessage }}
            </div>
        </Transition>
        <div class="inputs-flex">
            <TextInputWrapper name="name" id="name" placeholder="Название товара">
                <template v-slot:label>Название товара</template>
                <template v-if="errors.name">
                    {{ errors.name }}
                </template>
            </TextInputWrapper>
        </div>
        <div class="inputs-flex">
            <TextInputWrapper name="price" id="price" width="160px" placeholder="Цена" numberonly modifiers="toLocaleString"
                max="1000000">
                <template v-slot:label>Цена</template>
                <template v-if="errors.price">
                    {{ errors.price }}
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="discount_price" id="discount_price" width="160px" placeholder="Цена по скидке"
                numberonly modifiers="toLocaleString" max="1000000">
                <template v-slot:label>Цена по скидке</template>
                <template v-if="errors.discount_price">
                    {{ errors.discount_price }}
                </template>
            </TextInputWrapper>
        </div>
        <div class="inputs-flex">
            <ValueSelect v-model="input.taxonomies.brand" name="brand" :values="[
                'Xiaomi',
                'Honor',
                'Apple',
                'Samsung'
            ]">
                <template v-slot:label>
                    Бренд
                </template>
            </ValueSelect>
            <ValueSelect v-model="input.taxonomies.category" name="category" :values="[
                'Проводные',
                'Беспроводные',
            ]">
                <template v-slot:label>
                    Категория
                </template>
            </ValueSelect>
            <ValueSelect v-model="input.taxonomies.type" name="type" :values="[
                'Наушники'
            ]">
                <template v-slot:label>
                    Тип
                </template>
            </ValueSelect>
        </div>
        <div class="admin-page__creation-table">
            <AdminTable :headers="['Характеристика', 'Значение']" v-model="input.info"></AdminTable>
        </div>
        <div class="admin-page__creation-table">
            <AdminTable multivalues :headers="['Вариация', 'Значения']" v-model="input.variations"></AdminTable>
        </div>
        <div class="admin-page__creation-image">
            <ImageLoad v-model="mainImage.path" v-model:id="mainImage.id">
                <template v-slot:title>
                    Главное изображение
                </template>
            </ImageLoad>
        </div>
        <div class="admin-page__creation-image admin-page__creation-image--gallery">
            <ImagesGallery v-model="gallery"></ImagesGallery>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import ValueSelect from '@/components/inputs/ValueSelect.vue'
import AdminTable from '@/components/tables/AdminTable.vue'
import ImageLoad from '@/components/inputs/images/ImageLoad.vue'
import ImagesGallery from '@/components/inputs/images/ImagesGallery.vue'

export default {
    name: 'ProductCreate',
    components: {
        TextInputWrapper,
        ValueSelect,
        AdminTable,
        ImageLoad,
        ImagesGallery
    },
    data() {
        return {
            errors: {},
            errorMessage: '',
            input: {
                taxonomies: {
                    brand: '',
                    category: '',
                    type: ''
                },
                info: [
                    { name: 'Тест', values: ['Тест'] },
                ],
                variations: []
            },
            mainImage: {
                path: '',
                id: 0
            },
            gallery: []
        }
    },
}
</script>

<style></style>