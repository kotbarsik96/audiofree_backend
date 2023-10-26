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
    else if (Array.isArray(ctx.errors) && data.errors)
        ctx.errors = data.errors
    else if (!Array.isArray(ctx.errors) && typeof ctx.error === 'string' && data.errors) {
        const values = Object.values(data.errors)
        if (Array.isArray(values[0]) && values[0][0])
            ctx.error = values[0][0]
    }
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