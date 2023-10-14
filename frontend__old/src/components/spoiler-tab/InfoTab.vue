<template>
   <div class="tab" :class="{ 'tab--vertical': params.isVertical }">
      <div class="tab__titles">
         <div
            class="tab-spoiler__title tab__title"
            v-for="(title, index) in titles"
            :key="index"
            @click="tabChange(index)"
            :class="{ '__active': activeTitle === index }"
         >
            <div class="tab__title-text">{{ typeof title === "string" ? title : title.text }}</div>
         </div>
      </div>
      <div class="tab__content" ref="contentContainer">
         <slot :name="activeTitle"></slot>
      </div>
   </div>
</template>

<script>
import shared from "./shared";

export default {
   name: "InfoTab",
   props: {
      params: Object,
      titles: Array,
   },
   data() {
      return {
         activeTitle: 0,
         titleClassName: "tab__content-title",
         paragraphClassName: "tab__content-text tab__content-paragraph",
      };
   },
   methods: {
      setContentClassNames: shared.setContentClassNames,
      tabChange(index) {
         this.activeTitle = index;
         this.$nextTick(() => {
            this.setContentClassNames("tab");
         });
      },
   },
   mounted() {
      this.setContentClassNames("tab");
   },
};
</script>

<style lang="scss">
$tabTransDur: 0.5s;

.tab {
   &__title {
      transition-property: font-size, font-weight, color, margin;
      transition-duration: $tabTransDur;
   }
}
</style>