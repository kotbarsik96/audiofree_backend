<template>
    <div class="search" :class="{ '__active': isHideable ? isSearchShown : true }">
        <div class="search__field" :class="{ '__shown-results': isResultsFoundAndShown }">
            <div class="search__icon __icon-search" @click="toggleSearchBlock"></div>
            <div class="search__input">
                <input type="text" placeholder="Поиск товара" v-model="searchQuery" @focus="showResults"
                    @blur="hideResults" />
            </div>
        </div>
        <TransitionGroup tag="ul" name="header-search" class="search-results"
            :style="{ 'display': isResultsFoundAndShown ? '' : 'none' }">
            <!-- <RouterLink v-for="prod in searchedProducts" :key="prod.id"
                :to="{ name: 'product', params: { id: prod.id } }" class="search-results__item">
                <img :src="`${rootPath}img/products/${prod.images[0]}`" :alt="prod.id"
                    class="search-results__item-image" />
                <div class="search-results__item-info">
                    <div class="search-results__item-name">{{ prod.name }}</div>
                    <div class="search-results__item-rating">
                        <ProductRating :rating="prod.rating"></ProductRating>
                    </div>
                    <div class="search-results__item-price">{{ prod.price }} ₽</div>
                </div>
            </RouterLink> -->
        </TransitionGroup>
    </div>
</template>

<script>

export default {
    name: "SearchField",
    props: {
        isHideable: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            searchQuery: "",
            isSearchShown: false,
            isResultsShown: false,
        };
    },
    methods: {
        toggleSearchBlock() {
            if (this.isHideable) {
                this.isSearchShown = !this.isSearchShown;
            }
        },
        showResults() {
            this.isResultsShown = true;
        },
        hideResults() {
            setTimeout(() => {
                this.isResultsShown = false;
            }, 100);
        },
    },
    computed: {
        isResultsFoundAndShown() {
            return this.isResultsShown && this.searchedProducts.length > 0;
        },
        routePath() {
            return this.$route.path;
        },
    },
    watch: {
        routePath() {
            this.searchQuery = "";
        },
    },
};
</script>