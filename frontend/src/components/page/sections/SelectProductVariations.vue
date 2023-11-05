<template>
    <div class="select-variations">
        <div class="select-variations__item" v-for="(obj, objIndex) in variations" :key="obj.variation.id">
            <h6 class="select-variations__title">
                {{ obj.variation.name }}
            </h6>
            <ul class="select-variations__list">
                <li class="select-variations__list-item" v-for="(varValue, valueIndex) in obj.values" :key="varValue.id">
                    <RadioLabel v-model="values[objIndex].value" ref="variationRadio" :name="obj.variation.name"
                        :checked="valueIndex === 0" :value="varValue.value">
                        {{ varValue.value }}
                    </RadioLabel>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SelectProductVariations',
    props: {
        variations: {
            type: Array,
            required: true
        },
        modelValue: Object
    },
    emits: ['update:modelValue'],
    data() {
        return {
            values: this.variations.map(obj => {
                return {
                    name: obj.variation.name,
                    value: obj.values[0] ? obj.values[0].value : ''
                }
            })
        }
    },
    watch: {
        values: {
            deep: true,
            handler() {
                this.$emit('update:modelValue', this.values)
            }
        }
    }
}
</script>

<style></style>