<template>
    <input class="text-input__input" v-model="value" :placeholder="placeholder" :type="type" :name="name" :id="id"
        :autocomplete="autocomplete" :maxlength="maxlength" ref="input" @focus="onFocus" @blur="onBlur" @input="onInput"
        @keydown="onKeydown" @keyup.enter="onEnterKeyup">
</template>

<script>
import { nextTick } from 'vue'

export default {
    name: 'TextInput',
    emits: ['update:modelValue'],
    props: {
        name: {
            type: String,
            required: true
        },
        modelValue: [String, Number],
        placeholder: String,
        type: {
            type: String,
            default: 'text'
        },
        id: String,
        autocomplete: [String, Boolean],
        /* e.g.: a,g,+,3, спецсимволы можно не экранировать, это делается в scopeSymbolsRegexp */
        allowSymbols: {
            type: String,
            validator(value) {
                return value.match(/^(.,)+.?$/)
            }
        },
        numberonly: Boolean,
        /* e.g.: gi */
        regexpFlags: {
            type: String,
            validator(valueRaw) {
                const flags = ['g', 'i', 'm', 's', 'u', 'y']
                const value = valueRaw.split('')
                    .filter(s => flags.includes(s))
                    .join('')

                return valueRaw.length === value.length
            }
        },
        // если numberonly, max === максимальное число; иначе === максимальная длина строки
        max: [Number, String],
        /* представляют собой методы String, например, toLocaleString. Можно передать массив, можно передать строку вида "toLocaleString|trim" */
        modifiers: [String, Array],
        /* маска, в которую будет преобразовываться введенное значение. Например: '+7 (...) ... - .. - ..', где все символы точки заменятся на значения, взятые из input.value. Вместо точки можно указать другой символ/символы, тогда указать соответствующий maskSymbol */
        mask: String,
        maskSymbol: {
            type: String,
            default: '.'
        }
    },
    data() {
        return {
            value: ''
        }
    },
    computed: {
        scopeSymbolsRegexp() {
            if (!this.numberonly && !this.allowSymbols)
                return null

            const allowSymbolsShielded = this.allowSymbols
                ? this.allowSymbols
                    .replace('+', '\\+')
                    .replace('-', '\\-')
                    .replace('(', '\\(')
                    .replace(')', '\\)')
                : null
            let regexpStr = ''
            if (this.numberonly)
                regexpStr += '0-9'

            if (allowSymbolsShielded)
                regexpStr += allowSymbolsShielded


            if (regexpStr.length > 0)
                regexpStr = `[^${regexpStr}]`

            if (this.regexpFlags)
                return new RegExp(regexpStr, this.regexpFlags)

            return new RegExp(regexpStr)
        },
        maxlength() {
            if (!this.max)
                return null

            if (this.numberonly) {
                const length = this.max.toString().length
                return length + Math.floor(length / 3)
            }
            else
                return this.max
        }
    },
    methods: {
        focus() {
            this.$refs.input.focus()
        },
        // onKeydown(event) {
        //     if (event.key.match(/delete|backspace/i))  
        //         event.target.value = ''
        // },
        onEnterKeyup(event) {
            event.preventDefault()
            event.stopPropagation()
        },
        onInput(event) {
            if (event.inputType.match(/delete/))
                return

            const input = event.target

            if (this.maxlength) {
                const value = input.value.replace(/\D/g, '')
                if (this.numberonly && parseInt(value) > parseInt(this.max))
                    input.value = this.max
            }

            this.doScopeSymbols(input)
            this.doApplyModifiers(input)
            this.doApplyMask(input)
        },
        doScopeSymbols(input = this.$refs.input) {
            if (!this.scopeSymbolsRegexp)
                return

            nextTick().then(() => input.value = input.value.replace(this.scopeSymbolsRegexp, ''))
        },
        doApplyModifiers(input = this.$refs.input) {
            if (!this.modifiers)
                return

            if (this.modifiers.length < 0)
                return

            const array = Array.isArray(this.modifiers)
                ? this.modifiers
                : this.modifiers.split('|')

            array.forEach(modifier => {
                let value = input.value
                const method = value[modifier]
                if (typeof method !== 'function')
                    return

                if (modifier === 'toLocaleString') {
                    value = parseInt(value.replace(/\s/g, ''))
                    if (isNaN(value))
                        return
                }

                // без nextTick input.value как будто откатывается назад на один шаг
                nextTick().then(() => input.value = value[modifier]())
            })
        },
        doApplyMask(input = this.$refs.input) {
            if (!this.mask)
                return

            let modifiedValue = this.mask
            let clearValue = input.value
            if (clearValue.length >= this.mask.indexOf(this.maskSymbol)) {
                clearValue = clearValue.split('')
                    .filter((s, index) => this.mask[index] === this.maskSymbol)
                    .join('')
            }

            clearValue.split('')
                .forEach(substr => modifiedValue = modifiedValue.replace(this.maskSymbol, substr))
            if (modifiedValue.includes(this.maskSymbol)) {
                const index = modifiedValue.indexOf(this.maskSymbol)
                modifiedValue = modifiedValue.slice(0, index)
            }

            input.value = modifiedValue
        }
    },
    watch: {
        value() {
            let value = this.value || ''
            if (this.numberonly && !this.scopeSymbolsRegexp)
                value = parseInt(value.toString().replace(/\D/g, '')) || 0
            this.$emit('update:modelValue', value)
        },
        modelValue() {
            if (this.modelValue)
                this.value = this.modelValue
        }
    }
}
</script>

<style></style>