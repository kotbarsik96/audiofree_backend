<template>
    <div class="admin-page">
        <button class="admin-page__sidebar-button" :class="{ '__shown': sidebarShownMobile }" type="button"
            @click="sidebarShownMobile = !sidebarShownMobile">
            <ChevronIcon></ChevronIcon>
        </button>
        <aside class="admin-page__sidebar">
            <nav class="admin-page__nav">
                <ul class="admin-page__nav-list">
                    <AdminPanelExpandable class="admin-page__nav-item" name="Товары" :createTo="{ name: 'ProductCreate' }"
                        :controlTo="{ name: 'ProductsControl' }"></AdminPanelExpandable>
                    <SpoilerElem tag="li" class="admin-page__nav-item" spoilerBodyTag="ul"
                        spoilerBodyClass="admin-page__nav-sublist">
                        <template v-slot:buttonContent>
                            Таксономии
                        </template>
                        <AdminPanelExpandable class="admin-page__nav-subitem" name="Бренды" :createTo="{
                            name: 'TaxonomyCreate',
                            params: { taxonomyName: 'brand' }
                        }" :controlTo="{ name: 'TaxonomiesControl', params: { taxonomyName: 'brand' } }">
                        </AdminPanelExpandable>
                        <AdminPanelExpandable class="admin-page__nav-subitem" name="Категории" :createTo="{
                            name: 'TaxonomyCreate',
                            params: { taxonomyName: 'category' }
                        }" :controlTo="{ name: 'TaxonomiesControl', params: { taxonomyName: 'category' } }">
                        </AdminPanelExpandable>
                        <AdminPanelExpandable class="admin-page__nav-subitem" name="Типы" :createTo="{
                            name: 'TaxonomyCreate',
                            params: { taxonomyName: 'type' }
                        }" :controlTo="{ name: 'TaxonomiesControl', params: { taxonomyName: 'type' } }">
                        </AdminPanelExpandable>
                    </SpoilerElem>
                </ul>
            </nav>
        </aside>
        <div class="container admin-page__container">
            <RouterView :key="routeKey" @updateRouteKey="routeKey++"></RouterView>
        </div>
    </div>
</template>

<script>
import '@/assets/scss/admin.scss'
import AdminPanelExpandable from '@/components/admin-panel/AdminPanelExpandable.vue'
import SpoilerElem from '@/components/misc/SpoilerElem.vue';

export default {
    name: 'AdminPage',
    components: {
        AdminPanelExpandable,
        SpoilerElem
    },
    data() {
        return {
            routeKey: 1,
            sidebarShownMobile: false
        }
    }
}
</script>