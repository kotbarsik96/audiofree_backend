<template>
    <div class="admin-page__control">
        <LoadingScreen v-if="isLoading"></LoadingScreen>
        <div class="admin-page__control-filtering inputs-flex">
            <TextInputWrapper name="name" id="name" v-model="filters.name">
                <template v-slot:label>
                    Название
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="priority" id="priority" v-model="filters.priority">
                <template v-slot:label>
                    Приоритет
                </template>
            </TextInputWrapper>
        </div>
        <div class="admin-page__listing" ref="tableContainer">
            <AdminListTable v-model="list" v-model:selectedItems="selectedItems" :columnsCount="4" addable
                ref="adminListTable" @deleteSelected="deleteAllSelected">
                <template v-slot:containerHeading>
                    <span>
                        Список ролей (всего: {{ totalCount }})
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
                    <th>Приоритет</th>
                    <th>Действие</th>
                </template>
                <tr v-for="(item, index) in list" :key="item.id" :class="{ '__not-saved': isCreated(item.id) }" ref="tr">
                    <td>
                        <label class="checkbox">
                            <input type="checkbox" name="taxonomy-control-selection" :value="item.id"
                                v-model="selectedItems" :checked="selectedItems.includes(item.id)">
                            <div class="checkbox__box"></div>
                        </label>
                    </td>
                    <td>
                        <textarea placeholder="Введите значение" v-model="list[index].name"
                            @keyup="adjustTextarea"></textarea>
                    </td>
                    <td>
                        <textarea placeholder="Введите значение" v-model="list[index].priority"
                            @keyup="adjustTextarea"></textarea>
                    </td>
                    <td>
                        <button class="admin-list-table__control-button admin-list-table__control-button--save"
                            type="button" @click="saveItem(item.id)">
                            <SaveIcon></SaveIcon>
                        </button>
                        <button class="admin-list-table__control-button admin-list-table__control-button--delete"
                            type="button" @click="deleteItem(item.id)">
                            <TrashCanCircleIcon></TrashCanCircleIcon>
                        </button>
                    </td>
                </tr>
            </AdminListTable>
            <ListPagination ref="paginationComponent" v-model="list" v-model:error="error" v-model:isLoading="isLoading"
                v-model:count="totalCount" :loadLink="loadLink" :pagesLimit="8" :limit="10" :filters="filters" allData>
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
import { isCreated, deleteFromArrays } from '@/components/tables/admin-list-table-methods.js'
import { adjustTextarea, adjustTextareas } from '@/assets/js/scripts.js'
import { useModalsStore } from '@/stores/modals.js'
import { useNotificationsStore } from '@/stores/notifications.js'
import { h, nextTick } from 'vue'
import axios from 'axios'
import { handleAjaxError } from '@/assets/js/scripts.js'

export default {
    name: 'RolesControl',
    components: {
        TextInputWrapper,
        LoadingScreen,
        ListPagination,
        ConfirmModal,
        AdminListTable,
    },
    emits: ['updateRouteKey'],
    data() {
        return {
            isLoading: false,
            totalCount: 0,
            filters: {
                name: '',
                priority: ''
            },
            error: '',
            selectedItems: [],
            list: [],
        }
    },
    computed: {
        loadLink() {
            return `${import.meta.env.VITE_ROLES_GET_LINK}`
        },
    },
    methods: {
        isCreated,
        adjustTextarea,
        deleteFromArrays,
        updateList() {
            this.$refs.paginationComponent.loadList(true)
            this.selectedItems = this.selectedItems.filter(id => list.find(o => o.id === parseInt(id)))
        },
        async saveItem(id) {
            this.error = ''

            const item = this.list.find(o => o.id === id)
            if (!item)
                return

            this.isLoading = true
            // добавить новый
            if (this.isCreated(id)) {
                const link = `${import.meta.env.VITE_ROLE_CREATE_LINK}`

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
                const link = `${import.meta.env.VITE_ROLE_UPDATE_LINK}${id}`

                try {
                    await axios.post(link, { name: item.name, priority: item.priority })
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
                    const link = `${import.meta.env.VITE_ROLE_DELETE_LINK}${itemId}`

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
                    title: `Удалить роль "${item.name}"?`,
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
                this.selectedItems.forEach((id, index) => {
                    if (this.isCreated(id)) {
                        setTimeout(() => this.deleteFromArrays(id), index * 50)
                    } else
                        idsList.push(id)
                })

                if (idsList.length > 0) {
                    const link = `${import.meta.env.VITE_ROLE_DELETE_LINK}`

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
                        title: `Выбрано ролей: ${this.selectedItems.length}. Удалить их?`,
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
    async mounted() {
        await nextTick()
        adjustTextareas(this.$refs.tr)
    }
}
</script>