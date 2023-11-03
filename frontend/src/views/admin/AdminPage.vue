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
                    <li class="admin-page__nav-item">
                        <RouterLink class="link" :to="{ name: 'UsersControl' }">
                            Пользователи
                        </RouterLink>
                    </li>
                    <li class="admin-page__nav-item">
                        <RouterLink class="link" :to="{ name: 'RolesControl' }">
                            Роли
                        </RouterLink>
                    </li>
                    <SpoilerElem tag="li" class="admin-page__nav-item" spoilerBodyTag="ul"
                        spoilerBodyClass="admin-page__nav-sublist">
                        <template v-slot:buttonContent>
                            Таксономии
                        </template>
                        <li class="admin-page__nav-subitem">
                            <RouterLink class="link" :to="{ name: 'TaxonomiesControl', params: { taxonomyName: 'brand' } }">
                                Бренды
                            </RouterLink>
                        </li>
                        <li class="admin-page__nav-subitem">
                            <RouterLink class="link"
                                :to="{ name: 'TaxonomiesControl', params: { taxonomyName: 'category' } }">
                                Категории
                            </RouterLink>
                        </li>
                        <li class="admin-page__nav-subitem">
                            <RouterLink class="link" :to="{ name: 'TaxonomiesControl', params: { taxonomyName: 'type' } }">
                                Типы
                            </RouterLink>
                        </li>
                        <li class="admin-page__nav-subitem">
                            <RouterLink class="link"
                                :to="{ name: 'TaxonomiesControl', params: { taxonomyName: 'product_status' } }">
                                Статусы товара
                            </RouterLink>
                        </li>
                    </SpoilerElem>
                </ul>
            </nav>
        </aside>
        <div class="container admin-page__container">
            <RouterView :key="routeKey" @updateRouteKey="routeKey < 2 ? routeKey++ : routeKey--"></RouterView>
        </div>
    </div>
</template>

<script>
import '@/assets/scss/admin.scss'
import AdminPanelExpandable from '@/components/admin-panel/AdminPanelExpandable.vue'
import SpoilerElem from '@/components/misc/SpoilerElem.vue'

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