<template>
    <div class="table-container">
        <div class="admin-table" ref="table">
            <div class="admin-table__header" v-if="headers.length === 1">
                <div class="admin-table__header-value admin-table__header-value--single">
                    {{ headers[0] }}
                </div>
            </div>
            <div v-else class="admin-table__header">
                <div class="admin-table__header-value">
                    {{ headers[0] }}
                </div>
                <div class="admin-table__header-value">
                    {{ headers[1] }}
                </div>
            </div>
            <div v-for="(obj, objIndex) in values" class="admin-table__row">
                <div class="admin-table__name" :class="{ '__empty': !obj.name }">
                    <span @click="setContentEditable" @input="onContentEditableInput('name', objIndex, $event)">
                        {{ obj.name }}
                    </span>
                    <button class="admin-table__remove" type="button" @click="removeRow(objIndex)">
                        <TrashCanCircleIcon></TrashCanCircleIcon>
                    </button>
                </div>
                <div class="admin-table__values">
                    <div class="admin-table__value" v-for="(value, valueIndex) in obj.values">
                        <span @click="setContentEditable"
                            @input="onContentEditableInput('value', [objIndex, valueIndex], $event)">
                            {{ value }}
                        </span>
                        <button class="admin-table__remove" type="button" @click="removeValueSubrow(objIndex, valueIndex)">
                            <TrashCanCircleIcon></TrashCanCircleIcon>
                        </button>
                    </div>
                    <div class="admin-table__value" v-if="multivalues">
                        <button class="admin-table__add-new link" type="button" @click="addValueRow(objIndex)">
                            <PlusCircleIcon></PlusCircleIcon>
                            <span>Добавить</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="admin-table__row">
                <button class="admin-table__add-new link" type="button" @click="addRow">
                    <PlusCircleIcon></PlusCircleIcon>
                    <span>Добавить</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { delay } from '@/assets/js/scripts.js'
import { useModalsStore } from '@/stores/modals.js'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import { h } from 'vue'

export default {
    name: 'AdminTable',
    emits: ['update:modelValue'],
    props: {
        /* modelValue[i]: { name: '', value: ''|[] }. Через v-model передаются дефолтные значения для таблицы, которые прокидываются в this.values */
        modelValue: {
            type: Array,
            validator(arr) {
                for (let obj of arr) {
                    if (!obj || typeof obj !== 'object')
                        return false

                    // неправильно передан/не передан name
                    if (typeof obj.name !== 'string')
                        return false

                    // неправильно передан/не передан obj.value и obj.values
                    if (typeof obj.value !== 'string' && !Array.isArray(obj.values))
                        return false
                }
                return true
            }
        },
        headers: {
            type: Array,
            default: []
        },
        multivalues: Boolean
    },
    data() {
        return {
            /* формат: values[i]: { name: '', values: [] } */
            values: []
        }
    },
    methods: {
        addRow() {
            this.values.push({ name: '', values: [''] })
        },
        addValueRow(objIndex) {
            this.values[objIndex].values.push('')
        },
        async setContentEditable(event) {
            // сначала пройдет this.unsetContentEditable()
            await delay(0)
            event.target.setAttribute('contenteditable', 'true')
            event.target.addEventListener('blur', this.unsetContentEditable)
            event.target.focus()
        },
        unsetContentEditable(event = null) {
            this.$refs.table.querySelectorAll('[contenteditable]')
                .forEach(el => {
                    el.removeEventListener('blur', this.unsetContentEditable)
                    el.removeAttribute('contenteditable')
                })
        },
        onContentEditableInput(key, indexOrIndexes, event) {
            const value = event.target.textContent.replace(/\s{2,}/g, ' ')
            if (key === 'name') {
                const index = indexOrIndexes
                this.values[index].name = value
            }
            if (key === 'value') {
                const objIndex = indexOrIndexes[0]
                const valueIndex = indexOrIndexes[1]
                this.values[objIndex].values[valueIndex] = value
            }
        },
        removeRow(objIndex) {
            let title = 'Удалить всю строку?';
            if (this.headers.length > 1)
                title += ` (${this.headers[0]}: ${this.values[objIndex].name || '<не заполнено>'})`

            useModalsStore().addModal({
                component: h(ConfirmModal, {
                    title,
                    confirmProps: {
                        text: 'Удалить',
                        callback: () => {
                            this.values.splice(objIndex, 1)
                        }
                    },
                    declineProps: {
                        text: 'Не удалять'
                    }
                })
            })
        },
        removeValueSubrow(objIndex, valueIndex) {
            // если осталась только одна подстрока, удаляет всю строку вместе со значением
            const arr = this.values[objIndex].values
            if (arr.length > 1)
                arr.splice(valueIndex, 1)
            else
                this.removeRow(objIndex)
        }
    },
    watch: {
        values: {
            deep: true,
            handler(){
                this.$emit('update:modelValue', this.values)
            }
        }
    },
    mounted() {
        this.values = this.modelValue.map(obj => {
            let values = Array.isArray(obj.values)
                ? obj.values : []

            if (typeof obj.value === 'string')
                values.push(obj.value)

            return {
                name: obj.name,
                values
            }
        })
    },
    beforeUnmount() {
        this.unsetContentEditable()
    }
}
</script>

<style lang="scss">
.admin-table {
    --min_height: 30px;

    border: 1px solid #bababa;

    &__header,
    &__row {
        border-bottom: 1px solid #bababa;
        min-height: var(--min_height);
    }

    &__name,
    &__header-value:not(:last-child) {
        border-right: 1px solid #bababa;
    }

    &__header-value,
    &__name,
    &__value {
        padding: 5px 10px;
    }

    &__header-value,
    &__name,
    &__values {
        flex: 0 0 50%;
    }

    &__name,
    &__value {
        font-weight: 400;
        font-size: 18px;
        position: relative;
        padding-right: 55px;

        span {
            display: block;

            &:empty::before {
                content: 'Начните вводить здесь';
                cursor: text;
                font-style: italic;
                color: #bababa;
            }

            &:focus {
                min-width: 1em;
                min-height: 1em;
            }
        }
    }

    &__header {
        display: flex;
        background-color: var(--theme_color);
        color: #fff;
    }

    &__header-value {
        text-align: center;
        font-size: 21px;
        font-weight: 500;
        padding: 5 10px;

        &--single {
            flex: 1 1 auto;
        }
    }

    &__row {
        display: flex;

        &:last-child {
            border-bottom-width: 0;
        }
    }

    &__name {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    &__value {
        border-bottom: 1px solid #bababa;
        min-height: var(--min_height);

        &:last-child {
            border-bottom-width: 0;
        }
    }

    &__value &__add-new {
        padding-left: 0;
    }

    &__add-new {
        display: flex;
        align-items: center;
        font-size: 18px;
        line-height: 21px;
        padding: 5px 10px;
        font-weight: 500;

        svg {
            width: 21px;
            height: 21px;
            margin-right: 5px;
            transform: translateY(-1px);
        }
    }

    &__remove {
        position: absolute;
        right: 15px;
        top: 2px;
        width: 25px;
        height: 25px;

        svg {
            width: 100%;
            height: 100%;
            color: var(--error_color);
        }
    }
}
</style>