<!-- ВАЖНО: чтобы компонент работал, он должен находиться внутри route, у которого есть params.pageNumber 
    ({ name: '...', path: '/:pageNumber?' }) -->
<template>
    <div class="pagination" v-if="visiblePages.length > 0">
        <button class="pagination__button pagination__button--prev" type="button" @click="setPage('prev')">
            <ChevronIcon></ChevronIcon>
        </button>
        <div class="pagination__numbers">
            <button class="pagination__number" v-if="showPaginationEllipsis('start')"
                :class="{ '__active': currentPageNumber === 1 }" @click="setPage(1)">
                1
            </button>
            <span class="pagination__ellipsis" v-if="showPaginationEllipsis('start')">
                ...
            </span>
            <button v-for="pageNumber in visiblePages" :key="pageNumber" class="pagination__number"
                :class="{ '__active': currentPageNumber === pageNumber }" @click="setPage(pageNumber)">
                {{ pageNumber }}
            </button>
            <span class="pagination__ellipsis" v-if="showPaginationEllipsis('end')">
                ...
            </span>
            <button class="pagination__number" v-if="showPaginationEllipsis('end')"
                :class="{ '__active': currentPageNumber === pagesCount }" @click="setPage(pagesCount)">
                {{ pagesCount }}
            </button>
        </div>
        <button class="pagination__button pagination__button--next" type="button" @click="setPage('next')">
            <ChevronIcon></ChevronIcon>
        </button>
    </div>
</template>

<script>
import axios from 'axios'
import { isNumeric } from '@/assets/js/scripts.js'
import { setMatchMedia } from '@/assets/js/methods.js'
import { nextTick } from 'vue'

export default {
    name: 'ListPagination',
    emits: [
        'update:modelValue',
        'updateLoaded',
        'update:error',
        'update:isLoading',
        'update:count'
    ],
    props: {
        /* массив, который содержит в себе элементы с текущей страницы */
        modelValue: {
            type: Array,
            required: true
        },
        /* ссылка, по которой нужно загружать элементы списка, например, товары (https://../api/products). Ресурс должен возврать массив */
        loadLink: {
            type: String,
            required: true
        },
        /* v-model:error */
        error: String,
        /* v-model:isLoading */
        isLoading: Boolean,
        /* v-model:count — общее количество загруженных элементов, а не только на выбранной странице */
        count: Number,
        /* ограничение на показ количества страниц в пагинации. Остальное будет скрыто "...", а после него будет номер последней страницы */
        pagesLimit: {
            type: Number,
            default: 5
        },
        /* сколько загружать за раз */
        limit: {
            type: Number,
            default: 10
        },
        /* фильтры: { name: value } */
        filters: Object,
        allData: Boolean,
    },
    data() {
        return {
            totalCount: 0,
            totalCountLast: 0,
            list: {},
            filtersApplyTimeout: null,
            initialLoadPassed: false,
            matchMediaMatches: {
                max: {
                    '992': false,
                    '479': false,
                    '399': false
                }
            }
        }
    },
    computed: {
        pagesCount() {
            return Math.floor(this.totalCount / this.limit)
        },
        pagesLimitComputed() {
            if (this.matchMediaMatches.max['399'])
                return 1
            if (this.matchMediaMatches.max['479'])
                return 2
            if (this.matchMediaMatches.max['992'])
                return 4

            return this.pagesLimit
        },
        currentPageNumber() {
            return parseInt(this.$route.params.pageNumber) || 1
        },
        visiblePages() {
            const array = []
            const half = Math.floor(this.pagesLimitComputed / 2) || 1
            if (this.currentPageNumber <= half) {
                for (let num = 1; num <= this.pagesLimitComputed && num <= this.pagesCount; num++) {
                    array.push(num)
                }
            } else {
                let num = this.currentPageNumber - (half - 1)
                const until = num + this.pagesLimitComputed
                for (num; num <= until; num++) {
                    if (num > 0 && num <= this.pagesCount)
                        array.push(num)
                }
            }

            return array
        },
        offset() {
            return (this.currentPageNumber - 1) * this.limit
        },
        shownElems() {
            return this.list[this.offset]
                || []
        }
    },
    methods: {
        async loadList(forced = false) {
            // важно: использовать переменную offset, вместо использования this.offset, иначе после await offset может смениться и запишется this.list[this.offset] уже не туда
            const offset = this.offset
            const isAlreadyLoaded = this.totalCountLast === this.totalCount
                && Array.isArray(this.list[offset])
                && this.list[offset].length > 0

            if (isAlreadyLoaded && !forced) {
                this.updateModelValue()
                return
            }

            this.$emit('update:isLoading', true)

            try {
                const params = Object.assign({ ...this.filters } || {}, {
                    limit: this.limit,
                    offset
                })
                if (this.allData)
                    params.allData = true

                const res = await axios.get(this.loadLink, { params })
                if (Array.isArray(res.data.result)) {
                    this.list[offset] = res.data.result
                    this.$emit('updateLoaded', res.data.result)
                    if (isNumeric(res.data.total_count))
                        this.totalCount = parseInt(res.data.total_count)
                } else {
                    this.$emit('update:error', 'Произошла ошибка')
                }
            } catch (err) {
                const data = err.response.data
                if (data.error)
                    this.$emit('update:error', data.error)
            }

            this.$emit('update:isLoading', false)
        },
        setMatchMedia,
        setPage(value) {
            const name = this.$route.name

            if (isNumeric(value)) {
                value = parseInt(value)
                if (value < 1)
                    value = 1
                if (value > this.pagesCount)
                    value = this.pagesCount
                this.$router.push({ name, params: { pageNumber: value } })
            }
            else {
                let newNumber = this.currentPageNumber
                switch (value) {
                    case 'prev':
                        newNumber = newNumber - 1
                        if (newNumber <= 0)
                            newNumber = 1
                        break
                    case 'next':
                        newNumber = newNumber + 1
                        if (newNumber > this.pagesCount)
                            newNumber = this.pagesCount
                        break
                }
                this.$router.push({ name, params: { pageNumber: newNumber } })
            }
        },
        trimList() {
            const keys = Object.keys(this.list)
            if (keys.length > 10) {
                const diff = keys.length - 9
                for (let i = 0; i < diff; i++) {
                    const key = keys[i]
                    delete this.list[key]
                }
            }
        },
        showPaginationEllipsis(side) {
            switch (side) {
                case 'start':
                    return this.visiblePages[0] > 1
                case 'end':
                    return this.pagesCount > this.pagesLimitComputed
                        && this.visiblePages[this.visiblePages.length - 1] !== this.pagesCount
            }

            return false
        },
        updateModelValue() {
            this.trimList()
            this.$emit('update:modelValue', this.shownElems)
        }
    },
    watch: {
        list: {
            deep: true,
            handler() {
                this.updateModelValue()
            }
        },
        currentPageNumber() {
            this.loadList()
        },
        totalCount(newValue, oldValue) {
            if (newValue === oldValue)
                return

            this.$emit('update:count', this.totalCount)
            this.totalCountLast = oldValue
        },
        filters: {
            deep: true,
            handler() {
                if (!this.initialLoadPassed)
                    return

                if (this.filtersApplyTimeout)
                    clearTimeout(this.filtersApplyTimeout)

                this.filtersApplyTimeout = setTimeout(() => {
                    this.loadList()
                    this.$router.push({ name: this.$route.name, params: { pageNumber: 1 } })
                }, 1500)
            }
        }
    },
    async mounted() {
        await nextTick()
        await this.loadList()
        this.initialLoadPassed = true
        this.setMatchMedia()
    }
}
</script>