<template>
    <div class="list-table card">
        <div class="list-table__container card__container" ref="container">
            <ul class="list-table__column" v-for="(column, colIndex) in columns" :data-index="colIndex">
                <li class="list-table__column-item" v-for="(strOrObj, strIndex) in column"
                    :class="{ 'fw-700': strOrObj.isBold, 'list-table__column-item--empty': isEmpty(strOrObj) }"
                    :data-row-index="strIndex">
                    <template v-if="typeof strOrObj === 'string'">{{ strOrObj }}</template>
                    <template v-else>{{ strOrObj.string }}</template>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { setMatchMedia } from '@/assets/js/methods.js'

export default {
    name: 'ListTable',
    props: {
        items: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            columnLength: 7,
            maxColumns: 4,
            displayBlockMedia: 1019, // !* при изменении здесь менять и в <style></style>
            minItemHeightDesktop: 60,
            matchMediaMatches: {
                max: {
                    '1019': false
                }
            }
        }
    },
    computed: {
        columnLengthComputed() {
            const max = this.columnLength * this.maxColumns
            if (this.items.length > max) {
                let newColLength = this.columnLength + 1
                while (newColLength * this.maxColumns < this.items.length) {
                    newColLength++
                }

                return newColLength
            }

            return this.columnLength
        },
        columns() {
            const parts = Math.ceil(this.items.length / this.columnLengthComputed)
            const array = []
            for (let i = 0; i < parts; i++) {
                const column = []
                const offset = this.columnLengthComputed * i
                this.items.slice(offset, this.columnLengthComputed + offset)
                    .forEach(str => column.push(str))
                if (column.length < this.columnLengthComputed) {
                    const diff = this.columnLengthComputed - column.length
                    for (let j = 0; j < diff; j++)
                        column.push('')
                }
                array.push(column)
            }
            return array
        },
        displayBlockMediaMatch() {
            return this.matchMediaMatches.max[this.displayBlockMedia.toString()]
        }
    },
    methods: {
        setMatchMedia,
        isEmpty(strOrObj) {
            if (!strOrObj)
                return true
            if (typeof strOrObj === 'string' && !strOrObj.trim())
                return true
            if (typeof strOrObj === 'object' && !strOrObj.string)
                return true

            return false
        },
        setItemHeights() {
            if (this.columns.length < 2)
                return

            const rowsCount = Math.ceil(this.items.length / this.maxColumns)

            if (this.matchMediaMatches.max['1019']) {
                this.$refs.container.querySelectorAll('li')
                    .forEach(li => li.style.removeProperty('height'))

                return
            }

            for (let i = 0; i < rowsCount; i++) {
                const lis = this.$refs.container.querySelectorAll(`[data-row-index="${i}"]`)

                let highestLi = 0
                lis.forEach(li => {
                    if (li.offsetHeight > highestLi)
                        highestLi = li.offsetHeight
                })
                lis.forEach(li => li.style.height = `${highestLi}px`)
            }
        }
    },
    watch: {
        items: {
            deep: true,
            handler() {
                this.setItemHeights()
            }
        },
        displayBlockMediaMatch() {
            this.setItemHeights()
        }
    },
    mounted() {
        this.setItemHeights()
        this.setMatchMedia()
    },
}
</script>

<style lang="scss">
.list-table {
    --border_color: #ECECEC;

    &::before,
    &__container {
        box-shadow: 0 0 15px rgba(0, 0, 0, .15);
    }

    &__container {
        display: flex;
    }

    &__column {
        flex: 1 1 auto;

        &:not(:last-child) {
            border-right: 1px solid var(--border_color);
        }
    }

    &__column-item {
        padding: 18px 35px;
        font-size: 14px;
        line-height: 22px;
        min-height: 60px;
        display: flex;
        align-items: center;

        &:not(:last-child) {
            border-bottom: 1px solid var(--border_color);
        }
    }

    // !* при изменении max-width здесь менять и в <scripts></scripts> -> displayBlockMedia
    @media (max-width: 1019px) {

        &::before,
        &__container {
            box-shadow: none;
        }

        &::before {
            display: none;
        }

        &__container {
            background: transparent;
            display: block;
        }

        &__column {
            border-radius: var(--border_radius);
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, .1);

            &:last-child {
                margin-bottom: 0;
            }
        }

        &__column-item {
            padding: 20px 15px;
        }

        &__column-item--empty {
            display: none;
        }
    }
}
</style>