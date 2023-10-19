<template>
    <div class="loading-screen" :style="{ '--square-size': squareSize, position }">
        <div class="loading-screen__wrapper">
            <div class="loading-screen__circle" style="--i: 1"></div>
            <div class="loading-screen__circle" style="--i: 2"></div>
            <div class="loading-screen__circle" style="--i: 3"></div>
            <div class="loading-screen__circle" style="--i: 4"></div>
            <div class="loading-screen__circle" style="--i: 5"></div>
            <div class="loading-screen__circle" style="--i: 6"></div>
            <div class="loading-screen__circle" style="--i: 7"></div>
            <div class="loading-screen__circle" style="--i: 8"></div>
            <div class="loading-screen__circle" style="--i: 9"></div>
            <div class="loading-screen__circle" style="--i: 10"></div>
            <div class="loading-screen__circle" style="--i: 11"></div>
            <div class="loading-screen__circle" style="--i: 12"></div>
            <div class="loading-screen__circle" style="--i: 13"></div>
            <div class="loading-screen__circle" style="--i: 14"></div>
            <div class="loading-screen__circle" style="--i: 15"></div>
            <div class="loading-screen__circle" style="--i: 16"></div>
            <div class="loading-screen__circle" style="--i: 17"></div>
            <div class="loading-screen__circle" style="--i: 18"></div>
            <div class="loading-screen__circle" style="--i: 19"></div>
            <div class="loading-screen__circle" style="--i: 20"></div>
        </div>
        <div class="loading-screen__icon">
            <HeadphonesIcon></HeadphonesIcon>
        </div>
    </div>
</template>

<script>
export default {
    name: "LoadingScreen",
    props: {
        squareSize: {
            type: String,
            default: '125px'
        },
        position: {
            type: String,
            default: 'absolute'
        }
    }
};
</script>

<style lang="scss">
.loading-screen {
    --ls-anim-duration: 1.5s;
    --circles-amount: 20;

    position: fixed;
    z-index: 1100;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: rgba(255, 255, 255, .5);
    display: flex;
    align-items: center;
    justify-content: center;

    &__wrapper {
        width: calc(var(--square-size) * 1.3);
        height: calc(var(--square-size) * 1.3);
        position: absolute;
    }

    &__icon {
        svg {
            width: var(--square-size);
            height: var(--square-size);
            color: #039900;
            animation: color-blink var(--ls-anim-duration) ease infinite alternate;
        }

        @keyframes color-blink {
            0% {
                color: #039900;
            }

            100% {
                color: var(--theme_color);
            }
        }
    }

    &__circle {
        width: 100%;
        height: 100%;
        transform: rotate(calc(360deg / var(--circles-amount) * var(--i)));
        position: absolute;

        &::before {
            content: "";
            border-radius: 50%;
            width: 15px;
            height: 15px;
            position: absolute;
            background: var(--theme_color);
            top: 0;
            left: 0;
            animation: circle-blink var(--ls-anim-duration) calc(0.1s * var(--i)) ease infinite alternate;

            @keyframes circle-blink {
                0% {
                    opacity: 1;
                }

                100% {
                    opacity: 0;
                }
            }
        }
    }
}
</style>