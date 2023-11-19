<template>
    <main>
        <BestsellersSlider></BestsellersSlider>
        <BrandTabsSection></BrandTabsSection>
        <TapeSliderSection></TapeSliderSection>
        <QuestionsAnswersSection></QuestionsAnswersSection>
        <CtaSection imageTag="cta-5-bests-test" isScaledImage>
            <template v-slot:text>
                <h3 class="section-title">
                    <div class="section-title__highlighted">
                        Подберем 5 лучших моделей
                    </div>
                    <div>
                        беспроводных наушников под ваши цели
                    </div>
                </h3>
                <p class="cta-section__par">
                    Пройдите тест и получите
                    <span style="color: #ADFF00">скидку до 10%</span>
                    на первую покупку
                </p>
            </template>
            <template v-slot:buttons>
                <button class="button button--cta" type="button">
                    <span>
                        Пройти тест и получить скидку!
                    </span>
                </button>
            </template>
        </CtaSection>
        <YearTopSection></YearTopSection>
        <WirelessAdvantagesSection imageTag="wireless-advantages"></WirelessAdvantagesSection>
        <AudiofreeAdvantagesSection></AudiofreeAdvantagesSection>
        <CtaSection imageTag="personal-consult" isRightText>
            <template v-slot:text>
                <h3 class="section-title">
                    <div>
                        Персональный
                    </div>
                    <div class="section-title__highlighted">
                        Консультант на связи!
                    </div>
                </h3>
                <p class="cta-section__par">
                    Когда Вы совершаете покупки на сайте “AudioFree” - все Ваши заказы ведет личный менеджер, который
                    поможет с выбором и оформлением заказа.
                </p>
                <p class="cta-section__par">
                    <span class="tape-underline">
                        Приятных и безопасных покупок!
                    </span>
                </p>
            </template>
            <template v-slot:buttons>
                <button class="button button--cta" style="margin-top: -10px" type="button">
                    <span>
                        Получить консультацию
                    </span>
                </button>
            </template>
        </CtaSection>
    </main>
</template>

<script>

import BestsellersSlider from '@/components/sliders/BestsellersSlider.vue'
import BrandTabsSection from '@/components/page/sections/homepage/BrandTabsSection.vue'
import TapeSliderSection from '@/components/page/sections/homepage/TapeSliderSection.vue'
import QuestionsAnswersSection from '@/components/page/sections/QuestionsAnswersSection.vue'
import CtaSection from '@/components/page/sections/homepage/CtaSection.vue'
import YearTopSection from '@/components/page/sections/homepage/YearTopSection.vue'
import WirelessAdvantagesSection from '@/components/page/sections/homepage/WirelessAdvantagesSection.vue'
import AudiofreeAdvantagesSection from '@/components/page/sections/homepage/AudiofreeAdvantagesSection.vue'

export default {
    name: 'HomePage',
    components: {
        BestsellersSlider,
        BrandTabsSection,
        TapeSliderSection,
        QuestionsAnswersSection,
        CtaSection,
        YearTopSection,
        WirelessAdvantagesSection,
        AudiofreeAdvantagesSection
    },
}
</script>