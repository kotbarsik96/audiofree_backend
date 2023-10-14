<template>
    <main class="catalogue-page">
        <section class="catalogue-page__block catalogue-block">
            <div class="catalogue-block__container container">
                <div class="catalogue-block__title">
                    <div class="page-title">
                        <div class="page-title__links">
                            <RouterLink to="/" class="page-title__arrow-back __icon-back-arrow"></RouterLink>
                            <RouterLink to="/" class="page-title__link-back">Главная</RouterLink>
                            <RouterLink to="/catalogue" class="page-title__link-back">Каталог</RouterLink>
                        </div>
                        <div class="page-title__title">Каталог</div>
                    </div>
                    <div class="catalogue-block__selects">
                        <select-block inputName="sort-type" :options="[
                            {
                                title: 'С начала (от меньшего к большему)',
                                value: 'start',
                            },
                            {
                                title: 'С конца (от большего к меньшему)',
                                value: 'end',
                            },
                        ]" @selectValueChange="(selected) => getSortOrder(selected)
    " ref="sortOrderBlock"></select-block>
                        <select-block inputName="sort-catalogue" :objectToSort="filteredProducts" :options="[
                            { title: 'По названию', value: 'name' },
                            { title: 'По цене', value: 'price' },
                            { title: 'По отзывам', value: 'rating' },
                        ]" @sortValueChange="(sorted) => getSortedCatalogueList(sorted)
    " ref="sortKeysBlock"></select-block>
                    </div>
                </div>
                <div class="catalogue-block__aside">
                    <filter-block filterBlockTitle="Фильтр товаров" :fields="[
                        {
                            title: 'Бренд',
                            inputsType: 'checkbox',
                            key: 'brand',
                        },
                        {
                            title: 'Категория',
                            inputsType: 'checkbox',
                            key: 'category',
                        },
                        {
                            title: 'Цена',
                            inputsType: 'range',
                            key: 'price',
                            maxRangeValue: calcMaxRangeValue('price'),
                            rangeValueString: '₽',
                        },
                        {
                            title: 'По акции',
                            inputsType: 'checkbox',
                            key: 'sale.type',
                        },
                    ]" :objectToFilter="products" @filterApply="(filtered) => getFilteredCatalogueList(filtered)
    " ref="filterBlock"></filter-block>
                    <product-info-card :title="'Распродажа'"></product-info-card>
                </div>
                <router-view :catalogueListPage="catalogueListPage" />
                <div class="catalogue-block__page-counter page-counter">
                    <div class="page-counter__container">
                        <router-link class="page-counter__number" v-for="num in pagesAmount" :key="num" :to="{
                            name: 'catalogue-list',
                            params: { pageNumber: [num] },
                        }">{{ num }}</router-link>
                        <router-link :to="{
                            name: 'catalogue-list',
                            params: { pageNumber: [prevPageNumber] },
                        }" class="
                                page-counter__arrow page-counter__arrow--back
                                __icon-back-arrow
                            "></router-link>
                        <router-link :to="{
                            name: 'catalogue-list',
                            params: { pageNumber: [nextPageNumber] },
                        }" class="
                                page-counter__arrow page-counter__arrow--next
                                __icon-next-arrow
                            "></router-link>
                    </div>
                </div>
            </div>
        </section>
        <section class="catalogue-page__block tab-spoiler tab-spoiler--colored-bg">
            <div class="tab-spoiler__title title">
                <div class="title__top">Что стоит знать</div>
                <div class="title__bottom title__bold">
                    при выборе наушников
                </div>
            </div>
            <spoiler-tab-wrapper class="container" :titles="[
                'Есть ли гарантии?',
                'Сколько устройств можно подключить?',
                'Какой провод использовать?',
            ]" :spoilerParams="{
    isAccordeon: true,
}">
                <template #0>
                    <h5>Гарантия на всю продукцию - 3 месяца</h5>
                    <p>
                        Есть много вариантов Lorem Ipsum, но большинство из них
                        имеет не всегда приемлемые модификации, например,
                        юмористические вставки или слова, которые даже отдалённо
                        не напоминают латынь.
                    </p>
                    <p>
                        Если вам нужен Lorem Ipsum для серьёзного проекта, вы
                        наверняка не хотите какой-нибудь шутки, скрытой в
                        середине абзаца.
                    </p>
                </template>
                <template #1>
                    <h5>Возможно подключение только одного устройства</h5>
                    <p>
                        Есть много вариантов Lorem Ipsum, но большинство из них
                        имеет не всегда приемлемые модификации, например,
                        юмористические вставки или слова, которые даже отдалённо
                        не напоминают латынь.
                    </p>
                    <p>
                        Если вам нужен Lorem Ipsum для серьёзного проекта, вы
                        наверняка не хотите какой-нибудь шутки, скрытой в
                        середине абзаца.
                    </p>
                    <p>
                        Есть много вариантов Lorem Ipsum, но большинство из них
                        имеет не всегда приемлемые модификации, например,
                        юмористические вставки или слова, которые даже отдалённо
                        не напоминают латынь.
                    </p>
                    <p>
                        Если вам нужен Lorem Ipsum для серьёзного проекта, вы
                        наверняка не хотите какой-нибудь шутки, скрытой в
                        середине абзаца.
                    </p>
                </template>
                <template #2>
                    <p>
                        Есть много вариантов Lorem Ipsum, но большинство из них
                        имеет не всегда приемлемые модификации, например,
                        юмористические вставки или слова, которые даже отдалённо
                        не напоминают латынь.
                    </p>
                    <p>
                        Если вам нужен Lorem Ipsum для серьёзного проекта, вы
                        наверняка не хотите какой-нибудь шутки, скрытой в
                        середине абзаца.
                    </p>
                </template>
            </spoiler-tab-wrapper>
        </section>
    </main>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import SpoilerTabWrapper from "@/components/spoiler-tab/SpoilerTabWrapper";
import FilterBlock from "@/components/catalogue/FilterBlock";

export default {
    name: "CataloguePage",
    components: {
        SpoilerTabWrapper,
        FilterBlock,
    },
    data() {
        return {
            productsPerPage: 8,
            filteredProducts: [],
            productsSortType: "",
            sortedProducts: [],
            catalogueList: [],
            catalogueListPage: [],
        };
    },
    computed: {
        ...mapGetters(["products"]),
        pageNumber() {
            const pageNumber = this.$route.params.pageNumber;
            return (
                (Array.isArray(pageNumber)
                    ? parseInt(pageNumber[0])
                    : parseInt(pageNumber)) || [1]
            );
        },
        pagesAmount() {
            return Math.ceil(
                Object.keys(this.catalogueList).length / this.productsPerPage
            );
        },
        nextPageNumber() {
            const number = this.pageNumber + 1;
            return number <= this.pagesAmount ? number : this.pagesAmount;
        },
        prevPageNumber() {
            const number = this.pageNumber - 1;
            return number > 0 ? number : 1;
        },
    },
    methods: {
        ...mapActions(["loadProducts"]),
        calcMaxRangeValue(key) {
            const keyValuesList = new Set();
            this.products.forEach((prod) => {
                if (prod[key]) keyValuesList.add(prod[key]);
            });

            const keyValuesArray = Array.from(keyValuesList);
            return Math.max(...keyValuesArray);
        },
        getFilteredCatalogueList(filtered) {
            this.filteredProducts = filtered;
            this.$refs.sortKeysBlock.doSort(this.filteredProducts); // $emit("sortValueChange"), запустит getSortedCatalogueList()
        },
        getSortOrder(selected) {
            this.productsSortType = selected.value;
            if (this.sortedProducts) this.getCatalogueList();
        },
        getSortedCatalogueList(sorted) {
            this.sortedProducts = sorted;
            this.getCatalogueList();
        },
        getCatalogueList() {
            let sorted;
            switch (this.productsSortType) {
                case "end":
                    sorted = [...this.sortedProducts].reverse();
                    break;
                default:
                    sorted = this.sortedProducts;
                    break;
            }

            this.catalogueList = sorted;
            const endPos = this.productsPerPage * this.pageNumber;
            const startPos = endPos - this.productsPerPage;
            this.catalogueListPage = [];

            for (let i = startPos; i < endPos; i++) {
                const prod = this.catalogueList[i];
                if (prod) this.catalogueListPage.push(prod);
            }

            if (this.pageNumber > this.pagesAmount) {
                this.$router.push({
                    name: "catalogue-list",
                    params: { pageNumber: 1 },
                });
            }
        },
    },
    watch: {
        pageNumber() {
            this.getCatalogueList();
        },
    },
    created() {
        this.loadProducts();
    },
};
</script>