<template>
    <div class="checkmarks-table">
        <div class="checkmarks-table__section" v-for="obj in contents" :key="obj.title">
            <h6 class="checkmarks-table__section-title">
                {{ obj.title }}:
            </h6>
            <ul class="checkmarks-table__section-list">
                <li class="checkmarks-table__section-item" v-for="item in obj.items">
                    <span :style="{ color: obj.checkmarkColor || 'var(--theme_color)' }">
                        <CheckmarkIcon></CheckmarkIcon>
                    </span>
                    <span>
                        {{ item }}
                    </span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CheckmarksTable',
    props: {
        contents: {
            type: Array,
            default: []
        }
    }
}
</script>

<style lang="scss">
.checkmarks-table {
    border: 1px solid #ECECEC;
    border-radius: var(--border_radius);
    width: 270px;

    &__section {
        border-bottom: 1px solid #ECECEC;
        padding: 20px 25px 25px 25px;

        &:last-child {
            border-bottom-width: 0px;
        }
    }

    &__section-title {
        font-size: 14px;
        line-height: 16px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    &__section-item {
        margin-bottom: 15px;
        display: flex;
        align-items: center;


        &:last-child {
            margin-bottom: 0;
        }

        svg {
            width: 13px;
            height: 13px;
            margin-right: 12px;
        }
    }
}
</style>