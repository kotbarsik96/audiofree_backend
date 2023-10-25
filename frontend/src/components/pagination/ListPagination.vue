<!-- ВАЖНО: чтобы компонент работал, он должен находиться внутри route, у которого есть params.pageNumber 
    ({ name: '...', path: '/:pageNumber?' }) -->
<template>
    <div class="pagination">
        <button class="pagination__button pagination__button--prev" type="button" @click="setPage('prev')">
            <ChevronIcon></ChevronIcon>
        </button>
        <div class="pagination__numbers">
            <button v-for="pageNumber in visiblePages" :key="pageNumber" class="pagination__number"
                :class="{ '__active': currentPageNumber === pageNumber }" @click="setPage(pageNumber)">
                {{ pageNumber }}
            </button>
            <span class="pagination__ellipsis" v-if="showPaginationEllipsis()">
                ...
            </span>
            <button class="pagination__number" v-if="showPaginationEllipsis()"
                :class="{ '__active': currentPageNumber === currentPageNumber }" @click="setPage(pagesCount)">
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

export default {
    name: 'ListPagination',
    emits: ['update:modelValue', 'update:error', 'update:isLoading', 'update:count'],
    props: {
        /* массив, который содержит в себе элементы с текущей страницы */
        modelValue: {
            type: Array,
            required: true
        },
        error: String,
        isLoading: Boolean,
        count: Number,
        /* ссылка, по которой нужно загружать элементы списка, например, товары (https://../api/products). Ресурс должен возврать массив */
        loadLink: {
            type: String,
            required: true
        },
        /* ссылка, по которой можно загрузить число общего количества элементов (https://.../api/products/count). Ресурс должен вернуть такое: data: { count: 1 } */
        countLink: {
            type: String,
            required: true
        },
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
        forAdminPage: Boolean,
    },
    data() {
        return {
            totalCount: 0,
            list: {}
        }
    },
    computed: {
        pagesCount() {
            return Math.floor(this.totalCount / this.limit)
        },
        currentPageNumber() {
            return parseInt(this.$route.params.pageNumber) || 1
        },
        visiblePages() {
            const array = []
            const half = Math.floor(this.pagesLimit / 2)
            if (this.currentPageNumber < half) {
                for (let num = 1; num <= this.pagesLimit && num <= this.pagesCount; num++) {
                    array.push(num)
                }
            } else {
                let num = this.currentPageNumber - half
                const until = num + this.pagesLimit
                for (num; num <= until; num++) {
                    if (num > 0 && num <= this.pagesCount)
                        array.push(num)
                }
            }

            return array
        },
        offset() {
            return this.currentPageNumber * this.limit
        },
        shownElems() {
            return this.list[this.offset]
                || []
        }
    },
    methods: {
        async loadCount() {
            try {
                const res = await axios.get(this.countLink)
                const count = parseInt(res.data.count)
                if (!isNaN(count)) {
                    this.totalCount = count
                    this.$emit('update:count', this.totalCount)
                }
            } catch (err) {
                const data = err.response.data
                if (data.error)
                    this.$emit('update:error', data.error)
            }
        },
        async loadList() {
            // важно: использовать переменную offset, вместо использования this.offset, иначе после await offset может смениться и запишется this.list[this.offset] уже не туда
            const offset = this.offset
            if (Array.isArray(this.list[offset]))
                return

            this.$emit('update:isLoading', true)

            try {
                const params = {
                    limit: this.limit,
                    offset
                }
                if (this.forAdminPage)
                    params.forAdminPage = true

                const res = await axios.get(this.loadLink, { params })
                if (Array.isArray(res.data)) {
                    this.list[offset] = res.data
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
        showPaginationEllipsis() {
            return this.pagesCount > this.pagesLimit
                && this.visiblePages[this.visiblePages.length - 1] !== this.pagesCount
        }
    },
    watch: {
        list: {
            deep: true,
            handler() {
                this.trimList()
                this.$emit('update:modelValue', this.shownElems)
            }
        },
        currentPageNumber() {
            this.loadList()
        }
    },
    mounted() {
        this.loadCount()
        this.loadList()
    }
}
</script>