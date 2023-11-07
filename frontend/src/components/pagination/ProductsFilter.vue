<template>
    <div class="filter card" :class="{ 'filter--body-hidden': isBodyHiddenComputed, '__sticky': isSticky }">
        <div class="filter__container card__container">
            <div class="filter__header" @click="() => isBodyHidden = !isBodyHidden">
                <span class="filter__header-text">
                    Фильтр товаров
                </span>
                <FilterIcon></FilterIcon>
            </div>
            <div class="filter__body" ref="filterBody">
                <div class="filter__section" v-for="section of sectionsComputed" :key="section.name">
                    <div class="filter__section-title">
                        {{ section.title }}:
                    </div>
                    <div class="filter__section-body" v-if="section.type === 'checkbox'">
                        <CheckboxLabel v-for="obj of section.values" :key="obj.value" :name="`filter__${section.name}`"
                            :value="obj.value" v-model="filterInput[section.name]">
                            {{ obj.string }}
                        </CheckboxLabel>
                    </div>
                    <div class="filter__section-body" v-if="section.type === 'radio'">
                        <RadioLabel v-for="obj of section.values" :key="obj.value" v-model="filterInput[section.name]"
                            :name="`filter__${section.name}`" :value="obj.value">
                            {{ obj.string }}
                        </RadioLabel>
                    </div>
                    <div class="filter__section-body" v-if="section.type === 'range'">
                        <DoubleRange ref="doubleRange" :name="section.name" placeholderMin="499₽" placeholderMax="8499₽"
                            :minValue="section.minValue" :maxValue="section.maxValue"
                            v-model:modelValueMin="filterInput[section.name].min"
                            v-model:modelValueMax="filterInput[section.name].max"></DoubleRange>
                    </div>
                </div>
                <div class="filter__apply">
                    <button class="button" type="button" @click="clear">
                        Очистить фильтры
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DoubleRange from '@/components/inputs/DoubleRange.vue'
import { isNumeric, getHeight } from '@/assets/js/scripts.js'
import { setMatchMedia } from '@/assets/js/methods.js'
import { gsap } from 'gsap'

export default {
    name: 'ProductsFilter',
    props: {
        /*  section: [
            { 
                title: 'Бренд', 
                name: 'brand', 
                type: 'radio|checkbox|range',
                ...(в зависимости от типа) 
            }
        ]
            для radio/checkbox:
                [{ values: [{ string: 'Xiaomi', value: 1 }, 'Apple'] }] (строка 'Apple' превратится в объект { string: 'Apple', value: 'Apple' })
            для range: 
                [{ minValue: 0, maxValue: 8499 }]
        */
        sections: {
            type: Array,
            required: true,
            validator(array) {
                for (let section of array) {
                    switch (section.type) {
                        case 'radio':
                        case 'checkbox':
                            if (!Array.isArray(section.values))
                                return false
                            break
                        case 'range':
                            if (!isNumeric(section.max))
                                return false
                            break
                        default:
                            return false
                    }

                    return true
                }
            }
        },
        modelValue: Object,
        /* те ключи из modelValue (т.е. фильтра), которые в методе clear не будут затронуты */
        unclearableFilters: Array,
    },
    emits: ['update:modelValue'],
    components: {
        DoubleRange
    },
    data() {
        return {
            filterInput: Object.assign({}, this.modelValue),
            isBodyHidden: true,
            matchMediaMatches: {
                max: {
                    '919': false
                }
            },
            isSticky: false
        }
    },
    computed: {
        sectionsComputed() {
            const mapButtonValues = (objOrString) => {
                if (!objOrString)
                    return null

                if (typeof objOrString === 'string') {
                    return {
                        string: objOrString,
                        value: objOrString
                    }
                }

                if (typeof objOrString === 'object') {
                    if (objOrString.value && !objOrString.string)
                        objOrString.string = objOrString.value
                    if (!objOrString.value && objOrString.string)
                        objOrString.value = objOrString.string

                    if (!objOrString.value && !objOrString.string)
                        return null

                    return objOrString
                }

                return null
            }

            return this.sections.map(section => {
                switch (section.type) {
                    case 'checkbox':
                    case 'radio':
                        section.values = section.values.map(mapButtonValues)
                            .filter(o => o)
                        return section
                    case 'range':
                        if (isNumeric(section.minValue))
                            section.minValue = parseInt(section.minValue)
                        else
                            section.minValue = 0

                        section.maxValue = parseInt(section.maxValue)
                        return section
                }
            })
        },
        isBodyHiddenComputed() {
            if (this.matchMediaMatches.max['919'] && this.isBodyHidden)
                return true

            return false
        }
    },
    methods: {
        setMatchMedia,
        clear() {
            this.$refs.doubleRange.forEach(dbr => dbr.setDefaults())

            const cleared = Object.assign({}, this.filterInput)
            for (let key in cleared) {
                const val = cleared[key]
                if (!val || this.isUnclearable(key))
                    continue

                if (typeof val === 'string')
                    cleared[key] = ''
                else if (Array.isArray(val))
                    cleared[key] = []
            }

            const fbody = this.$refs.filterBody
            fbody.querySelectorAll('input[type="checkbox"]')
                .forEach(cb => cb.checked = false)
            fbody.querySelectorAll('input[type="radio"]')
                .forEach(r => r.checked = false)

            this.filterInput = cleared
        },
        isUnclearable(key){
            if(!Array.isArray(this.unclearableFilters))
                return false

            return this.unclearableFilters.includes(key)
        },
        updateModelValue() {
            this.$emit('update:modelValue', this.filterInput)
        },
        getFilterModelValues() {
            const filters = {}
            for (let key in this.modelValue) {
                const obj = this.modelValue[key]
                if (obj)
                    filters[key] = obj.modelValue
            }
            return filters
        },
        setFilterBodyHeight() {
            const duration = 0.5
            const fbody = this.$refs.filterBody

            if (this.isBodyHiddenComputed) {
                gsap.to(fbody, {
                    maxHeight: '0px',
                    duration,
                    onComplete: () => {
                        if (!this.isBodyHiddenComputed)
                            fbody.style.removeProperty('max-height')
                    }
                })
            } else {
                if (!this.matchMediaMatches.max['919']) {
                    fbody.style.removeProperty('max-height')
                } else {
                    gsap.set(fbody, { overflow: 'hidden' })
                    const maxHeight = getHeight(fbody)
                    gsap.to(fbody, {
                        maxHeight,
                        duration,
                        onComplete: () => fbody.style.removeProperty('overflow')
                    })
                }
            }
        },
        onResize() {
            const filterHeight = this.$el.offsetHeight
            const windowHeight = document.documentElement.clientHeight

            if (filterHeight < windowHeight - 30)
                this.isSticky = true
            else
                this.isSticky = false
        }
    },
    watch: {
        filterInput: {
            deep: true,
            handler() {
                this.updateModelValue()
            }
        },
        isBodyHiddenComputed() {
            this.setFilterBodyHeight()
        }
    },
    mounted() {
        this.setMatchMedia()
        for (let key in this.modelValue) {
            this.filterInput[key] = this.modelValue[key]
        }
        this.onResize()
        window.addEventListener('resize', this.onResize)
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.onResize)
    }
}
</script>

<style lang="scss">
.filter {
    &.__sticky {
        position: sticky;
        top: 30px;
    }

    &::before {
        transform: translateY(26px) scale(0.96);
    }

    &__container {
        color: #353535;
        padding: 0 0 75px 0;
    }

    &__header {
        padding: 25px 20px;
        display: flex;

        svg {
            width: 28px;
            height: 28px;
            color: #251C41;
        }
    }

    &__header-text {
        display: inline-block;
        margin-right: 45px;
        font-size: 20px;
        line-height: 24px;
        font-weight: 500;
    }

    &__section {
        padding: 30px 25px;
        border-top: 1px solid #e6e6e6;
    }

    &__section-title {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 20px;
    }

    &__section-body {

        .checkbox,
        .radio {
            font-size: 14px;
            line-height: 16px;
            margin-bottom: 20px;

            &:last-child {
                margin-bottom: 0;
            }
        }
    }

    &__apply {
        padding: 0 20px;
        margin-top: 25px;

        .button {
            width: 100%;
            font-size: 16px;
            line-height: 19px;
            border-width: 2px;
            border-color: var(--theme_color);

            &:hover {
                background-color: var(--theme_color);
            }
        }
    }

    @media (max-width: 919px) {
        &.__sticky {
            position: relative;
            top: 0;
        }

        &__container {
            padding: 0 0 45px 0;
        }

        &__header {
            padding: 20px 20px 15px 20px;
            justify-content: center;
        }

        &__section {
            padding: 15px 20px;
        }
    }
}

.filter--body-hidden {
    &::before {
        transform: translateY(8px) scale(0.96);
    }

    .filter {
        &__container {
            padding-bottom: 0;
        }

        &__body {
            overflow: hidden;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
}
</style>