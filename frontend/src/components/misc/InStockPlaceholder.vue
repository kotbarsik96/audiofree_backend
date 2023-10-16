<template>
    <div class="in-stock-placeholder">
        <div class="in-stock-placeholder__icon" :style="[`--icon_color: ${iconColor}`]">
            <component :is="iconComponent"></component>
        </div>
        <div class="in-stock-placeholder__text">
            {{ text }}
        </div>
    </div>
</template>

<script>

const types = {
    'in-stock': {
        text: 'В наличии',
        icon: 'CheckmarkIcon',
        iconColor: '#0DB10A',
    },
    'out-of-stock': {
        text: 'Нет в наличии',
        icon: 'CloseCircularIcon',
        iconColor: 'var(--error_color)',
    }
}
export default {
    name: 'InStockPlaceholder',
    props: {
        type: {
            type: String,
            validator(value) {
                return Object.keys(types).includes(value)
            }
        },
    },
    computed: {
        text() {
            return types[this.type].text
        },
        iconComponent() {
            return types[this.type].iconComponent || 'CheckmarkIcon'
        },
        iconColor() {
            return types[this.type].iconColor || '#000'
        }
    }
}
</script>

<style lang="scss">
.in-stock-placeholder {
    display: flex;
    align-items: center;
    border: 1px solid #DCDCDC;
    border-radius: 14px;
    padding: 5px 10px 5px 5px;

    &__icon {
        margin-right: 10px;
        width: 13px;
        height: 13px;
        color: var(--icon_color);
        transform: translateY(-1px);
    }

    &__text {
        font-size: 12px;
        line-height: 14px;
        color: #454545;
    }
}
</style>