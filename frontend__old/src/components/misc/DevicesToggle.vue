<template>
    <div class="devices-toggle">
        <div class="card product-card page-reference__devices-list devices-list">
            <div v-for="(device, devKey) in devices" :key="devKey" @click="selectedDeviceName = devKey"
                class="devices-list__item devices-toggle__title" :class="{ '__active': selectedDeviceName === devKey }">
                <div :class="`devices-list__text __icon-${device.icon}`">{{ device.title }}</div>
            </div>
            <RouterLink :to="{ name: 'catalogue', params: { pageNumber: 1 } }"
                class="devices-list__item devices-list__item--more">
                <div class="devices-list__text">Смотреть больше моделей</div>
            </RouterLink>
        </div>
        <Transition name="mobslider-transition" mode="out-in">
            <MobileSlider class="page-reference__devices" :slideClassName="slideClassName" :media="767"
                :key="selectedDeviceName">
                <template v-for="id in selectedDeviceInfo.ids" :key="id" #[id]>
                    <component :is="productCardComponent" :id="id"></component>
                </template>
            </MobileSlider>
        </Transition>
    </div>
</template>

<script>
import MobileSlider from "@/components/mobile-slider/MobileSlider.vue";

export default {
    name: "DevicesToggle",
    components: {
        MobileSlider,
    },
    props: {
        isMobileSlider: {
            type: Boolean,
            default: false,
        },
        devices: {
            type: Object,
            required: true,
            // пример: iPhone: { title: 'Для iPhone', icon: 'apple', ids: ['400511', '400513', '400514'] },
        },
        productCardType: {
            type: String,
            default: "1",
        },
        slideClassName: {
            type: String,
            default: "card-wrapper",
        },
    },
    data() {
        return {
            selectedDeviceName: null,
        };
    },
    computed: {
        selectedDeviceInfo() {
            return this.devices[this.selectedDeviceName];
        },
        productCardComponent() {
            let template = "product-card-";
            return (template += this.selectedDeviceInfo.productCardType
                ? this.selectedDeviceInfo.productCardType
                : this.productCardType);
        },
    },
    created() {
        this.selectedDeviceName = Object.keys(this.devices)[0];
    },
};
</script>

<style scoped lang="scss">
$devListTransDur: 0.5s;

.devices-list {
    &__item {
        transition-property: border, box-shadow;
        transition-duration: $devListTransDur;
    }

    &__item>&__text {
        transition-property: color;
        transition-duration: $devListTransDur;
    }
}

.mobslider-transition {

    &-enter-from,
    &-leave-to {
        opacity: 0;
    }

    &-enter-to,
    &-leave-from {
        opacity: 1;
    }

    &-enter-active,
    &-leave-active {
        transition: all $devListTransDur;
    }
}
</style>