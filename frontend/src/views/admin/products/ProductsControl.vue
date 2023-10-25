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
                                    <input type="checkbox" name="product-control-selection" ref="allItemsCheckboxTop"
                                        @change="selectAllItems">
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
                                    :disabled="selectedItems.length < 1" type="button" @click="deleteAllSelected">
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
                                <RouterLink class="admin-list-table__control-button admin-list-table__control-button--edit"
                                    :to="{ name: 'ProductUpdate', params: { productId: product.id } }" type="button">
                                    <PencilIcon></PencilIcon>
                                </RouterLink>
                                <button class="admin-list-table__control-button admin-list-table__control-button--delete"
                                    type="button" @click="deleteProduct(product.id)">
                                    <TrashCanCircleIcon></TrashCanCircleIcon>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkbox">
                                    <input type="checkbox" name="product-control-selection" ref="allItemsCheckboxBottom"
                                        @change="selectAllItems">
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
                                    :disabled="selectedItems.length < 1" type="button" @click="deleteAllSelected">
                                    <TrashCanCircleIcon></TrashCanCircleIcon>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="admin-list-table__pagination">
                    <ListPagination ref="paginationComponent" v-model="products" v-model:error="error"
                        v-model:isLoading="isLoading" v-model:count="productsCount" :loadLink="loadLink"
                        :countLink="countLink" :pagesLimit="8" :limit="10" forAdminPage></ListPagination>
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
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import { useModalsStore } from '@/stores/modals.js'
import { useNotificationsStore } from '@/stores/notifications.js'
import { h } from 'vue'
import axios from 'axios'

export default {
    name: 'ProductsControl',
    components: {
        TextInputWrapper,
        ValueSelect,
        LoadingScreen,
        ListPagination,
        ConfirmModal
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
        updateProducts() {
            this.$refs.paginationComponent.load()
        },
        selectAllItems(event) {
            this.selectedItems = []
            this.$refs.itemCheckbox.forEach(cb => {
                cb.checked = event.target.checked
                if (event.target.checked)
                    this.selectedItems.push(cb.value)
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
        deleteProduct(id) {
            const callback = async () => {
                this.isLoading = true

                try {
                    const link = `${import.meta.env.VITE_PRODUCT_DELETE_LINK}${id}`
                    const res = await axios.delete(link)
                    if (res.data.success) {
                        this.updateProducts()
                    }
                    if (res.data.message) {
                        useNotificationsStore().addNotification({
                            timeout: 5000,
                            message: res.data.message
                        })
                    }
                } catch (err) {
                    const data = err.response.data
                    if (data.error)
                        this.error = data.error
                }

                this.isLoading = false
            }
            const product = this.products.find(obj => obj.id === id)
            if (!product)
                return

            const modalComponent = h(ConfirmModal, {
                title: `Удалить товар "${product.name}"?`,
                confirmProps: {
                    text: 'Удалить',
                    callback
                },
                declineProps: {
                    text: 'Не удалять'
                }
            })
            useModalsStore().addModal({ component: modalComponent })
        },
        deleteAllSelected() {
            const callback = async () => {
                this.isLoading = true

                try {
                    const link = import.meta.env.VITE_PRODUCT_DELETE_LINK
                    const res = await axios.delete(link, {
                        data: {
                            idsList: this.selectedItems
                        }
                    })

                    this.updateProducts()

                    if (res.data.message) {
                        useNotificationsStore().addNotification({
                            timeout: 5000,
                            message: res.data.message
                        })
                    }
                } catch (err) {
                    const data = err.response.data
                    if (data.error)
                        this.error = data.error
                }

                this.isLoading = false
            }

            const modalComponent = h(ConfirmModal, {
                title: `Удалить отмеченные товары (${this.selectedItems.length} шт.)?`,
                confirmProps: {
                    text: 'Удалить',
                    callback
                },
                declineProps: {
                    text: 'Не удалять'
                }
            })
            useModalsStore().addModal({ component: modalComponent })
        }
    },
    watch: {
        selectedItems() {
            const refCheckboxes = [
                this.$refs.allItemsCheckboxTop,
                this.$refs.allItemsCheckboxBottom
            ]
            if (this.selectedItems.length === this.products.length) {
                refCheckboxes.forEach(cb => cb.checked = true)
            }
            else {
                refCheckboxes.forEach(cb => cb.checked = false)
            }
        },
        products() {
            this.selectedItems = []
        }
    }
}
</script>