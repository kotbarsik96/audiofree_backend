<!-- компонент-обертка над ImagesGallery, отображает всю галерею, кроме "запрещенных" подпапок (например, изображений, у которых отдельная галерея) -->

<template>
    <div>
        <LoadingScreen v-if="isLoading"></LoadingScreen>
        <div class="filtering">
            <div class="inputs-flex">
                <ValueSelect name="gallery-uploader-email" :values="galleryUploaders" id="gallery-uploader-email"
                    v-model="filters.user_email">
                    <template v-slot:label>
                        Кем добавлено
                    </template>
                </ValueSelect>
                <TextInputWrapper name="gallery-image-name" id="gallery-image-name" placeholder="Название"
                    v-model="filters.original_name">
                    <template v-slot:label>
                        Название
                    </template>
                </TextInputWrapper>
            </div>
        </div>
        <ImagesGallery :singleSelect="singleSelect" :isSubgallery="isSubgallery" v-model="gallery" v-model:selected="selectedItems" :imageTag="imageTag"
            @update:modelValue="onGalleryUpdate">
        </ImagesGallery>
        <ListPagination ref="listPagination" :noRouter="noRouter" :loadLink="loadLink" :filters="filters" :limit="9"
            v-model="gallery" v-model:isLoading="isLoading" v-model:meta="galleryMeta" v-model:count="totalCount">
        </ListPagination>
    </div>
</template>

<script>
import LoadingScreen from '@/components/page/LoadingScreen.vue'
import ImagesGallery from '@/components/inputs/images/ImagesGallery.vue'
import ListPagination from '@/components/pagination/ListPagination.vue'
import ValueSelect from '@/components/inputs/ValueSelect.vue'
import TextInputWrapper from '@/components/inputs/TextInputWrapper.vue'

export default {
    name: 'ImagesGalleryPageable',
    emits: ['update', 'update:selected', 'update:modelValue'],
    props: {
        noRouter: Boolean,
        selected: Array,
        modelValue: Array,
        singleSelect: Boolean,
        imageTag: [Boolean, String],
        isSubgallery: Boolean
    },
    components: {
        LoadingScreen,
        ImagesGallery,
        ListPagination,
        ValueSelect,
        TextInputWrapper
    },
    data() {
        return {
            isLoading: false,
            gallery: [],
            galleryMeta: {},
            totalCount: 0,
            filters: {
                user_email: 'self',
                original_name: '',
                tag: this.imageTag
            },
            selectedItems: []
        }
    },
    computed: {
        loadLink() {
            return import.meta.env.VITE_GALLERY_GET_LINK
        },
        galleryUploaders() {
            const allUploaders = { string: 'Все добавившие', value: 'false' }
            const you = { string: 'Вы', value: 'self' }
            if (Array.isArray(this.galleryMeta.all_uploaders)) {
                return [
                    allUploaders,
                    you,
                    ...this.galleryMeta.all_uploaders.map(obj => obj.email)
                ]
            }
            return [allUploaders, you]
        }
    },
    methods: {
        async loadGallery() {
            if (!this.$refs.listPagination)
                return

            await this.$refs.listPagination.loadList(true)
        },
        async onGalleryUpdate() {
            this.loadGallery()
            this.$emit('update')
            if (this.$refs.listPagination) {
                setTimeout(() => {
                    this.$refs.listPagination.setPage('last')
                }, 500);
            }
        }
    },
    watch: {
        selectedItems: {
            deep: true,
            handler() {
                this.$emit('update:selected', this.selectedItems)
            }
        },
        gallery: {
            deep: true,
            handler() {
                this.$emit('update:modelValue', this.gallery)
            }
        }
    }
}
</script>