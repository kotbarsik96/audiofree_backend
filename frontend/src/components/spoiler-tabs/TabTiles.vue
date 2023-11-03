<template>
    <div class="tab-tiles" :class="{ 'tab-tiles--vertical': isVertical }">
        <div class="tab-tiles__buttons">
            <button v-for="(item, index) in content" :key="index" class="tab-tiles__button"
                :class="{ '__active': currentTab === index }" type="button" @click="currentTab = index">
                <div>
                    {{ item.button }}
                </div>
            </button>
        </div>
        <div class="tab-tiles__body">
            <Transition mode="out-in" name="fade-in">
                <div v-if="content[currentTab]" :key="currentTab" v-html="content[currentTab].text">
                </div>
            </Transition>
        </div>
    </div>
</template>

<script>
export default {
    name: 'TabTiles',
    props: {
        /* content: [
            { 
                button: 'Нажми меня', 
                text: '....часть длинного текста для таба, можно с <span>тегами</span> html и компонентами <BurgerIcon></BurgerIcon>...' 
            }
        ] */
        content: {
            type: Array,
            default: []
        },
        isVertical: Boolean
    },
    data() {
        return {
            currentTab: 0
        }
    }
}
</script>

<style lang="scss">
.tab-tiles {
    --border_radius: 9px;
    --transition_duration: .3s;

    display: flex;
    align-items: flex-start;
    border-radius: var(--border_radius);

    &__buttons {
        position: relative;
        z-index: 5;
        flex: 0 0 42%;
        background-color: rgba(254, 253, 255, 0.5);
        box-shadow: 0px 4px 18px rgba(0, 0, 0, .05);
        margin-bottom: 20px;
        border-top-left-radius: var(--border_radius);
        border-bottom-left-radius: var(--border_radius);
        box-shadow: inset -7px 0 12px -12px rgba(0, 0, 0, 0.4), 0px 4px 18px rgba(0, 0, 0, .05);
    }

    &__button {
        width: 100%;
        text-align: left;
        font-size: 16px;
        line-height: 19px;
        font-weight: 400;
        background: transparent;
        transition-property: background-color;
        transition-duration: var(--transition_duration);
        display: block;
        position: relative;

        &:first-child {
            border-top-left-radius: var(--border_radius);
        }

        &:last-child {
            border-bottom-left-radius: var(--border_radius);

            div {
                border-bottom-color: transparent;
            }
        }

        div {
            margin: 0 20px;
            border-bottom: 1px solid #e8e8e8;
            color: #8a8a8a;
            padding: 40px 50px 30px 50px;
            transition-property: font-size, font-weight, color, border-color;
            transition-duration: var(--transition_duration);
        }
    }

    &__button.__active {
        top: 2px;
        background-color: #fff;
        transform: scale(1.05);
        box-shadow: 0px 4px 15px rgba(0, 0, 0, .05);
        border-top-left-radius: var(--border_radius);
        border-bottom-left-radius: var(--border_radius);

        div {
            font-size: 20px;
            line-height: 23px;
            font-weight: 700;
            color: #4e4e4e;
            border-bottom-color: transparent;
        }
    }

    &__body {
        flex: 1 1 auto;
        background-color: #fff;
        box-shadow: -5px 0px 0px rgb(255, 255, 255), 0px 4px 18px rgba(0, 0, 0, .05);
        border-radius: var(--border_radius);
        border-top-left-radius: 0;
        align-self: stretch;
        position: relative;
        z-index: 10;

        .text {
            padding: 40px 30px 65px 85px;
        }

        p {
            font-size: 16px;
            line-height: 19px;
            margin-bottom: 30px;
            color: #494949;

            &:last-child {
                margin-bottom: 0;
            }
        }

        .title {
            font-size: 22px;
            color: #494949;
            margin-bottom: 30px;
        }
    }
}

.tab-tiles--vertical {
    display: block;

    .tab-tiles {
        &__buttons {
            display: flex;
            margin-bottom: 0;
            border-radius: var(--border_radius);
            box-shadow: none;
        }

        &__button {
            flex: 1 1 auto;
            margin-right: 18px;
            border-radius: var(--border_radius);
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            font-weight: 300;
            font-size: 18px;
            line-height: 21px;
            text-align: center;
            border: 1px solid #F8F8F8;
            border-bottom: none;

            &:last-child {
                margin-right: 0;
            }

            div {
                border-bottom: none;
                padding: 32px 0 23px 0;
            }
        }

        &__button.__active {
            transform: none;
            font-weight: 500;
            box-shadow: inset 0 -7px 9px -7px #fff, 0px 0px 19px rgba(0, 0, 0, .05);
        }

        &__body {
            box-shadow: 0px 0px 19px rgba(0, 0, 0, .05);
        }
    }
}
</style>