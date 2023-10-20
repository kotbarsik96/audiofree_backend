<template>
    <div class="admin-page">
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
            <RouterView></RouterView>
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
    }
}
</script>

<style lang="scss">
.admin-page {
    --admin_panel_color: var(--theme_color_2);
    --admin_panel_color_2: var(--theme_color_3);
    --admin_panel_button_hover_color: #990101;

    font-weight: 500;
    flex: 1 1 auto;
    display: flex;
    position: relative;

    &__sidebar {
        flex: 0 0 270px;
        background-color: var(--admin_panel_color);
        box-shadow: 1px 0px 10px 0px rgba(0, 0, 0, .4);
        color: #bababa;

        button,
        .link {
            color: inherit;
            width: 100%;
            text-align: left;
            transition-property: color, background-color;
            transition-duration: .3s;

            &:hover {
                background-color: var(--admin_panel_button_hover_color);
                color: #e9e9e9;
            }
        }
    }

    &__nav-item {
        position: relative;
        font-size: 21px;
        line-height: 24px;
        border-bottom: 1px solid #bababa;
    }

    &__nav-item>button,
    &__nav-expanded-item>a {
        display: block;
        padding: 10px;
    }

    &__nav-expanded {
        position: absolute;
        right: 0;
        top: 0;
        transform: translate(100%, 15%);
        min-width: 250px;
        background-color: var(--admin_panel_color);
        opacity: 0;
        border-left: 1px solid #bababa;
        z-index: 0;
        visibility: hidden;
        transition-property: opacity, visibility;
        transition-duration: .3s;
        transition-delay: 0s, .3s;
    }

    &__nav-expanded.__expanded {
        opacity: 1;
        visibility: visible;
        transition-delay: 0s;
        z-index: 15;
    }

    &__nav-expanded-item {
        border-bottom: 1px solid #bababa;
    }

    &__nav-sublist {
        background-color: var(--admin_panel_color_2);
        box-shadow: inset 0px 0px 10px 1px #223c50;
    }

    &__nav-subitem {
        position: relative;
        border-bottom: 1px solid #bababa;

        &:last-child {
            border-bottom-width: 0;
        }
    }

    &__nav-subitem button,
    &__nav-subitem .link {
        padding: 5px 15px 5px 25px;
    }

    &__nav-subitem &__nav-expanded {
        background: var(--admin_panel_color_2);
    }
}
</style>