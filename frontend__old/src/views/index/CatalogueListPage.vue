<template>
    <div class="catalogue-block__cards-list cards-list">
        <div class="card-wrapper" :style="{ 'order': '3' }">
            <ProductInfoCard :title="'Распродажа'"></ProductInfoCard>
        </div>
        <div class="card-wrapper" v-for="(prod, index) in catalogueListPage" :key="prod.vendorCode"
            :style="{ 'order': index.toString() }">
            <ProductCard1 :vendorCode="prod.vendorCode"></ProductCard1>
        </div>
    </div>
</template>

<script>
export default {
    name: "CatalogueListPage",
    props: {
        catalogueListPage: Object
    },
};
</script>