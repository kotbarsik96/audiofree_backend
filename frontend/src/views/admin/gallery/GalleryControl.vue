<template>
    <div class="admin-page__control">
        <div class="admin-page__gallery-container">
            <ImagesGalleryPageable></ImagesGalleryPageable>
        </div>
    </div>
</template>

<script>
import ImagesGalleryPageable from '@/components/inputs/images/ImagesGalleryPageable.vue'

export default {
    name: 'GalleryControl',
    components: {
        ImagesGalleryPageable
    },
    data() {
        return {
            isLoading: false,
            gallery: [],
            galleryMeta: {},
            original_name: '',
            user_email: '',
            filters: {
                
            }
        }
    },
}
</script>

<style></style>