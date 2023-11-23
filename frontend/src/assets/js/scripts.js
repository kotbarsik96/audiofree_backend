export function generateRandom(array = []) {
    array = array.map(i => i.toString())
    const firstPart = Math.floor(Math.random() * 100)
    const secondPart = Math.floor(Math.random() * 100)

    let rand = `${firstPart}${secondPart}`;
    while (array.includes(rand)) {
        rand = generateRandom(array)
    }

    return parseInt(rand)
}

export function delay(timeout) {
    return new Promise(resolve => {
        setTimeout(resolve, timeout);
    })
}

export function getCoords(el) {
    const c = el.getBoundingClientRect()

    return {
        top: c.top + window.scrollY,
        bottom: c.bottom + window.scrollY,
        left: c.left + window.scrollX,
        right: c.right + window.scrollX,
    }
}

export function alignModal(modalWindow) {
    const height = modalWindow.offsetHeight

    const windowHeight = document.documentElement.clientHeight

    const modalsList = document.querySelector(".modals-list")
    const paddingTop = modalsList
        ? parseInt(getComputedStyle(modalsList).paddingTop.replace(/\D/g, ""))
        : 20
    const paddingBottom = modalsList
        ? parseInt(getComputedStyle(modalsList).paddingBottom.replace(/\D/g, ""))
        : 20

    if (height > windowHeight - paddingBottom - paddingTop)
        modalWindow.style.alignSelf = 'flex-start'
    else
        modalWindow.style.removeProperty('align-self')
}

export function isNumeric(value) {
    const intValue = parseInt(value)
    return !isNaN(intValue)
}

export function removeErrorsOnInput(event) {
    const name = event.target.name
    if (this.errors[name])
        delete this.errors[name]

    if (this.errorMessage)
        this.errorMessage = ''
}

export function findClosest(relative, selector, maxParentsCount = null) {
    let closestNode = null
    let parent = relative
    let count = 0

    while (!closestNode) {
        if (isNumeric(maxParentsCount) && maxParentsCount <= count)
            break
        if (!parent)
            break

        parent = parent.parentNode
        if (!parent)
            break
        closestNode = parent.querySelector(selector)
        count++
    }

    return closestNode
}

export function getHeight(el, opts = {}) {
    let clone = el.cloneNode(true)
    const origStyles = getComputedStyle(el)
    clone.style.cssText = `
        width: ${opts.width || el.offsetWidth}px;
        position: absolute; 
        top: 0; 
        left: 0;
        z-index: -999;
        opacity: 0;
        pointer-events: none;
        font-size: ${origStyles.fontSize};
        line-height: ${origStyles.lineHeight};
    `
    el.after(clone)
    const height = clone.offsetHeight
    clone.remove()
    clone = null

    return height
}

export function createElement(tagName = 'div', optionsOrClassname = null, content = null) {
    const element = document.createElement(tagName)
    if (optionsOrClassname) {
        if (typeof optionsOrClassname === 'string')
            element.className = optionsOrClassname
        else {
            for (let key in optionsOrClassname) {
                element.setAttribute(key, optionsOrClassname[key])
            }
        }
    }
    if (content) {
        element.innerHTML = content
    }
    return element
}

export function handleAjaxError(err, ctx) {
    if (!err)
        return
    if (!err.response)
        return
    if (typeof ctx.error !== 'string' && !Array.isArray(ctx.errors))
        return

    const data = err.response.data
    if (typeof ctx.error === 'string' && data.error)
        ctx.error = data.error
    else if (ctx.errors && data.errors)
        ctx.errors = data.errors
    else if (!ctx.errors && typeof ctx.error === 'string' && data.errors) {
        const values = Object.values(data.errors)
        if (Array.isArray(values[0]) && values[0][0])
            ctx.error = values[0][0]
    }
    else
        ctx.error = 'Произошла ошибка'
}

/* работает только в массивах вида: array: [{ id: some }, { id: some2 }] */
export function removeFromArrayById(array, id) {
    const index = array.findIndex(obj => {
        if (!obj)
            return

        return obj.id === id
    })

    if (index < 0)
        return false

    array.splice(index, 1)
    return true
}

export function adjustTextarea(textareaOrEvent) {
    if (!textareaOrEvent)
        return

    let textarea = textareaOrEvent
    if (!textarea.tagName)
        textarea = textareaOrEvent.target

    if (!textarea)
        return

    textarea.style.height = '1px'
    textarea.style.height = `${textarea.scrollHeight}px`
}

export function adjustTextareas(relative, selector = 'textarea') {
    if (!relative)
        return
    if (typeof relative.querySelectorAll !== 'function')
        return

    const elements = relative.querySelectorAll(selector)
    elements.forEach(textarea => adjustTextarea(textarea))
}

export function getExcerpt(longText, options = {}) {
    if (!isNumeric(options.maxlength))
        options.maxlength = 50
    options.maxlength = parseInt(options.maxlength)

    let cut = longText.slice(0, options.maxlength)
    if (cut.length < options.maxlength)
        return cut

    const match = cut.match(/^.+(?=\s)/)
    if (match)
        cut = match[0]

    if (options.after && cut.length > 0)
        cut += options.after

    return cut
}

export function getNumber(string) {
    if (!string)
        return 0

    return parseFloat(string.toString().replace(/[^0-9,\.]/g, ''))
}

export function getScrollWidth() {
    const outer = createElement('div', {
        style: 'overflow: scroll; z-index: -999; position: absolute; top: -100vh; left: -100vw; width: 100px'
    })
    const inner = createElement('div')
    outer.append(inner)
    document.body.append(outer)

    const width = outer.offsetWidth - inner.offsetWidth
    outer.remove()

    return width
}

/* позволяет делать ленивую загрузку изображений в Swiper. Внедряется в компонент, внутри которого находится <Swiper></Swiper>.
    1. необходим обработчик <Swiper @swiper="onSwiper"></Swiper>
    2. onSwiper(swiper) {
        this.swiperLazyLoad = new SwiperLazyLoad(swiper, this)
    },
    3. создать data(){ 
        return { 
            swiperLazyLoadConditions: {},
            swiperLazyLoad: null
        }
    }
    4. swiperLazyLoadConditions заполняется индексами слайдов. Сколько будет слайдов, столько и будет ключей-индексов со значением: { isActiveSlide: false }, т.е.:
        0: { isActiveSlide: false },
        1: { isActiveSlide: false }
        ...и т.д. Например, в TapeSliderSection на this.gallery стоит watcher, при обновлении которого вызывается такой метод (также он вызывается на created()):
        getLazyLoadConditions() {
            // проходится по галерее
            for (let i in this.gallery) {
                // если такой уже был в объекте, пропускает: у него может стоять isActiveSlide: true
                if (this.swiperLazyLoadConditions[i])
                    continue

                // создать объект
                this.swiperLazyLoadConditions[i] = { isActiveSlide: false }
            }
        }
    5. пропс <ImagePicture :lazyLoadConditions="swiperLazyLoadConditions[slideIndex]"></ImagePicture>
    Не лишним будет делать вызов this.swiperLazyLoad.onSlideChange() после того, как обновился массив, из которого делаются слайды
*/
export class SwiperLazyLoad {
    constructor(swiper, componentCtx) {
        this.onSlideChange = this.onSlideChange.bind(this)

        this.swiper = swiper
        this.compCtx = componentCtx

        swiper.on('slideChange', this.onSlideChange)
        this.compCtx.$nextTick().then(() => {
            setTimeout(this.onSlideChange, 150)
        })
    }
    load() {
        const conditions = this.compCtx.swiperLazyLoadConditions

        const isCentered = this.swiper.params.centeredSlides
        const realSlidesCount = this.swiper.slides
            .filter(sl => !sl.classList.contains('swiper-slide-duplicate'))
            .length
        let slidesPerView = this.swiper.params.slidesPerView
        if (slidesPerView % 2 === 0)
            slidesPerView++

        const activeSlideRealIndex = this.swiper.realIndex
        const slidesToLoad = [activeSlideRealIndex]
        if (isCentered) {
            for (let i = 1; i <= Math.floor(slidesPerView / 2); i++) {
                slidesToLoad.push(activeSlideRealIndex + i)
                slidesToLoad.push(activeSlideRealIndex - i)
            }
        } else {
            for (let i = 1; i <= slidesPerView; i++)
                slidesToLoad.push(activeSlideRealIndex + i)
        }

        slidesToLoad.forEach(index => {
            if (index < 0)
                index = realSlidesCount - Math.abs(index)
            else if (index > realSlidesCount - 1)
                index = realSlidesCount - index

            if (conditions[index])
                conditions[index].isActiveSlide = true
        })
    }
    async onSlideChange() {
        // если все изображения уже итак загружены, не выполнять далее
        const allLoaded = !Object.values(this.compCtx.swiperLazyLoadConditions)
            .some(o => !o.isActiveSlide)
        if (allLoaded)
            return

        await this.compCtx.$nextTick()
        this.load()
    }
}