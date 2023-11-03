<template>
    <div class="admin-page__control">
        <LoadingScreen v-if="isLoading"></LoadingScreen>
        <div class="admin-page__control-filtering inputs-flex">
            <TextInputWrapper name="name" id="name" v-model="filters.name">
                <template v-slot:label>
                    Название товара
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="price" id="price" numberonly modifiers="toLocaleString" v-model="filters.current_price">
                <template v-slot:label>
                    Текущая цена
                </template>
            </TextInputWrapper>
            <ValueSelect name="has_discount" :values="[
                { string: 'Да', value: 'yes' },
                { string: 'Нет', value: 'no' },
                { string: 'Неважно', value: 'no_matter' }
            ]" v-model="filters.has_discount">
                <template v-slot:label>
                    Есть скидка
                </template>
            </ValueSelect>
            <ValueSelect name="brand" :values="taxonomies.brands" v-model="filters.brands">
                <template v-slot:label>
                    Бренд
                </template>
            </ValueSelect>
            <ValueSelect name="category" :values="taxonomies.categories" v-model="filters.categories">
                <template v-slot:label>
                    Категория
                </template>
            </ValueSelect>
            <ValueSelect name="type" :values="taxonomies.types" v-model="filters.types">
                <template v-slot:label>
                    Тип
                </template>
            </ValueSelect>
            <ValueSelect name="product_status" :values="taxonomies.product_statuses" v-model="filters.product_statuses">
                <template v-slot:label>
                    Статус
                </template>
            </ValueSelect>
            <!-- <TextInputWrapper name="brand" id="brand" v-model="filters.brands">
                <template v-slot:label>
                    Бренд
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="category" id="category" v-model="filters.categories">
                <template v-slot:label>
                    Категория
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="type" id="type" v-model="filters.types">
                <template v-slot:label>
                    Тип
                </template>
            </TextInputWrapper> -->
        </div>
        <div class="admin-page__listing">
            <AdminListTable v-model="list" v-model:selectedItems="selectedItems" :columnsCount="7"
                @deleteSelected="deleteAllSelected">
                <template v-slot:containerHeading>
                    <span>
                        Список товаров (всего: {{ listCount }})
                    </span>
                    <Transition name="grow">
                        <span v-if="error" class="error">
                            {{ error }}
                        </span>
                    </Transition>
                </template>
                <template v-slot:thead>
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
                </template>
                <tr v-for="item in list" :key="item.id">
                    <td>
                        <CheckboxLabel name="product-control-selection" :checked="selectedItems.includes(item.id)"
                            :value="item.id" v-model="selectedItems"></CheckboxLabel>
                    </td>
                    <td>
                        <img :src="getImageSrc(item.image_path)" :alt="item.image_path">
                    </td>
                    <td>
                        {{ item.name }}
                        <br>
                        {{ item.id }}
                    </td>
                    <td>
                        <div class="prices">
                            <span class="price-current">
                                {{ item.current_price }}₽
                            </span>
                            <span v-if="item.discount_price" class="price-old">
                                {{ item.price }}₽
                            </span>
                        </div>
                    </td>
                    <td>
                        {{ item.quantity || 1 }}
                    </td>
                    <td>
                        {{ item.product_status }}
                    </td>
                    <td>
                        <RouterLink class="admin-list-table__control-button admin-list-table__control-button--edit"
                            :to="{ name: 'ProductUpdate', params: { productId: item.id } }" type="button">
                            <PencilIcon></PencilIcon>
                        </RouterLink>
                        <button class="admin-list-table__control-button admin-list-table__control-button--delete"
                            type="button" @click="deleteProduct(item.id)">
                            <TrashCanCircleIcon></TrashCanCircleIcon>
                        </button>
                    </td>
                </tr>
            </AdminListTable>
            <ListPagination ref="paginationComponent" v-model="list" v-model:error="error" v-model:isLoading="isLoading"
                v-model:count="listCount" :loadLink="loadLink" :pagesLimit="8" :limit="10" :filters="filters" allData>
            </ListPagination>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import ValueSelect from '@/components/inputs/ValueSelect.vue'
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import ListPagination from '@/components/pagination/ListPagination.vue'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import AdminListTable from '@/components/tables/AdminListTable.vue'
import { useIndexStore } from '@/stores/'
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
        ConfirmModal,
        AdminListTable
    },
    data() {
        return {
            list: [],
            error: '',
            listCount: 0,
            selectedItems: [],
            isLoading: false,
            taxonomies: {
                brands: [],
                categories: [],
                types: [],
                product_statuses: []
            },
            filters: {
                name: '',
                current_price: '',
                has_discount: null,
                brands: '',
                categories: '',
                types: '',
            }
        }
    },
    computed: {
        loadLink() {
            return import.meta.env.VITE_PRODUCTS_GET_LINK
        }
    },
    methods: {
        async loadTaxonomies() {
            await useIndexStore().loadTaxonomies(this.taxonomies, true)
        },
        updateProducts() {
            this.$refs.paginationComponent.loadList(true)
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
            const item = this.list.find(obj => obj.id === id)
            if (!item)
                return

            const modalComponent = h(ConfirmModal, {
                title: `Удалить товар "${item.name}"?`,
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
        },
    },
    mounted() {
        this.loadTaxonomies()
    }
}
</script>