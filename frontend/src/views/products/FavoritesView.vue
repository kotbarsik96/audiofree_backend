<template>
    <div class="favorites-page">
        <div class="container">
            <div class="favorites-page__page-heading page-heading">
                <div class="breadcrumbs">
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Home' }">
                        Главная
                    </RouterLink>
                    <RouterLink class="breadcrumbs__link link" :to="{ name: 'Favorites' }">
                        Избранное
                    </RouterLink>
                </div>
                <h1 class="page-title">
                    Избранное
                </h1>
            </div>
            <div class="favorites-page__filtering">
                <div class="inputs-flex">
                    <TextInputWrapper name="product-search-name" id="product-search-name" placeholder="Название товара"
                        v-model="filters.name" width="300">
                        <template v-slot:label>
                            Название товара
                        </template>
                        <template v-slot:icon>
                            <SearchIcon></SearchIcon>
                        </template>
                    </TextInputWrapper>
                    <ValueSelect v-model="sortValue" name="product-search-sort" :values="sortValues">
                        <template v-slot:label>
                            Сортировка
                        </template>
                    </ValueSelect>
                </div>
            </div>
            <div class="favorites-page__list-container">
                <LoadingScreen v-if="isLoading"></LoadingScreen>
                <TransitionGroup tag="ul" move-class="favorites-page__item--move" class="favorites-page__list">
                    <li class="favorites-page__item" v-for="product in list" :key="product.id">
                        <ProductCard :productData="product"></ProductCard>
                    </li>
                </TransitionGroup>
                <div class="favorites-page__pagination">
                    <ListPagination v-model="list" v-model:count="totalCount" :loadLink="productsLoadLink"
                        :filters="filtersAndSorts" :limit="productsOnPageLimit" v-model:isLoading="isLoading">
                    </ListPagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import ValueSelect from '@/components/inputs/ValueSelect.vue'
import ListPagination from '@/components/pagination/ListPagination.vue'
import ProductCard from '@/components/cards/products/ProductCard.vue'
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import { mapState } from 'pinia'
import { useIndexStore } from '@/stores/'

export default {
    name: 'FavoritesView',
    components: {
        TextInputWrapper,
        ValueSelect,
        ListPagination,
        ProductCard,
        LoadingScreen
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
                name: '',
                is_favorite: true,
            },
            list: [],
            totalCount: 0,
            productsOnPageLimit: 9,
            isLoading: false
        }
    },
    computed: {
        ...mapState(useIndexStore, ['favorites']),
        productsLoadLink() {
            return import.meta.env.VITE_PRODUCTS_GET_LINK
        },
        filtersAndSorts() {
            return Object.assign({ sortValue: this.sortValue }, this.filters)
        },
    },
}
</script>

<style lang="scss">
.favorites-page {
    padding: 70px 0 50px 0;

    &__page-heading {
        margin-bottom: 20px;
    }

    &__filtering {
        margin-bottom: 30px;
        padding: 15px 10px 1px 10px;
        background-color: #f8f8f8;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, .1);
        border-radius: 5px;
    }

    &__list {
        display: grid;
        grid-template-columns: repeat(auto-fit, 270px);
        justify-content: center;
        grid-gap: 30px;
    }

    &__item {
        transform-origin: top center;

        .card,
        .card__container {
            height: 100%;
        }
    }

    &__item--move {
        transition: all .3s;
    }

    &__pagination {
        margin-top: 20px;
    }

    @media (max-width: 599px) {
        &__list {
            grid-template-columns: repeat(auto-fit, 176px);
        }
    }
}
</style>