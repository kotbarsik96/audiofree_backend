<template>
    <ComponentWithGallery tag="section" class="section cta-section section--theme-colored"
        :class="{ 'cta-section--right-text': isRightText, 'cta-section--scaled-image': isScaledImage }" isSingleSelect
        :imageTag="imageTag" v-model="gallery">
        <div class="container">
            <div class="cta-section__text">
                <slot name="text"></slot>
            </div>
            <div class="cta-section__image">
                <ImagePicture v-if="desktopImage" :obj="desktopImage" media="(min-width: 1060px)" :mediaSources="mediaSource">
                </ImagePicture>
            </div>
            <div class="cta-section__buttons">
                <slot name="buttons"></slot>
            </div>
        </div>
    </ComponentWithGallery>
</template>

<script>
import ComponentWithGallery from '@/components/misc/ComponentWithGallery.vue'

export default {
    name: 'CtaSection',
    components: {
        ComponentWithGallery
    },
    props: {
        imageTag: String,
        isRightText: Boolean,
        isScaledImage: Boolean
    },
    data() {
        return {
            gallery: []
        }
    },
    computed: {
        mediaSource() {
            const mobile = this.gallery
                .find(o => o.original_name && o.original_name.match(/mobile/i))
            const tablet = this.gallery
                .find(o => o.original_name && o.original_name.match(/tablet/i))

            const arr = []

            if (mobile && !tablet)
                arr.push({
                    obj: mobile,
                    media: '(max-width: 1059px)'
                })
            if (!mobile && tablet)
                arr.push({
                    obj: tablet,
                    media: '(max-width: 559px)'
                })

            if (mobile && tablet) {
                arr.push({
                    obj: tablet,
                    media: '(max-width: 1059px)'
                })
                arr.push({
                    obj: mobile,
                    media: '(max-width: 559px)'
                })
            }

            return arr
        },
        desktopImage() {
            if (this.gallery.length < 1)
                return null

            const image = this.gallery
                .find(o => o.original_name && !o.original_name.match(/mobile|tablet/i))
            return image || this.gallery[0]
        }
    }
}
</script>

<style lang="scss">
.cta-section {
    .container {
        display: grid;
        align-items: center;
        grid-template-columns: 590px 1fr;
        position: relative;
        height: 100%;
        padding-top: 100px;
        padding-bottom: 105px;

        @media (max-width: 949px) {
            padding-top: 40px;
            padding-bottom: 50px;
        }
    }

    &__text {
        position: relative;
        z-index: 10;
        grid-column: 1 / 2;

        .section-title {
            margin-bottom: 40px;
            font-size: 40px;
        }
    }

    &--right-text &__text {
        grid-column: 2 / -1;
    }

    &__par {
        margin-bottom: 50px;
        font-size: 20px;
        line-height: 30px;
        max-width: 410px;
    }

    &__image {
        position: absolute;
        bottom: 0;
        right: 10px;
        grid-column: 2 / -1;
        z-index: 5;
        display: flex;
        align-items: flex-end;
        width: 500px;

        picture {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transform-origin: bottom center;
        }
    }

    &--scaled-image &__image {
        picture {
            transform: scale(1.25);
        }
    }

    &--right-text &__image {
        grid-column: 1 / 2;
    }

    &__buttons {
        grid-column: 1 / 2;

        .button {
            min-width: 337px;
        }
    }

    &--right-text &__buttons {
        grid-column: 2 / -1;
    }

    &--right-text &__text,
    &--right-text &__buttons {
        margin-left: 55px;
    }

    @media (max-width: 1299px) {
        &--scaled-image &__image {
            picture {
                transform: scale(1);
            }
        }
    }

    @media (max-width: 1069px) {
        .container {
            display: block;
        }

        &__text {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;

            .section-title {
                margin-bottom: 20px;
            }
        }

        &__buttons {
            text-align: center;
        }

        &__par {
            margin-bottom: 30px;
        }

        &__image {
            position: relative;
            margin: 0 auto 10px auto;
        }

        &--right-text &__text,
        &--right-text &__buttons {
            margin-left: 0px;
        }
    }

    @media (max-width: 519px) {
        &__image {
            width: 100%;
        }

        &__text {
            .section-title {
                font-size: 24px;
                line-height: 30px;
            }
        }
    }

    @media (max-width: 369px) {
        .button--cta {
            font-size: 16px;
            line-height: 18px;
            padding: 20px 15px;
            width: 100%;
        }

        &__buttons {
            grid-column: 1 / 2;

            .button {
                min-width: unset;
            }
        }
    }
}

.button--cta {
    color: #fff;
    font-size: 18px;
    line-height: 21px;
    font-weight: 700;
    padding: 27px 32px;
    background-color: #97D413;
    box-shadow: 0px 12px 30px rgba(0, 0, 0, .15);
    border-color: transparent;
    background-image: none;
    transition-property: background-color, color, background-image;
    transition-delay: 0s, 0s, .6s;

    &:hover {
        background-color: var(--theme_color_3);
    }

    span {
        display: block;
    }
}
</style>