<template>
    <div class="admin-page__creation">
        <LoadingScreen v-if="isLoading"></LoadingScreen>
        <div class="inputs-flex">
            <TextInputWrapper name="name" id="name" placeholder="Название товара" v-model="input.name">
                <template v-slot:label>Название товара</template>
                <template v-if="errors.name">
                    {{ errors.name }}
                </template>
            </TextInputWrapper>
        </div>
        <div class="inputs-flex">
            <TextInputWrapper name="price" id="price" width="160px" placeholder="Цена" numberonly modifiers="toLocaleString"
                max="1000000" v-model="input.price">
                <template v-slot:label>Цена</template>
                <template v-if="errors.price">
                    {{ errors.price }}
                </template>
            </TextInputWrapper>
            <TextInputWrapper name="discount_price" id="discount_price" width="160px" placeholder="Цена по скидке"
                numberonly modifiers="toLocaleString" max="1000000" v-model="input.discount_price">
                <template v-slot:label>Цена по скидке</template>
                <template v-if="errors.discount_price">
                    {{ errors.discount_price }}
                </template>
            </TextInputWrapper>
        </div>
        <div class="inputs-flex">
            <ValueSelect v-model="input.taxonomies.brand" name="brand" :values="taxonomies.brands">
                <template v-slot:label>
                    Бренд
                </template>
            </ValueSelect>
            <ValueSelect v-model="input.taxonomies.category" name="category" :values="taxonomies.categories">
                <template v-slot:label>
                    Категория
                </template>
            </ValueSelect>
            <ValueSelect v-model="input.taxonomies.type" name="type" :values="taxonomies.types">
                <template v-slot:label>
                    Тип
                </template>
            </ValueSelect>
        </div>
        <div class="admin-page__creation-table">
            <AdminTable :headers="['Характеристика', 'Значение']" v-model="input.info"></AdminTable>
        </div>
        <div class="admin-page__creation-table">
            <AdminTable multivalues :headers="['Вариация', 'Значения']" v-model="input.variations"></AdminTable>
        </div>
        <div class="admin-page__creation-image">
            <ImageLoad v-model="input.image.path" v-model:id="input.image.id">
                <template v-slot:title>
                    Главное изображение
                </template>
            </ImageLoad>
        </div>
        <div class="admin-page__creation-image admin-page__creation-image--gallery">
            <ImagesGallery v-model="input.images"></ImagesGallery>
        </div>
        <TransitionGroup v-if="errorsList.length > 0" name="grow">
            <div v-for="(errorMessage, i) in errorsList" :key="i" class="error admin-page__error">
                {{ errorMessage }}
            </div>
        </TransitionGroup>
        <div class="admin-page__creation-save">
            <button class="button button--colored" type="submit" @click.prevent="saveProduct">
                {{ productData ? 'Сохранить изменения' : 'Создать товар' }}
            </button>
        </div>
    </div>
</template>

<script>
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'
import ValueSelect from '@/components/inputs/ValueSelect.vue'
import AdminTable from '@/components/tables/AdminTable.vue'
import ImageLoad from '@/components/inputs/images/ImageLoad.vue'
import ImagesGallery from '@/components/inputs/images/ImagesGallery.vue'
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import axios from 'axios'

export default {
    name: 'ProductCreate',
    components: {
        TextInputWrapper,
        ValueSelect,
        AdminTable,
        ImageLoad,
        ImagesGallery,
        LoadingScreen
    },
    data() {
        return {
            isRedirected: false,
            isLoading: false,
            productData: null,
            errors: {},
            error: '',
            taxonomies: {
                brands: [],
                categories: [],
                types: []
            },
            input: {
                name: '',
                price: 0,
                discount_price: 0,
                taxonomies: {
                    brand: '',
                    category: '',
                    type: ''
                },
                info: [],
                variations: [],
                image: {
                    path: '',
                    id: 0
                },
                images: []
            },
        }
    },
    computed: {
        errorsList() {
            const array = []
            if (this.error)
                array.push(this.error)

            for (let key in this.errors) {
                array.push(this.errors[key][0])
            }

            return array
        }
    },
    methods: {
        async loadTaxonomies() {
            try {
                const res = await axios.get(import.meta.env.VITE_TAXONOMIES_GET_LINK)
                for (let key in this.taxonomies) {
                    if (!Array.isArray(res.data[key]))
                        continue

                    this.taxonomies[key] = res.data[key].map(obj => obj.name)
                }
            } catch (err) { }
        },
        async loadProductData() {
            const productId = this.$route.params.productId
            if (!productId)
                return

            this.isLoading = true

            try {
                const link = `${import.meta.env.VITE_PRODUCT_GET_LINK}${productId}`
                const res = await axios(link)
                if (res.data.id) {
                    this.productData = res.data
                }
            } catch (err) {
                const data = err.response.data
                if (data.error)
                    this.error = data.error

                else if (data.errors)
                    this.errors = data.errors
            }

            this.isLoading = false
        },
        onLoadedProductData() {
            if (!this.productData)
                return

            this.input.name = this.productData.name
            this.input.price = this.productData.price
            this.input.discount_price = this.productData.discount_price
            this.input.taxonomies.brand = this.productData.brand
            this.input.taxonomies.category = this.productData.category
            this.input.taxonomies.type = this.productData.type
            this.input.info = this.productData.info
                .map(obj => {
                    return {
                        name: obj.name,
                        values: [obj.value]
                    }
                })
            this.input.variations = this.productData.variations
                .map(obj => {
                    return {
                        name: obj.variation.name,
                        values: obj.values.map(o => o.value)
                    }
                })
            this.input.image = {
                path: this.productData.image_path,
                id: this.productData.image_id
            }
            this.input.images = this.productData.images
                .map(obj => {
                    return {
                        id: obj.image_id,
                        path: obj.path
                    }
                })
        },
        async saveProduct() {
            this.nullifyErrors()
            this.isLoading = true

            const data = {
                name: this.input.name,
                price: this.input.price,
                discount_price: this.input.discount_price,
                brand: this.input.taxonomies.brand,
                category: this.input.taxonomies.category,
                type: this.input.taxonomies.type,
                image_id: this.input.image.id,
                images: this.input.images.map(obj => obj.id),
                info: this.input.info.map(obj => {
                    return { name: obj.name, value: obj.values[0] }
                }),
                variations: this.input.variations.map(obj => {
                    return { name: obj.name, values: obj.values }
                })
            }

            const link = this.productData
                ? `${import.meta.env.VITE_PRODUCT_UPDATE_LINK}${this.productData.id}`
                : import.meta.env.VITE_PRODUCT_CREATE_LINK
            try {
                const res = await axios.post(link, data)
                if (res.data.product) {
                    this.productData = res.data.product
                    this.$router.push({ name: 'ProductUpdate', params: { productId: this.productData.id } })
                }
            } catch (err) {
                const data = err.response.data
                if (data.error)
                    this.error = data.error
                else if (data.errors)
                    this.errors = data.errors
            }

            this.isLoading = false
        },
        nullifyErrors() {
            this.errors = []
            this.error = ''
        }
    },
    watch: {
        productData: {
            deep: true,
            handler() {
                this.onLoadedProductData()
            }
        }
    },
    mounted() {
        this.loadTaxonomies()
        this.loadProductData()
    }
}
</script>

<style></style>