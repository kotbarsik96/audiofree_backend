/* !здесь находятся переиспользуемые скрипты, которые можно импортировать в компонент/другой скрипт! */

// скрытие/сокрытие элемента по типу спойлера
export class SpoilerElem {
    constructor(button, body, media = null) {
        this.shownClass = "__shown-spoiler";
        this.height = body.offsetHeight;
        this.button = button;
        this.body = body;
        this.windowWidth = document.documentElement.clientWidth || window.innerWidth;
        body.parentNode.style.overflow = "hidden";

        this.onResize = this.onResize.bind(this);
        this.toggle = this.toggle.bind(this);

        if (media) {
            const onMediaChange = () => {
                if (mediaQuery.matches) this.setHandlers();
                else this.removeHandlers();
            }
            const mediaQuery = window.matchMedia(`(max-width: ${media}px)`);

            onMediaChange();
            mediaQuery.addEventListener("change", onMediaChange);
        }
        else this.setHandlers();
    }
    onResize() {
        const currentWindowWidth = document.documentElement.clientWidth || window.innerWidth;
        if (currentWindowWidth !== this.windowWidth) {
            const isShown = this.button.classList.contains(this.shownClass);

            this.body.style.transition = "none";
            this.show();
            this.height = this.body.offsetHeight;
            if (!isShown) this.hide();
            this.body.style.removeProperty("transition");
            this.windowWidth = currentWindowWidth;
        }
    }
    setHandlers() {
        window.addEventListener("resize", this.onResize);
        this.hide();
        this.button.addEventListener("click", this.toggle);
    }
    removeHandlers() {
        window.removeEventListener("resize", this.onResize);
        this.show();
        this.button.removeEventListener("click", this.toggle);
    }
    toggle() {
        this.button.classList.contains(this.shownClass) ? this.hide() : this.show();
    }
    hide() {
        this.button.classList.remove(this.shownClass);
        [this.body.style.maxHeight, this.body.style.padding, this.body.style.margin] = [0, 0, 0];
    }
    show() {
        this.button.classList.add(this.shownClass);
        this.body.parentNode.style.removeProperty("padding-bottom");
        this.body.style.removeProperty("padding");
        this.body.style.removeProperty("margin");
        this.body.style.maxHeight = `${this.height}px`;

    }
}

export function generateRandom(array = []){
    array = array.map(i => i.toString());
    const firstPart = Math.floor(Math.random() * 100);
    const secondPart = Math.floor(Math.random() * 100);

    let rand = `${firstPart}${secondPart}`;
    while(array.includes(rand)) {
        rand = generateRandom(array);
    }

    return parseInt(rand);
}