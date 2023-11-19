<template>
    <div class="container">
        <div class="page-heading">
            <div class="breadcrumbs">
                <RouterLink class="breadcrumbs__link link" :to="{ name: 'Home' }">
                    Главная
                </RouterLink>
                <RouterLink class="breadcrumbs__link link" :to="{ name: 'DeliveryPayment' }">
                    Доставка и оплата
                </RouterLink>
            </div>
            <h1 class="page-title">
                Доставка и оплата
            </h1>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DeliveryPaymentPage',
}
</script>

<style></style>