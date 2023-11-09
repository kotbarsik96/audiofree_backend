<template>
    <div class="admin-page__creation">
        <LoadingScreen v-if="isLoading"></LoadingScreen>
        <RouterLink class="admin-page__to-result link" v-if="this.productData"
            :to="{ name: 'Product', params: { productId: this.productData.id } }">
            <ChevronIcon></ChevronIcon>
            На страницу товара
        </RouterLink>
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
            <TextInputWrapper name="quantity" id="quantity" width="160px" placeholder="Количество" numberonly
                modifiers="toLocaleString" max="100000" v-model="input.quantity">
                <template v-slot:label>Количество</template>
                <template v-if="errors.quantity">
                    {{ errors.quantity }}
                </template>
            </TextInputWrapper>
        </div>
        <div class="inputs-flex">
            <ValueSelect v-model="input.taxonomies.product_status" name="product_status"
                :values="taxonomies.product_statuses">
                <template v-slot:label>
                    Статус
                </template>
            </ValueSelect>
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
        <div class="admin-page__creation-block" @keyup="onDescriptionInput">
            <h3 class="admin-page__title">
                Описание товара
            </h3>
            <div class="admin-page__creation-editor" id="product-creation-editor"></div>
            <Transition name="fade-in">
                <div class="admin-page__creation-editor-unsaved" v-if="isDescriptionUnsaved">
                    Изменения не сохранены
                </div>
            </Transition>
        </div>
        <div class="admin-page__creation-image">
            <ImageLoad v-model="input.image.path" v-model:id="input.image.id">
                <template v-slot:title>
                    Главное изображение (рекомендуется без фона)
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
        <div class="admin-page__creation-buttons">
            <button class="button button--colored" type="submit" @click.prevent="saveProduct">
                {{ productData ? 'Сохранить изменения' : 'Создать товар' }}
            </button>
            <button class="button" v-if="productData" type="button" @click="deleteProduct">
                Удалить товар
            </button>
        </div>
        <Transition name="grow">
            <div class="error" v-if="error">
                {{ error }}
            </div>
        </Transition>
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
import { getNumber, handleAjaxError } from '@/assets/js/scripts.js'
import { useIndexStore } from '@/stores/'
import { useNotificationsStore } from '@/stores/notifications.js'
import { useModalsStore } from '@/stores/modals.js'
import ConfirmModal from '@/components/modals/ConfirmModal.vue'
import { h } from 'vue'
import EditorJS from '@editorjs/editorjs'

export default {
    name: 'ProductCreate',
    emits: ['updateRouteKey'],
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
                types: [],
                product_statuses: []
            },
            input: {
                name: '',
                price: 0,
                discount_price: 0,
                quantity: '',
                taxonomies: {
                    brand: '',
                    category: '',
                    type: '',
                    product_status: ''
                },
                info: [],
                variations: [],
                description: {},
                image: {
                    path: '',
                    id: 0
                },
                images: [],
                imageHomepage: {
                    path: '',
                    id: 0,
                    tag: 'homepage'
                }
            },
            descriptionEditor: null,
            descriptionTimeout: null,
            isDescriptionUnsaved: false
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
        onKeydown(event) {
            if (event.code === 'KeyS' && event.ctrlKey) {
                event.preventDefault()
                this.saveProduct()
            }
        },
        async loadTaxonomies() {
            useIndexStore().loadTaxonomies(this.taxonomies, true)
        },
        async loadProductData() {
            const productId = this.$route.params.productId
            if (!productId) {
                this.descriptionEditor = new EditorJS({
                    holder: 'product-creation-editor',
                    placeholder: 'Введите описание товара',
                    minHeight: 0,
                })
                return
            }

            this.isLoading = true

            try {
                const link = `${import.meta.env.VITE_PRODUCT_GET_LINK}${productId}`
                const res = await axios(link)
                if (res.data.id) {
                    this.productData = res.data
                } else {
                    this.$router.push({ name: 'ProductCreate' })
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
            this.input.quantity = this.productData.quantity
            this.input.taxonomies.product_status = this.productData.product_status
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
            this.input.description = JSON.parse(this.productData.description)
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

            if (!this.descriptionEditor) {
                this.descriptionEditor = new EditorJS({
                    holder: 'product-creation-editor',
                    placeholder: 'Введите описание товара',
                    minHeight: 0,
                    data: this.input.description
                })
            }
        },
        async saveProduct() {
            this.nullifyErrors()
            this.isLoading = true

            this.input.description = await this.descriptionEditor.save()
            const data = {
                name: this.input.name,
                price: getNumber(this.input.price),
                discount_price: getNumber(this.input.discount_price),
                quantity: this.input.quantity,
                product_status: this.input.taxonomies.product_status,
                brand: this.input.taxonomies.brand,
                category: this.input.taxonomies.category,
                type: this.input.taxonomies.type,
                description: JSON.stringify(this.input.description),
                image_id: this.input.image.id,
                images: [...this.input.images.map(obj => obj.id), this.input.imageHomepage]
                    .filter(i => i),
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
                this.isDescriptionUnsaved = false
            } catch (err) {
                const data = err.response.data
                if (data.error)
                    this.error = data.error
                else if (data.errors)
                    this.errors = data.errors
            }

            this.isLoading = false
        },
        deleteProduct() {
            if (!this.productData) {
                useNotificationsStore().addNotification({
                    message: 'Ошибка'
                })
                return
            }

            const callback = async () => {
                const link = `${import.meta.env.VITE_PRODUCT_DELETE_LINK}${this.productData.id}`
                try {
                    const res = await axios.delete(link)
                    if (res.data.success) {
                        if (res.data.message) {
                            useNotificationsStore().addNotification({
                                message: res.data.message
                            })
                        }

                        this.$router.push({ name: 'ProductCreate' })
                    } else {
                        throw new Error()
                    }
                } catch (err) {
                    handleAjaxError(err, this)
                }
            }

            const component = h(ConfirmModal, {
                title: `Удалить товар ${this.productData.name}?`,
                confirmProps: {
                    text: 'Удалить',
                    callback
                },
                declineProps: {
                    text: 'Не удалять'
                }
            })
            useModalsStore().addModal({ component })
        },
        nullifyErrors() {
            this.errors = []
            this.error = ''
        },
        onDescriptionInput() {
            if (!this.$route.params.productId)
                return

            if (this.descriptionTimeout)
                clearTimeout(this.descriptionTimeout)

            this.descriptionTimeout = setTimeout(async () => {
                const savedObj = await this.descriptionEditor.save()
                const unsaved = JSON.stringify(savedObj.blocks.map(o => o.data))
                const saved = JSON.stringify(this.input.description.blocks.map(o => o.data))

                if (unsaved !== saved)
                    this.isDescriptionUnsaved = true
                else
                    this.isDescriptionUnsaved = false
                this.descriptionTimeout = null
            }, 500);
        }
    },
    watch: {
        productData: {
            deep: true,
            handler() {
                this.onLoadedProductData()
            }
        },
        $route() {
            this.$emit('updateRouteKey')
        }
    },
    created() {
        document.addEventListener('keydown', this.onKeydown)
    },
    mounted() {
        this.loadTaxonomies()
        this.loadProductData()
    },
    beforeUnmount() {
        document.removeEventListener('keydown', this.onKeydown)
    }
}
</script>