<template>
    <li class="admin-page__nav-expanded-item">
        <button type="button" @click="toggleExpansion">
            {{ name }}
        </button>
        <ul class="admin-page__nav-expanded" :class="{ '__expanded': isExpanded }">
            <li class="admin-page__nav-expanded-item">
                <RouterLink class="link" :to="createTo">
                    Создание
                </RouterLink>
            </li>
            <li class="admin-page__nav-expanded-item">
                <RouterLink class="link" :to="controlTo">
                    Просмотр
                </RouterLink>
            </li>
        </ul>
    </li>
</template>

<script>
export default {
    name: 'AdminPanelExpandable',
    props: {
        name: {
            type: String,
            required: true
        },
        createTo: {
            type: Object,
            required: true
        },
        controlTo: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            isExpanded: false
        }
    },
    mounted() {
        document.addEventListener('click', this.onDocumentClick)
    },
    beforeUnmount() {
        document.removeEventListener('click', this.onDocumentClick)
    },
    methods: {
        toggleExpansion() {
            this.isExpanded = !this.isExpanded
        },
        onDocumentClick(event) {
            if (event.target === this.$el
                || event.target.closest('.admin-page__nav-expanded-item') === this.$el)
                return

            this.isExpanded = false
        }
    }
}
</script>