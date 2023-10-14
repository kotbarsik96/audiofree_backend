<template>
   <div class="spoiler">
      <div class="spoiler__item" v-for="(title, index) in titles" :key="index" ref="spoilerNode">
         <div
            class="spoiler__title"
            @click="toggleSpoiler(index)"
         >{{ typeof title === "string" ? title : title.text }}</div>
         <div class="spoiler__content" ref="contentContainer">
            <slot :name="index"></slot>
         </div>
      </div>
   </div>
</template>

<script>
import shared from "./shared";

export default {
   name: "InfoSpoiler",
   props: {
      params: Object,
      titles: Array,
   },
   data() {
      return {
         spoilerNodesData: [],
         titleClassName: "spoiler__content-title",
         paragraphClassName: "spoiler__content-paragraph",
      };
   },
   methods: {
      getSpoilerNodesData() {
         return new Promise((resolve) => {
            this.spoilerNodesData = this.$refs.spoilerNode.map((node) => {
               const titleNode = node.querySelector(".spoiler__title");
               const contentNode = node.querySelector(".spoiler__content");
               const height = contentNode.offsetHeight;

               resolve();
               return { node, titleNode, contentNode, height };
            });
         });
      },
      toggleSpoiler(number) {
         const spoilerData = this.spoilerNodesData[number];
         const isShown = spoilerData.titleNode.classList.contains("__active");

         isShown
            ? this.hideSpoiler(spoilerData)
            : this.showSpoiler(spoilerData);
      },
      showSpoiler(spoilerData) {
         if (this.params.isAccordeon) this.hideAllSpoilers();

         spoilerData.titleNode.classList.add("__active");
         spoilerData.contentNode.style.cssText = `max-height: ${spoilerData.height}px;`;
      },
      hideSpoiler(spoilerData) {
         spoilerData.titleNode.classList.remove("__active");
         spoilerData.contentNode.style.cssText =
            "max-height: 0px; padding: 0px; margin-bottom: 25px";
      },
      hideAllSpoilers() {
         this.spoilerNodesData.forEach((spoilerData) =>
            this.hideSpoiler(spoilerData)
         );
      },
      setContentClassNames: shared.setContentClassNames,
   },
   mounted() {
      this.$nextTick().then(this.getSpoilerNodesData).then(this.hideAllSpoilers);
      this.setContentClassNames("spoiler");
   },
};
</script>