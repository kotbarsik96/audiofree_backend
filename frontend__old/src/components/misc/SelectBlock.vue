<template>
   <div
      class="select"
      ref="selectBlock"
      :class="{ '__active': isOptionsShown }"
      @click="isOptionsShown = !isOptionsShown"
   >
      <div class="select__selected">{{ selectedOption.title }}</div>
      <div class="select__options-list" ref="options">
         <label class="select__option" v-for="option in options" :key="option.value">
            <input
               type="radio"
               :name="inputName"
               :value="option.value"
               @change="setSelectedOption(option)"
            />
            <div>{{ option.title }}</div>
         </label>
      </div>
   </div>
</template>

<script>
// в случае, если в props указан objectToSort, возвращает отсортированный по значением в value объект, если не указан objectToSort, компонент вернет выбранные title и value

export default {
   name: "SelectBlock",
   // sortValueChange - при сортировке объекта (возвращает отсортированный объект), selectValueChange - при выборе опции (возвращает объект с title и value)
   emits: ["sortValueChange", "selectValueChange"],
   props: {
      inputName: {
         type: String,
         required: true,
      },
      objectToSort: {
         type: [Array, Object],
      },
      options: {
         // пример: { title: 'По названию', value: 'name' },
         // пример: { title: 'С начала', value: 'start' },
         type: Array,
         required: true,
      },
   },
   data() {
      return {
         selectedOption: {
            title: "",
            value: "",
         },
         isOptionsShown: false,
         sortedObject: [] || {},
      };
   },
   methods: {
      setDefaultSelected() {
         let isSet = false;

         this.$nextTick().then(() => {
            // если нужно отсортировать объект
            if (this.objectToSort) {
               // проверить, получены ли значения в this.objectToSort. Если нет - запускать функцию заново до момента, пока значения не будут получены
               for (let key in this.objectToSort) {
                  if (this.objectToSort[key] && !isSet) return doSet.call(this);
                  else this.setDefaultSelected();
               }
            }
            // если нужно выбрать значение, т.е. нет объекта на сортировку
            else doSet.call(this);
         });

         function doSet() {
            isSet = true;

            const inp = this.$refs.options.querySelector("input[type='radio']");
            const event = new Event("change");
            inp.checked = true;
            inp.dispatchEvent(event);
            this.isOptionsShown = false;
         }
      },
      setSelectedOption(option) {
         this.selectedOption.title = option.title;
         this.selectedOption.value = option.value;
         this.isOptionsShown = false;

         // если есть объект, то отсортировать его; иначе - вернуть выбранные значения
         this.objectToSort ? this.doSort() : this.doSelect();
      },
      doSelect() {
         this.$emit("selectValueChange", this.selectedOption);
      },
      doSort(obj = null) {
         const objectToSort = obj ? obj : this.objectToSort;

         const isArray = Array.isArray(this.objectToSort);
         const keyToSort = this.selectedOption.value;

         if (isArray) this.sortedObject = objectToSort.sort(sortMethod);
         else {
            this.sortedObject = {};
            // поместить свойства объекта в массив и сортировать
            const arr = [];
            for (let key in objectToSort) {
               const item = objectToSort[key];
               item.__objectKey = key;
               arr.push(item);
            }
            arr.sort(sortMethod);

            // перенести свойства из отсортированного массива обратно в объект
            for (let item of arr) {
               const key = item.__objectKey;
               this.sortedObj[key] = item;
               delete item.__objectKey;
            }
         }

         this.$emit("sortValueChange", this.sortedObject);

         function sortMethod(a, b) {
            if (a[keyToSort] > b[keyToSort]) return 1;
            if (a[keyToSort] < b[keyToSort]) return -1;
            return 0;
         }
      },
      toggleBlock(event) {
         const select = this.$refs.selectBlock;
         if (
            event.target.closest(".select") !== select &&
            event.target !== select &&
            this.isOptionsShown === true
         ) {
            this.isOptionsShown = false;
         }
      },
   },
   mounted() {
      document.addEventListener("click", this.toggleBlock);
      // автоматически делает $emit("sortValueChange")
      this.$nextTick().then(this.setDefaultSelected);
   },
};
</script>