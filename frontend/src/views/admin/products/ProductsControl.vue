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
                        Список товаров (всего: {{ totalCount }})
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
                        <tr v-for="product in shownProducts" :key="product.id" :data-id="product.id">
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
                    <!-- <ListPagintaion v-model="" :link="loadLink" :countLink="countLink"></ListPagintaion> -->
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
import axios from 'axios'
import { isNumeric } from '@/assets/js/scripts.js'

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
            pagesLimit: 8,
            products: {},
            totalCount: 0,
            limit: 10,
            error: '',
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
        isAllLoaded() {
            return this.products.length >= this.totalCount
        },
        pagesCount() {
            return Math.floor(this.totalCount / this.limit)
        },
        currentPageNumber() {
            return parseInt(this.$route.params.pageNumber) || 1
        },
        visiblePages() {
            const array = []
            const half = Math.floor(this.pagesLimit / 2)
            if (this.currentPageNumber < half) {
                for (let num = 1; num <= this.pagesLimit && num <= this.pagesCount; num++) {
                    array.push(num)
                }
            } else {
                let num = this.currentPageNumber - half
                const until = num + this.pagesLimit
                for (num; num <= until; num++) {
                    if (num <= this.pagesCount)
                        array.push(num)
                }
            }

            return array
        },
        offset() {
            return this.currentPageNumber * this.limit
        },
        shownProducts() {
            return this.products[this.offset]
                || []
        }
    },
    methods: {
        async loadCount() {
            try {
                const res = await axios.get(import.meta.env.VITE_PRODUCTS_COUNT_LINK)
                const count = parseInt(res.data.count)
                if (!isNaN(count)) {
                    this.totalCount = count
                }
            } catch (err) {
                const data = err.response.data
                if (data.error)
                    this.error = data.error
            }
        },
        async loadProducts() {
            // важно: использовать переменную offset, вместо использования this.offset, иначе после await offset может смениться и запишется this.products[this.offset] уже не туда
            const offset = this.offset
            if (Array.isArray(this.products[offset]))
                return

            this.isLoading = true

            try {
                const res = await axios.get(import.meta.env.VITE_PRODUCTS_GET_LINK, {
                    params: {
                        forAdminPage: true,
                        limit: this.limit,
                        offset
                    }
                })
                if (Array.isArray(res.data)) {
                    this.products[offset] = res.data
                } else {
                    this.error = 'Произошла ошибка'
                }
            } catch (err) {
                const data = err.response.data
                if (data.error)
                    this.error = data.error
            }

            this.isLoading = false
        },
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
        setPage(value) {
            const name = this.$route.name

            if (isNumeric(value)) {
                value = parseInt(value)
                if (value < 1)
                    value = 1
                if (value > this.pagesCount)
                    value = this.pagesCount
                this.$router.push({ name, params: { pageNumber: value } })
            }
            else {
                let newNumber = this.currentPageNumber
                switch (value) {
                    case 'prev':
                        newNumber = newNumber - 1
                        if (newNumber <= 0)
                            newNumber = 1
                        break
                    case 'next':
                        newNumber = newNumber + 1
                        if (newNumber > this.pagesCount)
                            newNumber = this.pagesCount
                        break
                }
                this.$router.push({ name, params: { pageNumber: newNumber } })
            }
        },
        clearProductsArray() {
            const keys = Object.keys(this.products)
            if (keys.length > 10) {
                const diff = keys.length - 9
                for (let i = 0; i < diff; i++) {
                    const key = keys[i]
                    delete this.products[key]
                }
            }
        },
        showPaginationEllipsis() {
            return this.pagesCount > this.pagesLimit
                && this.visiblePages[this.visiblePages.length - 1] !== this.pagesCount
        }
    },
    watch: {
        products: {
            deep: true,
            handler() {
                this.clearProductsArray()
            }
        },
        currentPageNumber() {
            this.loadProducts()
        }
    },
    mounted() {
        this.loadCount()
        this.loadProducts()
    }
}
</script>