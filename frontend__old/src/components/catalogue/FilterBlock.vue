<template>
    <div class="card filter">
        <div class="card__container card__side filter__container">
            <div class="filter__title" ref="filterTitle">
                <div class="filter__title-text">{{ filterBlockTitle }}</div>
                <div class="filter__title-icon __icon-filter"></div>
            </div>
            <div class="filter__main" ref="filterBody">
                <!-- <div class="filter-block" v-for="field in fields" :key="field.key">
                    <div class="filter-block__title">{{ field.title }}:</div>

                    <div class="filter-block__list" v-if="field.inputsType === 'checkbox' || field.inputsType === 'radio'"
                        ref="checktypeFilterBlock">
                        <label class="checkcircle" v-for="val in keyValues[field.key]" :key="val">
                            <input :type="field.inputsType" :name="field.key" ref="checktypeInputs" :value="val" />
                            <div class="checkcircle__option">
                                <span></span>
                                {{ capitalLetter(val) }}
                            </div>
                        </label>
                    </div>

                    <RangeBlock class="filter-block__price-range" v-if="field.inputsType === 'range'"
                        :valueString="field.rangeValueString" :minValueThreshold="0"
                        :maxValueThreshold="field.maxRangeValue" ref="rangeBlock" :filterName="field.key"></RangeBlock>
                </div> -->
                <div class="filter__buttons">
                    <button class="button filter__clear-button" @click="applyFilter">Применить фильтр</button>
                    <button class="button filter__clear-button" @click="clearFilter">Очистить фильтр</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {
    SpoilerElem,
    capitalLetter,
    getKeyPathValue,
} from "@/assets/js/scripts";
import RangeBlock from "@/components/catalogue/RangeBlock.vue";

export default {
    name: "FilterBlock",
    emits: ["filterApply"],
    components: {
        RangeBlock,
    },
    props: {
        filterBlockTitle: {
            type: String,
            default: "Фильтр",
        },
    },
    computed: {
        
    },
    data() {
        return {
            filtered: []
        }
    },
    methods: {
        capitalLetter,
        clearFilter() {
            
        },
        applyFilter() {
            
        },
    },
    mounted() {
        this.applyFilter();
        new SpoilerElem(this.$refs.filterTitle, this.$refs.filterBody, 949);
    },
};
</script>