<template>
    <div class="price-range">
        <div class="price-range__price">
            <div class="price-range__price-input">
                <input class="price-range__input price-range__input--min" :value="minValue"
                    @input="checkCorrectInput($event)" @change="onInputsChange($event, 'min')" type="text" placeholder="499"
                    ref="minValueInput" data-range-input />
                {{ valueString }}
            </div>
            <div class="price-range__separator">–</div>
            <div class="price-range__price-input">
                <input class="price-range__input price-range__input--max" :value="maxValue"
                    @input="checkCorrectInput($event)" @change="onInputsChange($event, 'max')" type="text"
                    placeholder="8499" ref="maxValueInput" data-range-input />
                {{ valueString }}
            </div>
        </div>
        <div class="price-range__range price-range__range--double">
            <div class="price-range__scale" @pointerdown="moveClosestToggler($event)" ref="scale">
                <div class="price-range__bar" ref="bar" :style="{ 'width': `${barWidth}px`, 'left': `${togglerMinPos}px` }">
                </div>
            </div>
            <div class="price-range__toggle price-range__toggle--min" ref="togglerMin"
                @pointerdown="dragToggler($event, 'min')" :style="{ 'left': `${togglerMinPos}px` }"></div>
            <div class="price-range__toggle price-range__toggle--max" ref="togglerMax"
                @pointerdown="dragToggler($event, 'max')" :style="{ 'left': `${togglerMaxPos}px` }"></div>
        </div>
    </div>
</template>

<script>
import { getCoords } from "@/assets/js/scripts";

export default {
    name: "RangeBlock",
    emits: ["valuesChange"],
    props: {
        valueString: {
            type: String,
            default: "",
        },
        minValueThreshold: {
            type: Number,
            default: 0,
        },
        maxValueThreshold: {
            type: Number,
            default: 9999,
        },
        filterName: String
    },
    data() {
        return {
            togglerMinPos: 0,
            togglerMaxPos: 0,
            minValue: 0,
            maxValue: 0,
            valueStep: 0,
            widthValues: {
                toggler: 0,
                scale: 0,
            },
        };
    },
    computed: {
        barWidth() {
            return this.togglerMaxPos - this.togglerMinPos;
        },
        valueInputs() {
            const minValueInput = this.$refs.minValueInput;
            const maxValueInput = this.$refs.maxValueInput;
            return [minValueInput, maxValueInput];
        },
    },
    methods: {
        updateSizes() {
            if (this.$refs.togglerMin)
                this.widthValues.toggler = this.$refs.togglerMin.offsetWidth;
            if (this.$refs.scale)
                this.widthValues.scale = this.$refs.scale.offsetWidth;

            this.valueStep =
                this.maxValueThreshold /
                (this.widthValues.scale - this.widthValues.toggler);
        },
        setDefaultPos() {
            let oldScaleWidth = this.widthValues.scale;
            const setPositions = (event = null) => {
                // выставить, только если размер шкалы был изменен при resize экрана или при загрузке страницы
                if (!event || oldScaleWidth !== this.widthValues.scale) {
                    this.togglerMinPos = 0;
                    this.togglerMaxPos =
                        this.widthValues.scale - this.widthValues.toggler;
                    oldScaleWidth = this.widthValues.scale;
                }
            };

            setPositions();
            window.addEventListener("resize", setPositions);
        },
        checkCorrectInput(event) {
            // убрать все кроме цифр
            const val = event.target.value.toString();
            event.target.value = val.replace(/\D/g, "");

            // убрать "0" в начале
            if (val.length > 1 && val.indexOf("0") === 0) {
                const correct = val.split("");
                correct.splice(0, 1);
                event.target.value = correct.join("");
            }

            // не превышать значение maxValueThreshold
            // if (parseInt(val) > this.maxValueThreshold) {
            //     this.addNotification({
            //         message: `Введенное значение (${val}) превысило максимальное - ${this.maxValueThreshold}, поэтому было исправлено`,
            //         timeout: 1500,
            //     });
            //     event.target.value = this.maxValueThreshold;
            // }
        },
        onTogglersMove() {
            this.minValue = Math.round(this.togglerMinPos * this.valueStep);
            this.maxValue = Math.round(this.togglerMaxPos * this.valueStep);
            this.$emit("valuesChange");
        },
        onInputsChange(event = null, type = null) {
            if (event && type) {
                let value = event.target.value;
                if (type === "min") {
                    if (value > this.maxValueThreshold)
                        value = this.maxValueThreshold;
                    this.minValue = parseInt(value);
                }
                if (type === "max") {
                    if (value < this.minValue) value = this.minValue;
                    this.maxValue = parseInt(value);
                }
            }

            this.togglerMinPos = this.minValue / this.valueStep;
            this.togglerMaxPos = this.maxValue / this.valueStep;

            this.$emit("valuesChange");
        },
        moveClosestToggler(event) {
            const scaleCoord = getCoords(this.$refs.scale).left;
            let coordX = event.clientX - scaleCoord - this.widthValues.toggler / 2;
            const scaleEdgeValue =
                this.widthValues.scale - this.widthValues.toggler;
            // если выходит за пределы шкалы, определить значением coordX значение края шкалы
            if (coordX > scaleEdgeValue) coordX = scaleEdgeValue;
            if (coordX < 0) coordX = 0;
            const isMinClosest =
                coordX - this.togglerMinPos <= this.togglerMaxPos - coordX;
            isMinClosest
                ? (this.togglerMinPos = coordX)
                : (this.togglerMaxPos = coordX);

            this.dispatchTogglerEvent();

            const togglerType = isMinClosest ? "min" : "max";
            this.dragToggler(event, togglerType);
        },
        dragToggler(downEvent, togglerType) {
            const scaleCoordX = getCoords(this.$refs.scale).left;
            const toggler = downEvent.target;

            const onUp = () => {
                document.removeEventListener("pointermove", onMove);
                document.removeEventListener("pointerup", onUp);
            };
            const onMove = (moveEvent) => {
                moveEvent.preventDefault();
                const pointerCoords =
                    moveEvent.clientX - scaleCoordX - this.widthValues.toggler / 2;

                switch (togglerType) {
                    case "min":
                        if (pointerCoords > 0 && pointerCoords < this.togglerMaxPos)
                            this.togglerMinPos = pointerCoords;
                        if (pointerCoords > this.togglerMaxPos)
                            this.togglerMinPos = this.togglerMaxPos;
                        if (pointerCoords < 0) this.togglerMinPos = 0;
                        break;
                    case "max":
                        const scaleWidth =
                            this.widthValues.scale - this.widthValues.toggler;

                        if (
                            pointerCoords > this.togglerMinPos &&
                            pointerCoords < scaleWidth
                        )
                            this.togglerMaxPos = pointerCoords;
                        if (pointerCoords < this.togglerMinPos)
                            this.togglerMaxPos = this.togglerMinPos;
                        if (pointerCoords > scaleWidth)
                            this.togglerMaxPos = scaleWidth;
                        break;
                }

                this.dispatchTogglerEvent();

                document.addEventListener("pointerup", onUp);
            };

            document.addEventListener("pointermove", onMove);
        },
        dispatchTogglerEvent() {
            document.dispatchEvent(new Event("togglerPositionChange"));
        },
    },
    watch: {
        minValue() {
            this.onInputsChange();
        },
        maxValue() {
            this.onInputsChange();
        },
    },
    mounted() {
        this.updateSizes();
        this.setDefaultPos();
        window.addEventListener("resize", this.updateSizes);
        this.onTogglersMove();
        document.addEventListener("togglerPositionChange", this.onTogglersMove);
    },
};
</script>