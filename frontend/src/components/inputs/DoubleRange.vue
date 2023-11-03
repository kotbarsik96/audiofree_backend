<template>
    <div class="double-range range">
        <div class="range__input">
            <TextInputWrapper v-model="input.min" :name="`${name}_range_min`" width="95" :placeholder="placeholderMin"
                numberonly @change="onInputChange">
            </TextInputWrapper>
            <span class="double-range__delimeter"></span>
            <TextInputWrapper v-model="input.max" :name="`${name}_range_max`" width="95" :placeholder="placeholderMax"
                numberonly @change="onInputChange">
            </TextInputWrapper>
        </div>
        <div class="range__scale" ref="scale" @pointerdown.prevent="onScalePointerdown">
            <div class="range__toggler range__toggler--min" :style="{ left: `${left.min}px` }" ref="togglerMin"></div>
            <div class="range__bar" :style="{ width: `${barWidth}px`, left: `${barLeft}px` }"></div>
            <div class="range__toggler range__toggler--max" :style="{ left: `${left.max}px` }"></div>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import { getCoords, isNumeric } from '@/assets/js/scripts.js'
import { nextTick } from 'vue'

export default {
    name: 'DoubleRange',
    components: {
        TextInputWrapper
    },
    props: {
        modelValueMin: Number,
        modelValueMax: Number,
        placeholderMin: String,
        placeholderMax: String,
        minValue: {
            type: Number,
            default: 0
        },
        name: {
            type: String,
            required: true
        },
        maxValue: {
            type: Number,
            required: true
        }
    },
    emits: ['update:modelValueMin', 'update:modelValueMax'],
    data() {
        return {
            input: {
                min: '',
                max: ''
            },
            left: {
                min: 0,
                max: 0,
            },
            scaleCoords: {},
            togglerWidth: 0,
            resizeTimeout: null
        }
    },
    computed: {
        totalValue() {
            return this.maxValue - this.minValue
        },
        totalScaleWidth() {
            return Math.round(
                this.scaleCoords.right - this.scaleCoords.left - this.togglerWidth
            )
        },
        onePercentOfScale() {
            return this.totalScaleWidth / 100
        },
        barWidth() {
            const max = isNumeric(this.left.max) ? this.left.max : 0
            const min = isNumeric(this.left.min) ? this.left.min : 0

            return Math.round(max - min)
        },
        barLeft() {
            const min = isNumeric(this.left.min) ? this.left.min : 0
            return min + (this.togglerWidth / 2)
        }
    },
    methods: {
        onResize() {
            if (this.resizeTimeout)
                clearTimeout(this.resizeTimeout)

            this.resizeTimeout = setTimeout(() => {
                this.togglerWidth = this.$refs.togglerMin.offsetWidth
                this.scaleCoords = getCoords(this.$refs.scale)
                this.onInputChange()
                this.resizeTimeout = null
            }, 500)
        },
        onInputChange() {
            if (!this.input.min || this.input.min < this.minValue)
                this.input.min = this.minValue

            if (!this.input.max || this.input.max > this.maxValue)
                this.input.max = this.maxValue

            if (this.input.min && this.input.max && this.input.max < this.input.min)
                this.input.max = this.input.min

            if (!this.totalScaleWidth) {
                setTimeout(this.onInputChange, 100)
                return
            }

            if (isNumeric(this.input.min))
                this.left.min = Math.round(
                    this.onePercentOfScale * ((this.input.min - this.minValue) / (this.totalValue / 100))
                )
            else
                this.left.min = 0

            if (isNumeric(this.input.max))
                this.left.max = Math.round(this.onePercentOfScale * ((this.input.max - this.minValue) / (this.totalValue / 100)))
            else
                this.left.max = Math.round(this.onePercentOfScale * ((this.maxValue - this.minValue) / (this.totalValue / 100)))
        },
        onScalePointerdown(event) {
            event.onDragstart = () => null

            const xRelative = event.clientX - this.scaleCoords.left
            const toTogglerMin = Math.abs(xRelative - this.left.min)
            const toTogglerMax = Math.abs(xRelative - this.left.max)
            let closestToggler = 'min'
            if (toTogglerMin > toTogglerMax)
                closestToggler = 'max'
            else if (toTogglerMin === toTogglerMax) 
                closestToggler = xRelative < toTogglerMin ? 'min' : 'max'

            onMove = onMove.bind(this)
            onUp = onUp.bind(this)

            document.addEventListener('pointermove', onMove)
            document.addEventListener('pointerup', onUp)
            onMove({ clientX: event.clientX })

            function onMove(moveEvent) {
                const moveX = moveEvent.clientX
                const moveXRelative = moveX - this.scaleCoords.left

                if (closestToggler === 'min' && moveXRelative > this.left.max)
                    this.input.min = this.input.max
                else if (closestToggler === 'max' && moveXRelative < this.left.min)
                    this.input.max = this.input.min
                else if (moveX < this.scaleCoords.left)
                    this.input[closestToggler] = this.minValue
                else if (moveXRelative - this.togglerWidth > this.totalScaleWidth)
                    this.input[closestToggler] = this.maxValue
                else {
                    const percent = moveXRelative / (this.totalScaleWidth / 100)
                    this.input[closestToggler] = this.totalValue / 100 * percent
                }

                this.onInputChange()
            }
            function onUp() {
                document.removeEventListener('pointermove', onMove)
                document.removeEventListener('pointerup', onUp)
            }
        },
        setDefaults(){
            this.input.min = this.minValue
            this.input.max = this.maxValue
            this.onInputChange()
        }
    },
    watch: {
        input: {
            deep: true,
            handler() {
                const min = parseInt(this.input.min)
                const max = parseInt(this.input.max)

                if (!isNaN(min)) {
                    this.input.min = min
                    this.$emit('update:modelValueMin', this.input.min)
                } else 
                    this.$emit('update:modelValueMin', this.minValue)

                if (!isNaN(max)) {
                    this.input.max = max
                    this.$emit('update:modelValueMax', this.input.max)
                } else 
                    this.$emit('update:modelValueMax', this.maxValue)
            }
        },
        minValue(){
            this.input.min = this.minValue
            this.onInputChange()
        },
        maxValue() {
            this.input.max = this.maxValue
            this.onInputChange()
        },
    },
    mounted() {
        nextTick().then(() => this.onInputChange())
        window.addEventListener('resize', this.onResize)
        this.onResize()
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.onResize)
    }
}
</script>

<style></style>