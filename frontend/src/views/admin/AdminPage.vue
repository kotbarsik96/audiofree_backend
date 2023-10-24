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

<style lang="scss">
.admin-page {
    --admin_panel_color: var(--theme_color_2);
    --admin_panel_color_2: var(--theme_color_3);
    --admin_panel_button_hover_color: #990101;
    --sidebar_trans_dur: .3s;

    font-weight: 500;
    flex: 1 1 auto;
    display: flex;
    position: relative;

    &__sidebar-button {
        display: none;
    }

    &__sidebar {
        flex: 0 0 220px;
        background-color: var(--admin_panel_color);
        box-shadow: 1px 0px 10px 0px rgba(0, 0, 0, .4);
        color: #bababa;
        position: relative;
        z-index: 100;

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
        font-size: 19px;
        line-height: 21px;
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
        border-right: 1px solid #bababa;
        z-index: 15;
        pointer-events: none;
        visibility: hidden;
        transition-property: opacity, visibility;
        transition-duration: .3s;
        transition-delay: 0s, .3s;
    }

    &__nav-expanded.__expanded {
        opacity: 1;
        visibility: visible;
        transition-delay: 0s;
        pointer-events: all;
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

    @media (max-width: 1599px) {
        &__container.container {
            max-width: unset;
            min-width: unset;
            flex: 1 1 auto;
            margin-left: 25px;
        }
    }

    @media (max-width: 767px) {
        &__container.container {
            margin-left: 0;
        }

        &__sidebar {
            position: fixed;
            top: var(--header_height);
            left: -100%;
            z-index: 150;
            flex: 0 0 auto;
            width: 250px;
            height: calc(100% - var(--header_height));
            overflow-y: scroll;
            transition: left var(--sidebar_trans_dur);
        }

        &__sidebar-button.__shown {
            transform: translateX(250px) rotate(180deg);
        }

        &__sidebar-button.__shown+&__sidebar {
            left: 0;
        }

        &__sidebar-button {
            display: block;
            position: fixed;
            top: 60px;
            left: 15px;
            width: 25px;
            height: 25px;
            transform: rotate(0deg);
            z-index: 50;
            transition: transform var(--sidebar_trans_dur);

            svg {
                width: 100%;
                height: 100%;
            }
        }

        &__nav-expanded {
            transform: none;
            top: 100%;
            border-left: 0;
            border-right: 0;
            background-color: var(--theme_color);
            color: #fff;
        }

        &__nav-expanded>&__nav-expanded-item {
            border-color: #fff;
        }
    }
}
</style>