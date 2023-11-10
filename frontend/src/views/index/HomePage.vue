<template>
    <main>
        <!-- <BestsellersSlider></BestsellersSlider> -->
        <BrandTabsSection></BrandTabsSection>
    </main>
</template>

<script>
import BestsellersSlider from '@/components/sliders/BestsellersSlider.vue'
import BrandTabsSection from '@/components/page/sections/homepage/BrandTabsSection.vue'

export default {
    name: 'HomePage',
    components: {
        BestsellersSlider,
        BrandTabsSection
    }
}
</script>

<style></style>