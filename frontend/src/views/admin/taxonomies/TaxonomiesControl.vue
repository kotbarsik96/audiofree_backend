<template>
    <div class="admin-page__control">
        <LoadingScreen v-if="isLoading"></LoadingScreen>
        <div class="admin-page__control-filtering inputs-flex">
            <TextInputWrapper name="name" id="name" v-model="filters.name">
                <template v-slot:label>
                    Название
                </template>
            </TextInputWrapper>
        </div>
        <div class="admin-page__listing" ref="tableContainer">
            <AdminListTable v-model="list" v-model:selectedItems="selectedItems" :columnsCount="3" addable
                ref="adminListTable" @deleteSelected="deleteAllSelected">
                <template v-slot:containerHeading>
                    <span>
                        Список {{ taxonomyTitle.titleGenitive }} (всего: {{ totalCount }})
                    </span>
                    <Transition name="grow">
                        <span v-if="error" class="error">
                            {{ error }}
                        </span>
                    </Transition>
                </template>
                <template v-slot:thead>
                    <th></th>
                    <th>Название</th>
                    <th>Действие</th>
                </template>
                <tr v-for="(item, index) in list" :key="item.id" :class="{ '__not-saved': isCreated(item.id) }" ref="tr">
                    <td>
                        <CheckboxLabel name="taxonomy-control-selection" :checked="selectedItems.includes(item.id)"
                            :value="item.id" v-model="selectedItems"></CheckboxLabel>
                    </td>
                    <td>
                        <textarea placeholder="Введите значение" v-model="list[index].name"
                            @keyup="adjustTextarea"></textarea>
                    </td>
                    <td>
                        <button v-if="isUnsaved(item.id)"
                            class="admin-list-table__control-button admin-list-table__control-button--save" type="button"
                            @click="saveItem(item.id)">
                            <SaveIcon></SaveIcon>
                        </button>
                        <button class="admin-list-table__control-button admin-list-table__control-button--delete"
                            type="button" @click="deleteItem(item.id)">
                            <TrashCanCircleIcon></TrashCanCircleIcon>
                        </button>
                    </td>
                </tr>
            </AdminListTable>
            <ListPagination ref="paginationComponent" @updateLoaded="getSavedList" v-model="list" v-model:error="error"
                v-model:isLoading="isLoading" v-model:count="totalCount" :loadLink="loadLink" :pagesLimit="8" :limit="10"
                :filters="filters" allData>
            </ListPagination>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import ListPagination from '@/components/pagination/ListPagination.vue'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import AdminListTable from '@/components/tables/AdminListTable.vue'
import { isCreated, deleteFromArrays, updateList, getSavedList, isUnsaved } from '@/components/tables/admin-list-table-methods.js'
import { adjustTextarea, adjustTextareas } from '@/assets/js/scripts.js'
import { useModalsStore } from '@/stores/modals.js'
import { useNotificationsStore } from '@/stores/notifications.js'
import { h, nextTick } from 'vue'
import axios from 'axios'
import { handleAjaxError } from '@/assets/js/scripts.js'

export default {
    name: 'TaxonomiesControl',
    emits: ['updateRouteKey'],
    components: {
        TextInputWrapper,
        LoadingScreen,
        ListPagination,
        ConfirmModal,
        AdminListTable
    },
    data() {
        return {
            isLoading: false,
            totalCount: 0,
            filters: {
                name: ''
            },
            error: '',
            selectedItems: [],
            list: [],
            listSaved: {}
        }
    },
    computed: {
        loadLink() {
            return `${import.meta.env.VITE_TAXONOMIES_GET_LINK}/${this.taxonomyName}`
        },
        taxonomyName() {
            return this.$route.params.taxonomyName
        },
        taxonomyTitle() {
            switch (this.$route.params.taxonomyName) {
                case 'brand':
                    return { title: 'Бренд', titleGenitive: 'брендов' }
                case 'type':
                    return { title: 'Тип', titleGenitive: 'типов' }
                case 'category':
                    return { title: 'Категория', titleGenitive: 'категорий' }
                case 'product_status':
                    return { title: 'Статус товара', titleGenitive: 'статусов товра' }
            }
        },
    },
    methods: {
        isCreated,
        deleteFromArrays,
        updateList,
        adjustTextarea,
        getSavedList,
        isUnsaved,
        async saveItem(id) {
            this.error = ''

            const item = this.list.find(o => o.id === id)
            if (!item)
                return

            this.isLoading = true
            // добавить новый
            if (this.isCreated(id)) {
                const link = `${import.meta.env.VITE_TAXONOMY_CREATE_LINK}${this.taxonomyName}`

                try {
                    await axios.post(link, { name: item.name })
                    this.deleteFromArrays(id)
                    this.updateList()
                } catch (err) {
                    handleAjaxError(err, this)
                    this.isLoading = false
                }
            }
            // обновить существующий
            else {
                const link = `${import.meta.env.VITE_TAXONOMY_UPDATE_LINK}${this.taxonomyName}/${id}`

                try {
                    await axios.post(link, { name: item.name })
                    this.updateList()
                } catch (err) {
                    handleAjaxError(err, this)
                    this.isLoading = false
                }
            }
        },
        deleteItem(itemId) {
            this.error = ''
            const callback = async () => {
                // если это добавленный элемент - просто удалить из массивов
                if (this.isCreated(itemId)) {
                    this.deleteFromArrays(itemId)
                }
                // если это элемент из БД, удалить через бекенд
                else {
                    const link = `${import.meta.env.VITE_TAXONOMY_DELETE_LINK}${this.taxonomyName}/${itemId}`

                    try {
                        const res = await axios.delete(link)
                        if (res.data.message) {
                            useNotificationsStore().addNotification({
                                timeout: 5000,
                                message: res.data.message
                            })
                        }
                    } catch (err) {
                        handleAjaxError(err, this)
                    }

                    this.updateList()
                }
            }

            const item = this.list.find(o => o.id === itemId)
            if (!item)
                return

            if (!item.name.trim()) {
                callback()
                return
            }

            useModalsStore().addModal({
                component: h(ConfirmModal, {
                    title: `Удалить ${this.taxonomyTitle.title.toLowerCase()} "${item.name}"?`,
                    confirmProps: {
                        text: 'Удалить',
                        callback
                    }
                })
            })
        },
        deleteAllSelected() {
            const callback = async () => {
                this.error = ''

                const idsList = []
                this.selectedItems.forEach(id => {
                    if (id.toString().includes(this.createdPrefix)) {
                        setTimeout(() => this.deleteFromArrays(id), index * 50)
                    } else
                        idsList.push(id)
                })

                if (idsList.length > 0) {
                    const link = `${import.meta.env.VITE_TAXONOMY_DELETE_LINK}${this.taxonomyName}`

                    try {
                        const res = await axios.delete(link, { data: { idsList } })
                        if (res.data.message) {
                            useNotificationsStore().addNotification({
                                timeout: 5000,
                                message: res.data.message
                            })
                        }
                        this.updateList()
                    } catch (err) {
                        handleAjaxError(err, this)
                    }
                }
            }

            useModalsStore().addModal({
                component: h(ConfirmModal,
                    {
                        title: `Выбрано ${this.taxonomyTitle.titleGenitive}: ${this.selectedItems.length}. Удалить их?`,
                        confirmProps: {
                            text: 'Удалить',
                            callback
                        },
                    }
                )
            })
        }
    },
    watch: {
        $route: {
            deep: true,
            handler() {
                this.$emit('updateRouteKey')
            }
        },
        list: {
            deep: true,
            async handler() {
                await nextTick()
                if (this.$refs.tableContainer)
                    adjustTextareas(this.$refs.tableContainer)
            }
        },
    },
}
</script>