<template>
    <TextInputWrapper class="text-input--round" v-model="value" :placeholder="placeholder" :type="type" :name="name"
        :id="id" :results="results" containerClass="search-input" wrapperClass="search-input__wrapper" inputClass="search-input__input"
        @update:modelValue="onInput" ref="rootComp">
        <template v-slot:icon>
            <SearchIcon @click="focus"></SearchIcon>
        </template>
        <template v-slot:results v-if="results.length > 0">
            <li class="text-input__result-item" v-for="obj of results">
                <RouterLink :to="{ name: 'Product', params: { productId: obj.id } }">
                    <ImagePicture class="text-input__result-item-image" :obj="obj"></ImagePicture>
                </RouterLink>
                <div class="text-input__result-item-container">
                    <RouterLink class="link text-input__result-item-link"
                        :to="{ name: 'Product', params: { productId: obj.id } }">
                        {{ obj.name }}
                    </RouterLink>
                    <StarRating :stars="5" :rating="obj.rating_value"></StarRating>
                    <RouterLink class="link text-input__result-item-link"
                        :to="{ name: 'Product', params: { productId: obj.id } }">
                        {{ obj.current_price.toLocaleString() }} ₽
                    </RouterLink>
                </div>
            </li>
        </template>
    </TextInputWrapper>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import SearchIcon from '@/components/icons/SearchIcon.vue'
import axios from 'axios'
import StarRating from '@/components/misc/StarRating.vue'

export default {
    name: 'HeaderSearchInput',
    components: {
        TextInputWrapper,
        SearchIcon,
        StarRating
    },
    emits: ['update:modelValue'],
    props: {
        placeholder: String,
        type: {
            type: String,
            default: 'text'
        },
        name: {
            type: String,
            required: true
        },
        id: String
    },
    data() {
        return {
            value: '',
            lastInputTimeout: null,
            results: []
        }
    },
    methods: {
        focus() {
            this.$refs.rootComp.focus()
        },
        onInput() {
            if (this.lastInputTimeout) {
                clearTimeout(this.lastInputTimeout)
                if(!this.value.trim()) {
                    this.results = []
                    return
                }
            }

            this.lastInputTimeout = setTimeout(this.loadProducts, 1500);
        },
        async loadProducts() {
            const query = this.value.trim()
            if(!query) {
                this.results = []
                return
            }

            try {
                const res = await axios.get(import.meta.env.VITE_PRODUCTS_GET_LINK, {
                    params: {
                        nameOrBrand: query
                    }
                })
                if(Array.isArray(res.data.result))
                    this.results = res.data.result
            } catch (err) { }
        }
    }
}
</script>