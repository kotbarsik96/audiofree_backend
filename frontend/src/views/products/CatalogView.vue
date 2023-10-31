<template>
    <div>
        <div class="container">
            <div class="catalog">
                <div class="catalog__wrapper">
                    <LoadingScreen v-if="isLoading"></LoadingScreen>
                    <div class="catalog__filter-container">
                        <ProductsFilter ref="productsFilter" v-model="filters" :sections="filterSections"></ProductsFilter>
                    </div>
                    <div class="catalog__page-heading page-heading">
                        <div class="breadcrumbs">
                            <RouterLink class="breadcrumbs__link link" :to="{ name: 'Home' }">
                                Главная
                            </RouterLink>
                            <RouterLink class="breadcrumbs__link link" :to="{ name: 'Catalog' }">
                                Каталог
                            </RouterLink>
                        </div>
                        <h1 class="page-title">
                            Каталог
                        </h1>
                    </div>
                    <div class="catalog__select">
                        <ValueSelect v-model="sortValue" name="products-catalog-sort" :values="sortValues"></ValueSelect>
                    </div>
                    <div class="catalog__list-container">
                        <ul class="catalog__list">
                            <li v-for="product in list" :key="product.id" class="catalog__list-item">
                                <ProductCard :product="product"></ProductCard>
                            </li>
                        </ul>
                        <div class="catalog__pagination">
                            <ListPagination v-model="list" v-model:error="error" v-model:count="totalCount"
                                :loadLink="productsLoadLink" :filters="filtersAndSort" :limit="productsOnPageLimit"
                                v-model:isLoading="isLoading">
                            </ListPagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <QuestionsAnswersSection></QuestionsAnswersSection>
    </div>
</template>

<script>
import ProductsFilter from '@/components/pagination/ProductsFilter.vue'
import ValueSelect from '@/components/inputs/ValueSelect.vue'
import ListPagination from '@/components/pagination/ListPagination.vue'
import ProductCard from '@/components/cards/products/ProductCard.vue'
import ProductCardDiscount from '@/components/cards/products/ProductCardDiscount.vue'
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import QuestionsAnswersSection from '@/components/page/sections/QuestionsAnswersSection.vue'
import { setMatchMedia } from '@/assets/js/methods.js'

export default {
    name: 'CatalogView',
    components: {
        ProductsFilter,
        ValueSelect,
        ListPagination,
        ProductCard,
        LoadingScreen,
        ProductCardDiscount,
        QuestionsAnswersSection
    },
    data() {
        return {
            sortValues: [
                { string: 'По алфавиту (а-я)', value: 'name|asc' },
                { string: 'По алфавиту (я-а)', value: 'name|desc' },
                { string: 'По цене (сначала дешевле)', value: 'price|asc' },
                { string: 'По цене (сначала дороже)', value: 'price|desc' },
            ],
            sortValue: 'name|asc',
            filters: {
                brand: [],
                category: [],
                type: [],
                has_discount: ''
            },
            filterSections: [
                {
                    title: 'Бренд',
                    name: 'brand',
                    type: 'checkbox',
                    values: ['Xiaomi', 'Apple']
                },
                {
                    title: 'Категория',
                    name: 'category',
                    type: 'checkbox',
                    values: ['Наушники']
                },
                {
                    title: 'Скидка',
                    name: 'has_discount',
                    type: 'radio',
                    values: [
                        { string: 'Есть', value: 'yes' },
                        { string: 'Нет', value: 'no' }
                    ]
                },
            ],
            matchMediaMatches: {
                max: {
                    '1189': false
                }
            },
            sort: '',
            totalCount: 0,
            isLoading: false,
            list: [],
            error: ''
        }
    },
    computed: {
        productsLoadLink() {
            return import.meta.env.VITE_PRODUCTS_GET_LINK
        },
        filtersAndSort() {
            return Object.assign(this.filters, { sortValue: this.sortValue })
        },
        productsOnPageLimit() {
            if (this.matchMediaMatches.max['1189'])
                return 8

            return 9
        }
    },
    methods: {
        setMatchMedia
    },
    mounted() {
        this.setMatchMedia()
    }
}
</script>

<style lang="scss">
.catalog {
    padding: 60px 0;

    &__wrapper {
        display: grid;
        grid-template-columns: repeat(4, 270px);
        grid-template-rows: 60px repeat(auto-fit, minmax(0, 1fr));
        grid-gap: 30px;
        position: relative;

        >.loading-screen {
            align-items: flex-start;

            .loading-screen__body {
                position: sticky;
                top: 40vh;
            }
        }
    }

    &__filter-container {
        grid-column: 1 / 2;
        position: relative;
        grid-row: 1 / -1;
    }

    &__page-heading {
        grid-column: 2 / 4;
    }

    &__list-container {
        grid-column: 2 / 5;
    }

    &__select {
        grid-column: 4 / 5;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: flex-end;
    }

    &__list {
        display: grid;
        grid-template-columns: repeat(auto-fit, 270px);
        grid-gap: 30px;
    }

    &__pagination {
        grid-column: 1 / -1;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 45px;
    }

    @media (max-width: 1249px) {
        grid-template-columns: repeat(auto-fit, 270px);

        &__page-heading {
            grid-column: 2;
        }

        &__select {
            grid-column: 3 / -1;
        }
    }

    @media (max-width: 1199px) {
        &__wrapper {
            grid-template-columns: repeat(3, 270px);
        }
    }

    @media (max-width: 919px) {
        &__wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            grid-template-columns: none;
            grid-template-rows: none;
        }

        &__page-heading {
            flex: 0 0 100%;
            order: 1;
        }

        &__select {
            order: 2;
            flex: 0 0 100%;
            align-items: center;
        }

        &__filter-container {
            flex: 0 0 500px;
            order: 3;
        }

        &__list-container {
            order: 4;
            flex: 0 0 100%;
        }

        &__pagination {
            order: 5;
        }

        &__list {
            justify-content: center;
        }
    }

    @media (max-width: 599px) {
        &__list {
            display: grid;
            grid-template-columns: repeat(auto-fit, 176px);
            grid-gap: 15px;
        }
    }

    @media (max-width: 529px) {
        &__filter-container {
            flex: 0 0 100%;
        }
    }
}
</style>