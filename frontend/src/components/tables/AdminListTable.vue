<!-- Пояснения для корректного внедрения компонента:
    передать в v-model="" массив с объектами, каждый из которых содержит в себе поле id
    передать пустой массив в v-model:selectedItems="selectedItems"
    указать prop :columnsCount="number" - количество колонок в таблице, включая чекбокс и колонку для кнопок-действий
    повесить ref="adminListTable"
    | ЕСЛИ addable === true: 
        | импортировать: 
        | import { isCreated, deleteFromArrays } from '@/components/tables/admin-list-table-methods.js'
        | и добавить эти методы в methods. Как раз в них будет использоваться this.$refs.adminListTable
        | isCreated(itemId) позволит узнать, является ли строка созданной
        | deleteFromArrays нужно использовать при удалении строки из массивов listCreated и modelValue
-->

<template>
    <table class="admin-list-table__table" ref="table">
        <tr ref="theadTr">
            <slot name="thead"></slot>
        </tr>
        <tr>
            <td>
                <label class="checkbox">
                    <input type="checkbox" name="taxonomy-control-selection" :checked="isAllChecked"
                        @change="selectAllItems">
                    <div class="checkbox__box"></div>
                </label>
            </td>
            <td v-for="col in columnsCount - 2"></td>
            <td>
                <button class="admin-list-table__control-button admin-list-table__control-button--delete"
                    :disabled="selectedItems.length < 1" type="button" @click="$emit('deleteSelected', selectedItems)">
                    <TrashCanCircleIcon></TrashCanCircleIcon>
                </button>
            </td>
        </tr>
        <slot></slot>
        <tr v-if="addable">
            <td>
                <button class="admin-list-table__add-button" type="button" @click="addItem">
                    <PlusIcon></PlusIcon>
                    Добавить
                </button>
            </td>
            <td v-for="col in columnsCount - 1"></td>
        </tr>
        <tr v-else>
            <td>
                <label class="checkbox">
                    <input type="checkbox" name="taxonomy-control-selection" :checked="isAllChecked"
                        @change="selectAllItems">
                    <div class="checkbox__box"></div>
                </label>
            </td>
            <td v-for="col in columnsCount - 2"></td>
            <td>
                <button class="admin-list-table__control-button admin-list-table__control-button--delete"
                    :disabled="selectedItems.length < 1" type="button" @click="$emit('deleteSelected', selectedItems)">
                    <TrashCanCircleIcon></TrashCanCircleIcon>
                </button>
            </td>
        </tr>
    </table>
</template>

<script>
import { isNumeric, generateRandom, removeFromArrayById } from '@/assets/js/scripts.js'

export default {
    name: 'AdminListTable',
    props: {
        addable: Boolean,
        modelValue: {
            type: Array,
            required: true,
            validator(array) {
                for (let obj of array) {
                    if (!obj || typeof obj !== 'object')
                        return false
                    if (!obj.id)
                        return false
                }
                return true
            }
        },
        selectedItems: Array,
        columnsCount: {
            type: Number,
            required: true
        }
    },
    emits: [
        'update:modelValue',
        'update:selectedItems',
        'deleteSelected'
    ],
    data() {
        return {
            listCreated: [],
            createdPrefix: 'created_'
        }
    },
    methods: {
        isCreated(itemId) {
            return itemId.toString().includes(this.createdPrefix)
        },
        deleteById(itemId) {
            removeFromArrayById(this.listCreated, itemId)
            const updated = [...this.modelValue]
            removeFromArrayById(updated, itemId)
            this.sortModelValue(updated)
        },
        /* добавит в this.modelValue элементы из this.listCreated и отсортирует так, чтобы элементы с listCreated были в конце */
        sortModelValue(arrayToSort = null) {
            if (!Array.isArray(arrayToSort))
                arrayToSort = [...this.modelValue]

            this.listCreated.forEach(obj => {
                const isInList = arrayToSort.find(o => o.id === obj.id)
                if (isInList)
                    return

                arrayToSort.push(obj)
            })

            const updated = arrayToSort.sort((obj1, obj2) => {
                const firstIsCreated = obj1.name.includes(this.createdPrefix)
                const secondIsCreated = obj2.name.includes(this.createdPrefix)
                if (firstIsCreated && secondIsCreated)
                    return 0
                if (firstIsCreated && !secondIsCreated)
                    return 1
                if (!firstIsCreated && secondIsCreated)
                    return -1
            })

            this.$emit('update:modelValue', updated)
        },
        selectAllItems(event) {
            const selectedItems = event.target.checked
                ? this.modelValue.map(obj => obj.id)
                : []
            this.$emit('update:selectedItems', selectedItems)
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
            this.listCreated.push(obj)
            this.sortModelValue()
        },
    },
    computed: {
        isAllChecked() {
            return this.selectedItems.length !== 0 &&
                this.selectedItems.length === this.modelValue.length
        }
    },
    watch: {
        listCreated: {
            deep: true,
            handler() {
                this.sortModelValue()
            }
        },
        modelValue() {
            this.$emit('update:selectedItems', this.selectedItems.filter(id => {
                if (this.modelValue.find(o => o.id === id))
                    return true

                return false
            }))

            const hasNotInList = this.listCreated.find(obj => !this.modelValue.find(o => o.id === obj.id))
            if (hasNotInList)
                this.sortModelValue()
        }
    },
}
</script>

<style></style>