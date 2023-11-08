<template>
    <input class="text-input__input" v-model="value" :placeholder="placeholder" :type="type" :name="name" :id="id"
        :autocomplete="autocomplete" :maxlength="maxlength" ref="input" @focus="onFocus" @blur="onBlur" @input="onInput"
        @keyup.enter="onEnterKeyup">
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
        /* маска, в которую будет преобразовываться введенное значение. Зарезервированные маски:
            1. phone: '+7 (...) ... - .. - ..'
        Зарезервированные маски работают корректнее, т.к. позволяют настроить getUnmaskedValue() правильно
        Например: '+7 (...) ... - .. - ..', где все символы точки заменятся на значения, взятые из input.value. Вместо точки можно указать другой символ/символы, тогда указать соответствующий maskSymbol */
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

            let allowSymbols = this.allowSymbols ?
                this.allowSymbols.replace('+', '\\+')
                    .replace('-', '\\-')
                    .replace('(', '\\(')
                    .replace(')', '\\)')
                    .replace('.', '\\.')
                : null
            if (this.mask) {
                switch (this.mask) {
                    case 'phone':
                        allowSymbols = '\\+,\\(,\\), ,\\-'
                        break
                }
            }

            let regexpStr = ''
            if (allowSymbols)
                regexpStr += allowSymbols
            if (this.numberonly)
                regexpStr += '0-9'

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
        },
        maskString() {
            switch (this.mask) {
                case 'phone':
                    return '+7 (...) ... - .. - ..'
                default:
                    return this.mask
            }
        }
    },
    methods: {
        focus() {
            this.$refs.input.focus()
        },
        onInput() {
            if (this.maxlength) {
                const value = this.value.toString().replace(/\D/g, '')
                if (this.numberonly && parseInt(value) > parseInt(this.max))
                    this.value = this.max
            }

            this.doScopeSymbols()
            this.doApplyModifiers()
            this.doApplyMask(event)
        },
        doScopeSymbols() {
            if (!this.scopeSymbolsRegexp)
                return

            nextTick().then(() => this.value = this.value.toString().replace(this.scopeSymbolsRegexp, ''))
        },
        doApplyModifiers() {
            if (!this.modifiers)
                return

            if (this.modifiers.length < 0)
                return

            const array = Array.isArray(this.modifiers)
                ? this.modifiers
                : this.modifiers.split('|')

            array.forEach(modifier => {
                let value = this.value
                const method = value[modifier]
                if (typeof method !== 'function')
                    return

                if (modifier === 'toLocaleString') {
                    value = parseInt(value.toString().replace(/\s/g, ''))
                    if (isNaN(value))
                        return
                }

                // без nextTick input.value как будто откатывается назад на один шаг
                nextTick().then(() => this.value = value[modifier]())
            })
        },
        doApplyMask(event) {
            if (event && event.inputType && event.inputType.match(/delete/))
                return
            if (!this.maskString)
                return

            let modifiedValue = this.maskString
            let clearValue = this.getUnmaskedValue()

            clearValue.split('')
                .forEach(substr => modifiedValue = modifiedValue.toString().replace(this.maskSymbol, substr))
            if (modifiedValue.includes(this.maskSymbol)) {
                const index = modifiedValue.indexOf(this.maskSymbol)
                modifiedValue = modifiedValue.slice(0, index)
            }

            this.value = modifiedValue
        },
        getUnmaskedValue() {
            if (!this.maskString)
                return this.value

            switch (this.mask) {
                case 'phone':
                    return this.value.toString().replace(/(\+7)|\(|\)|\-|\s/g, '')
                default:
                    if (this.value.length >= this.maskString.indexOf(this.maskSymbol)) {
                        return this.value.split('')
                            .filter((s, index) => this.maskString[index] === this.maskSymbol)
                            .join('')
                    }
                    else return this.value
            }
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
            if (this.modelValue) {
                this.value = this.modelValue
                this.onInput()
            }
        }
    },
}
</script>