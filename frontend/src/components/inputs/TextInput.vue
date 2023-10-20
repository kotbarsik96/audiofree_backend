<template>
    <input class="text-input__input" v-model="value" :placeholder="placeholder" :type="type" :name="name" :id="id" :autocomplete="autocomplete"
        :maxlength="maxlength" ref="input" @focus="onFocus" @blur="onBlur" @input="onInput" @keyup.enter="onEnterKeyup">
</template>

<script>
import { nextTick } from 'vue'

export default {
    name: 'TextInput',
    props: {
        name: {
            type: String,
            required: true
        },
        modelValue: String,
        placeholder: String,
        type: {
            type: String,
            default: 'text'
        },
        id: String,
        autocomplete: [String, Boolean],
        /* e.g.: a,g,\+,3, не забывать экранировать спецсимволы: \-, \+ */
        allowSymbols: {
            type: String,
            validator(value) {
                value.match(/^(.,)+.?$/)
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
        modifiers: [String, Array]
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

            let regexpStr = ''
            if (this.numberonly)
                regexpStr += '0-9'

            if (this.allowSymbols)
                regexpStr += this.allowSymbols

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
        onEnterKeyup(event) {
            event.preventDefault()
            event.stopPropagation()
        },
        onInput(event) {
            const input = event.target

            if (this.maxlength) {
                const value = input.value.replace(/\D/g, '')
                if (this.numberonly && parseInt(value) > parseInt(this.max))
                    input.value = this.max
            }

            this.doScopeSymbols(input)
            this.doApplyModifiers(input)

            this.$emit('input')
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
        }
    }
}
</script>

<style></style>