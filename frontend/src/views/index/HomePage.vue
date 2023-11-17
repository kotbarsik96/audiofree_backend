<template>
    <main>
        <BestsellersSlider></BestsellersSlider>
        <BrandTabsSection></BrandTabsSection>
        <TapeSliderSection></TapeSliderSection>
        <QuestionsAnswersSection></QuestionsAnswersSection>
    </main>
</template>

<script>

import BestsellersSlider from '@/components/sliders/BestsellersSlider.vue'
import BrandTabsSection from '@/components/page/sections/homepage/BrandTabsSection.vue'
import TapeSliderSection from '@/components/page/sections/homepage/TapeSliderSection.vue'
import QuestionsAnswersSection from '@/components/page/sections/QuestionsAnswersSection.vue'

export default {
    name: 'HomePage',
    components: {
        BestsellersSlider,
        BrandTabsSection,
        TapeSliderSection,
        QuestionsAnswersSection
    }
}
</script>

<style></style>