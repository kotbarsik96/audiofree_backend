<template>
    <div class="admin-page__control">
        <LoadingScreen v-if="isLoading"></LoadingScreen>
        <div class="admin-page__control-searching inputs-flex">
            <TextInputWrapper name="name" id="name" v-model="search.name">
                <template v-slot:label>
                    Название товара
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="price" id="price" numberonly modifiers="toLocaleString" v-model="search.price">
                <template v-slot:label>
                    Текущая цена
                </template>
            </TextInputWrapper>
            <ValueSelect name="has_discount" :values="['Да', 'Нет', 'Неважно']" v-model="search.has_discount">
                <template v-slot:label>
                    Есть скидка
                </template>
            </ValueSelect>
            <TextInputWrapper name="brand" id="brand" v-model="search.brand">
                <template v-slot:label>
                    Бренд
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="category" id="category" v-model="search.category">
                <template v-slot:label>
                    Категория
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="type" id="type" v-model="search.type">
                <template v-slot:label>
                    Тип
                </template>
            </TextInputWrapper>
        </div>
        <div class="admin-page__listing">
            <div class="admin-list-table">
                <div class="admin-list-table__heading">
                    <ListIcon></ListIcon>
                    <span>
                        Список товаров (всего: {{ productsCount }})
                    </span>
                    <Transition name="grow">
                        <span v-if="error" class="error">
                            {{ error }}
                        </span>
                    </Transition>
                </div>
                <div class="admin-list-table__container">
                    <table class="admin-list-table__table">
                        <tr>
                            <th></th>
                            <th>
                                Изображение
                            </th>
                            <th>
                                Наименование
                            </th>
                            <th>
                                Цена
                            </th>
                            <th>
                                Количество
                            </th>
                            <th>
                                Статус
                            </th>
                            <th>
                                Действие
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkbox">
                                    <input type="checkbox" name="product-control-selection" @change="selectAllItems">
                                    <div class="checkbox__box"></div>
                                </label>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <button class="admin-list-table__control-button admin-list-table__control-button--delete"
                                    :disabled="selectedItems.length < 1" type="button">
                                    <TrashCanCircleIcon></TrashCanCircleIcon>
                                </button>
                            </td>
                        </tr>
                        <tr v-for="product in products" :key="product.id" :data-id="product.id">
                            <td>
                                <label class="checkbox">
                                    <input ref="itemCheckbox" type="checkbox" name="product-control-selection"
                                        :value="product.id" v-model="selectedItems">
                                    <div class="checkbox__box"></div>
                                </label>
                            </td>
                            <td>
                                <img :src="getImageSrc(product.image_path)" :alt="product.image_path">
                            </td>
                            <td>
                                {{ product.name }}
                            </td>
                            <td class="prices">
                                <span class="price-current">
                                    {{ product.current_price }}₽
                                </span>
                                <span v-if="product.discount_price" class="price-old">
                                    {{ product.price }}₽
                                </span>
                            </td>
                            <td>
                                {{ product.amount || 1 }}
                            </td>
                            <td>
                                {{ translateStatus(product.status) }}
                            </td>
                            <td>
                                <button class="admin-list-table__control-button admin-list-table__control-button--edit"
                                    type="button">
                                    <PencilIcon></PencilIcon>
                                </button>
                                <button class="admin-list-table__control-button admin-list-table__control-button--delete"
                                    type="button">
                                    <TrashCanCircleIcon></TrashCanCircleIcon>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="admin-list-table__pagination">
                    <ListPagination v-model="products" v-model:error="error" v-model:isLoading="isLoading" v-model:count="productsCount" :loadLink="loadLink" :countLink="countLink" :pagesLimit="8" :limit="10" forAdminPage></ListPagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import ValueSelect from '@/components/inputs/ValueSelect.vue'
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import ListPagination from '@/components/pagination/ListPagination.vue'

export default {
    name: 'ProductsControl',
    components: {
        TextInputWrapper,
        ValueSelect,
        LoadingScreen,
        ListPagination
    },
    data() {
        return {
            products: [],
            error: '',
            productsCount: 0,
            selectedItems: [],
            isLoading: false,
            search: {
                name: '',
                price: '',
                has_discount: '',
                brand: '',
                category: '',
                type: '',
            }
        }
    },
    computed: {
        loadLink() {
            return import.meta.env.VITE_PRODUCTS_GET_LINK
        },
        countLink() {
            return import.meta.env.VITE_PRODUCTS_COUNT_LINK
        },
    },
    methods: {
        selectAllItems(event) {
            this.$refs.itemCheckbox.forEach(cb => {
                cb.checked = event.target.checked
                setTimeout(() => cb.dispatchEvent(new Event('change')), 0);
            })
        },
        translateStatus(statusString) {
            switch (statusString) {
                case 'active':
                default:
                    return 'Активен'
                case 'disabled':
                    return 'Неактивен'
            }
        },
        getImageSrc(imagePath) {
            return `${import.meta.env.VITE_LINK}${imagePath}`
        },
    },
}
</script>