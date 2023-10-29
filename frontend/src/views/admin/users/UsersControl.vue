<template>
    <div class="admin-page__control">
        <LoadingScreen v-if="isLoading"></LoadingScreen>
        <div class="admin-page__control-filtering inputs-flex">
            <TextInputWrapper name="email" id="email" v-model="filters.email">
                <template v-slot:label>
                    Email
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="name" id="name" v-model="filters.name">
                <template v-slot:label>
                    Имя
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="surname" id="surname" v-model="filters.surname">
                <template v-slot:label>
                    Фамилия
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="patronymic" id="patronymic" v-model="filters.patronymic">
                <template v-slot:label>
                    Отчество
                </template>
            </TextInputWrapper>
            <ValueSelect v-model="filters.role" name="role" :values="rolesOnFilter"></ValueSelect>
        </div>
        <div class="admin-page__listing" ref="tableContainer">
            <AdminListTable v-model="list" v-model:selectedItems="selectedItems" :columnsCount="9" ref="adminListTable"
                @deleteSelected="deleteAllSelected">
                <template v-slot:containerHeading>
                    <span>
                        Список пользователей (всего: {{ totalCount }})
                    </span>
                    <Transition name="grow">
                        <span v-if="error" class="error">
                            {{ error }}
                        </span>
                    </Transition>
                </template>
                <template v-slot:thead>
                    <th></th>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Дата подтверждения email</th>
                    <th>ФИО</th>
                    <th>Роль</th>
                    <th>Номер телефона</th>
                    <th>Дата регистрации</th>
                    <th>Действие</th>
                </template>
                <tr v-for="(item, index) in list">
                    <td>
                        <label class="checkbox">
                            <input type="checkbox" name="taxonomy-control-selection" :value="item.id"
                                v-model="selectedItems" :checked="selectedItems.includes(item.id)">
                            <div class="checkbox__box"></div>
                        </label>
                    </td>
                    <td>
                        {{ item.id }}
                    </td>
                    <td>
                        {{ item.email }}
                    </td>
                    <td>
                        {{ getDate(item.email_verified_at) || 'Почта не подтверждена' }}
                    </td>
                    <td>
                        {{ item.name }}{{ item.patronymic ? ' ' + item.surname : '' }}{{ item.surname ? ' ' + item.surname :
                            '' }}
                    </td>
                    <td>
                        <ValueSelect :name="`user_role_${item.id}`" :values="roles" v-model="list[index].role_id">
                        </ValueSelect>
                    </td>
                    <td>
                        {{ item.phone_number || 'Не указан' }}
                    </td>
                    <td>
                        {{ getDate(item.created_at) }}
                    </td>
                    <td>
                        <button v-if="isUnsaved(item.id)"
                            class="admin-list-table__control-button admin-list-table__control-button--save" type="button"
                            @click="saveItem(item.id)">
                            <SaveIcon></SaveIcon>
                        </button>
                        <button class="admin-list-table__control-button admin-list-table__control-button--delete"
                            type="button" @click="deleteUser(item.id)">
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
import ValueSelect from '@/components/inputs/ValueSelect.vue'
import { adjustTextarea, adjustTextareas } from '@/assets/js/scripts.js'
import { useModalsStore } from '@/stores/modals.js'
import { useNotificationsStore } from '@/stores/notifications.js'
import { h, nextTick } from 'vue'
import axios from 'axios'
import { handleAjaxError } from '@/assets/js/scripts.js'
import { getDate } from '@/assets/js/methods.js'
import { updateList, getSavedList, isUnsaved } from '@/components/tables/admin-list-table-methods.js'

export default {
    name: 'UsersControl',
    components: {
        TextInputWrapper,
        LoadingScreen,
        ListPagination,
        ConfirmModal,
        AdminListTable,
        ValueSelect
    },
    data() {
        return {
            isLoading: false,
            filters: {
                email: '',
                name: '',
                surname: '',
                patronymic: '',
                role: '',
            },
            totalCount: 0,
            error: '',
            selectedItems: [],
            list: [],
            listSaved: [],
            roles: []
        }
    },
    computed: {
        loadLink() {
            return `${import.meta.env.VITE_USERS_GET_LINK}`
        },
        rolesOnFilter() {
            return [{ string: 'Все роли', value: 'false' }, ...this.roles]
        }
    },
    methods: {
        adjustTextarea,
        getDate,
        updateList,
        isUnsaved,
        getSavedList,
        deleteUser(userId) {
            const callback = async () => {
                const link = `${import.meta.env.VITE_API_LINK}user/delete/${userId}`

                try {
                    const res = await axios.delete(link)
                    if (res.data.message) {
                        useNotificationsStore()
                            .addNotification({ timeout: 5000, message: res.data.message })
                    }
                    this.updateList()
                } catch (err) {
                    handleAjaxError(err, this)
                }
            }

            const user = this.list.find(o => o.id === userId)
            if (!user)
                return

            const component = h(ConfirmModal, {
                title: `Удалить пользователя ${user.email}?`,
                confirmProps: {
                    text: 'Удалить',
                    callback
                },
            })
            useModalsStore().addModal({
                component
            })
        },
        deleteAllSelected() {
            const callback = async () => {
                const link = `${import.meta.env.VITE_API_LINK}users/delete`

                try {
                    const res = await axios.delete(link, {
                        data: {
                            idsList: this.selectedItems
                        }
                    })
                    if (res.data.message) {
                        useNotificationsStore()
                            .addNotification({ timeout: 5000, message: res.data.message })
                    }
                    this.updateList()
                } catch (err) {
                    handleAjaxError(err, this)
                }
            }

            const component = h(ConfirmModal, {
                title: `Удалить выбранных пользователей? (${this.selectedItems.length})`,
                confirmProps: {
                    text: 'Удалить',
                    callback
                },
            })
            useModalsStore().addModal({
                component
            })
        },
        async saveItem(id) {
            this.error = ''

            const item = this.list.find(o => o.id === id)
            if (!item)
                return

            this.isLoading = true

            try {
                const link = `${import.meta.env.VITE_USER_ROLE_UPDATE}${id}/${item.role_id}`
                const updateRoleRes = await axios.post(link)
                if(updateRoleRes.data.message) {
                    useNotificationsStore()
                        .addNotification({ timeout: 5000, message: updateRoleRes.data.message }) 
                }

                this.updateList()
            } catch (err) {
                handleAjaxError(err, this)
                this.isLoading = false
            }
        },
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
        nextTick().then(() => {
            adjustTextareas(this.$refs.tr)
        })

        axios.get(import.meta.env.VITE_ROLES_GET_LINK)
            .then((res) => {
                if (Array.isArray(res.data.result)) {
                    this.roles = res.data.result.map(obj => {
                        return { string: obj.name, value: obj.id }
                    })
                }
            })
            .catch(() => this.error = 'Не удалось загрузить список ролей')
    }
}
</script>

<style></style>