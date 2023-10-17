<template>
    <div class="admin-page">
        <aside class="admin-page__sidebar">
            <nav class="admin-page__nav">
                <ul class="admin-page__nav-list">
                    <li class="admin-page__nav-item">
                        <RouterLink :to="{ name: 'ProductsControl' }" class="link">
                            Товары
                        </RouterLink>
                    </li>
                </ul>
            </nav>
        </aside>
        <div class="container">
            <RouterView></RouterView>
        </div>
    </div>
</template>

<script>

export default {
    name: 'AdminPage',
}
</script>

<style></style>