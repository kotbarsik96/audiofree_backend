import { nextTick } from "vue";
import { mediaQueriesHandlers } from "@/assets/js/scripts";

export default {
    props: {
        vendorCode: String,
    },
    emits: ["favoriteToggled"],
    data() {
        return {
            isIconsOpened: false,
            isFavorite: false,
            mediaQueries: {
                992: false
            },
            cardButtonsParents: {}
        };
    },
    computed: {
        product() {
            
        },
    },
    methods: {
        toggleCardIcons() {
            this.isIconsOpened = !this.isIconsOpened;
        },
        mediaQueriesHandlers,
        placeCardButtons(doReplace = false) {
            const cardButtons = this.$refs.cardButtons;
            if (!cardButtons) return;

            const parents = this.cardButtonsParents;
            if (!parents.mobile) parents.mobile = this.$refs.cardIconsContainer;
            if (!parents.desktop) parents.desktop = cardButtons.parentNode;

            if (parents.mobile && cardButtons)
                doReplace ? parents.mobile.append(cardButtons) : parents.desktop.append(cardButtons);
        },
    },
    watch: {
        'mediaQueries.992'(matches) {
            this.placeCardButtons(matches);
        },
        // здесь нужно вызывать методы, которые должны работать только после полной отрисовки компонента; полная отрисовка выполняется только если есть загружен product
        product(newVal, oldVal) {
            if (!oldVal && newVal) {
                nextTick().then(() => {
                    this.placeCardButtons();
                });
            }
        }
    },
    mounted() {
        this.mediaQueriesHandlers();
    },
}