<template>
   <div class="tab-spoiler__container">
      <info-spoiler
         v-if="mediaQueries[spoilerMedia]"
         :params="spoilerParams"
         :titles="titles"
      >
         <template v-for="slotNum in slots" :key="slotNum" #[slotNum]>
            <slot :name="slotNum"></slot>
         </template>
      </info-spoiler>
      <info-tab
         v-else
         :params="tabParams"
         :titles="titles"
      >
         <template v-for="slotNum in slots" :key="slotNum" #[slotNum]>
            <slot :name="slotNum"></slot>
         </template>
      </info-tab>
   </div>
</template>

<script>
import { mediaQueriesHandlers } from "@/assets/js/scripts";
import InfoSpoiler from "@/components/spoiler-tab/InfoSpoiler.vue";
import InfoTab from "@/components/spoiler-tab/InfoTab.vue";

export default {
   name: "SpoilerTabWrapper",
   components: {
      InfoSpoiler,
      InfoTab,
   },
   props: {
      spoilerMedia: {
         type: Number,
         default: 767,
      },
      tabParams: {
         type: Object,
         default: {
            isVertical: false,
         },
      },
      spoilerParams: {
         type: Object,
         default: {
            isAccordeon: false,
         },
      },
      titles: {
         type: Array,
         default: [],
         // пример: [ 'Описание товара:', { text: 'Характеристики:', contentContainerClass: 'specs' } ]
      },
   },
   data() {
      return {
         mediaQueries: {},
         bodiesContents: [],
      };
   },
   methods: {
      mediaQueriesHandlers,
      getSlotsContent() {
         for (let i in this.$slots) {
            const bodyContent = [];
            const slotNodes = this.$slots[i]();
            slotNodes.forEach((node) => {
               const tag = node.type;
               const content = node.children;
               const className = node.props ? node.props.class : null;
               bodyContent.push({ tag, content, className });
            });
            this.bodiesContents.push(bodyContent);
         }
      },
   },
   computed: {
      slots() {
         return Object.keys(this.$slots);
      },
   },
   created() {
      this.mediaQueries[this.spoilerMedia] = false;
      this.mediaQueriesHandlers();
      this.getSlotsContent();
   },
};
</script>