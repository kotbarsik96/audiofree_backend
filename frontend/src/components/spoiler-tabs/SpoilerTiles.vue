<template>
    <div class="spoiler-tiles">
        <SpoilerElem class="spoiler-tile" v-for="(item, index) in content" :key="index" @show="onShow(index)" ref="spoilerTile">
            <template v-slot:buttonContent>
                {{ item.button }}
                <div class="spoiler-tile__button-icon"></div>
            </template>
            <div class="spoiler-tile__body" v-html="item.text"></div>
        </SpoilerElem>
    </div>
</template>

<script>

import SpoilerElem from '@/components/misc/SpoilerElem.vue'

export default {
    name: 'SpoilerTiles',
    props: {
        /* content: [
            { 
                button: 'Нажми меня', 
                text: '....часть длинного текста для спойлера, можно с <span>тегами</span> html и компонентами <BurgerIcon></BurgerIcon>...' 
            }
        ] */
        content: {
            type: Array,
            default: []
        },
        isAccordeon: Boolean
    },
    components: {
        SpoilerElem
    },
    methods: {
        onShow(index){
            if(this.isAccordeon && this.$refs.spoilerTile) {
                this.$refs.spoilerTile.forEach((component, i) => {
                    if(i === index)
                        return

                    component.hide()
                })
            }
        }
    },
}
</script>

<style lang="scss">
.spoiler-tile {
    --border_radius: 8px;

    margin-bottom: 5px;

    &.__shown {
        margin-bottom: -17px;
    }

    &__button-icon {
        position: absolute;
        right: 25px;
        top: 22px;
        width: 17px;
        height: 17px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform .3s;

        &::before,
        &::after {
            content: "";
            position: absolute;
            background-color: var(--theme_color);
            display: block;
            transition: transform .3s;
        }

        &::before {
            width: 17px;
            height: 4px;
        }

        &::after {
            width: 4px;
            height: 17px;
        }
    }

    .__shown &__button-icon {
        transform: rotate(180deg);

        &::after {
            transform: scale(0);
        }
    }

    &__body {
        padding: 47px 20px 30px 20px;
    }

    p {
        margin-bottom: 30px;

        &:last-child {
            margin-bottom: 0;
        }
    }

    .spoiler {
        &__button {
            position: relative;
            z-index: 15;
            text-align: left;
            width: 100%;
            font-size: 20px;
            line-height: 23px;
            font-weight: 700;
            padding: 20px 30px;
            color: #4e4e4e;
            box-shadow: 0px 0px 8px rgba(0, 0, 0, .15);
            border-radius: var(--border_radius);
            background-color: #fff;
        }

        &__body {
            z-index: 10;
            box-shadow: 0px 0px 8px rgba(0, 0, 0, .15);
            border-radius: var(--border_radius);
            background-color: #fff;
            position: relative;
            top: -22px;
            font-size: 16px;
            line-height: 19px;
            color: #494949;
        }
    }
}
</style>