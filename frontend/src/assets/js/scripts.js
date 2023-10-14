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

    if(this.errorMessage)
        this.errorMessage = ''
}