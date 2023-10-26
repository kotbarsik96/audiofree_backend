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
        <div class="admin-page__listing">
            <div class="admin-list-table">
                <div class="admin-list-table__heading">
                    <ListIcon></ListIcon>
                    <span>
                        Список {{ taxonomyTitle.titleGenitive }} (всего: {{ totalCount }})
                    </span>
                    <Transition name="grow">
                        <span v-if="error" class="error">
                            {{ error }}
                        </span>
                    </Transition>
                </div>
                <div class="admin-list-table__container">
                    <table class="admin-list-table__table" ref="table">
                        <tr>
                            <th></th>
                            <th>Название</th>
                            <th>Действие</th>
                        </tr>
                        <tr>
                            <td>
                                <label class="checkbox">
                                    <input type="checkbox" name="taxonomy-control-selection" ref="allItemsCheckboxTop"
                                        :checked="isAllChecked" @change="selectAllItems">
                                    <div class="checkbox__box"></div>
                                </label>
                            </td>
                            <td></td>
                            <td>
                                <button class="admin-list-table__control-button admin-list-table__control-button--delete"
                                    :disabled="selectedItems.length < 1" type="button" @click="deleteAllSelected">
                                    <TrashCanCircleIcon></TrashCanCircleIcon>
                                </button>
                            </td>
                        </tr>
                        <tr v-for="(item, index) in list" :key="item.id" :class="{ '__not-saved': listCreated.find(o => o.id === item.id) }">
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
                        <tr>
                            <td></td>
                            <td>
                                <button class="admin-list-table__add-button" type="button" @click="addItem">
                                    <PlusIcon></PlusIcon>
                                    Добавить
                                </button>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                    <div class="admin-list-table__pagination">
                        <ListPagination ref="paginationComponent" v-model="list" v-model:error="error"
                            v-model:isLoading="isLoading" v-model:count="totalCount" :loadLink="loadLink" :pagesLimit="8"
                            :limit="10" :filters="filters" allData></ListPagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import ListPagination from '@/components/pagination/ListPagination.vue'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import { useModalsStore } from '@/stores/modals.js'
import { useNotificationsStore } from '@/stores/notifications.js'
import { h, nextTick } from 'vue'
import axios from 'axios'
import { selectAllItems } from '@/assets/js/methods.js'
import { isNumeric, generateRandom, handleAjaxError, removeFromArrayById } from '@/assets/js/scripts.js'

export default {
    name: 'TaxonomiesControl',
    emits: ['updateRouteKey'],
    components: {
        TextInputWrapper,
        LoadingScreen,
        ListPagination,
        ConfirmModal
    },
    data() {
        return {
            createdPrefix: 'created_',
            isLoading: false,
            totalCount: 0,
            filters: {
                name: ''
            },
            error: '',
            selectedItems: [],
            list: [],
            listCreated: [],
            listCreatedHidden: []
        }
    },
    computed: {
        loadLink() {
            return `${import.meta.env.VITE_TAXONOMIES_GET_LINK}/${this.taxonomyName}?getCount=true`
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
        isAllChecked() {
            return this.selectedItems.length === this.list.length
                && this.selectedItems.length !== 0
        }
    },
    methods: {
        updateList() {
            this.$refs.paginationComponent.loadList(true)
            this.selectedItems = this.selectedItems.filter(id => list.find(o => o.id === parseInt(id)))
        },
        selectAllItems,
        /* добавит в this.list элементы из this.listCreated и отсортирует так, чтобы элементы с listCreated были в конце */
        sortList() {
            this.listCreated.forEach(obj => {
                const isInList = this.list.find(o => o.id === obj.id)
                if (isInList)
                    return
                this.list.push(obj)
            })

            this.list = this.list.sort((obj1, obj2) => {
                const firstIsCreated = obj1.name.includes(this.createdPrefix)
                const secondIsCreated = obj2.name.includes(this.createdPrefix)
                if (firstIsCreated && secondIsCreated)
                    return 0
                if (firstIsCreated && !secondIsCreated)
                    return 1
                if (!firstIsCreated && secondIsCreated)
                    return -1
            })
        },
        adjustTextarea(eventOrAll) {
            if (eventOrAll === 'all') {
                this.$refs.table.querySelectorAll('textarea')
                    .forEach(textarea => this.adjustTextarea({ target: textarea }))
            } else {
                const textarea = eventOrAll.target
                textarea.style.height = '1px'
                textarea.style.height = `${textarea.scrollHeight}px`
            }
        },
        addItem() {
            const usedIds = this.listCreated.map(obj => {
                if (isNumeric(obj.id))
                    return null

                return obj.id.toString().replace(this.createdPrefix, '')
            }).filter(id => id)
            const obj = {
                id: this.createdPrefix + generateRandom(usedIds),
                name: '',
            }
            this.list.push(obj)
            this.listCreated.push(obj)
        },
        async saveItem(id) {
            this.error = ''

            const item = this.list.find(o => o.id === id)
            if (!item)
                return

            this.isLoading = true
            // добавить новый
            if (id.toString().includes(this.createdPrefix)) {
                const link = `${import.meta.env.VITE_TAXONOMY_CREATE_LINK}${this.taxonomyName}`

                try {
                    await axios.post(link, { name: item.name })
                    removeFromArrayById(this.listCreated, id)
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
                if (itemId.toString().includes(this.createdPrefix)) {
                    removeFromArrayById(this.listCreated, itemId)
                    removeFromArrayById(this.list, itemId)
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
        async deleteAllSelected() {
            this.error = ''

            const idsList = []
            this.selectedItems.forEach(id => {
                if (id.toString().includes(this.createdPrefix)) {
                    removeFromArrayById(this.selectedItems, id)
                    removeFromArrayById(this.listCreated, id)
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
                this.sortList()
                this.adjustTextarea('all')
            }
        },
    },
}
</script>

<style></style>