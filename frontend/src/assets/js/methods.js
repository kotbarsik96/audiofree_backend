import { isNumeric } from './scripts.js'
import { adjustTextarea } from './scripts.js'

/* требуется, чтобы в data был прописан объект matchMediaMatches такого вида:
    matchMediaMatches: {
        max: {
            '992': false
        },
        min: {
            '993': false
        }
    }
А в mounted() прописать this.setMatchMedia()
будет автоматически выставлять window.matchMedia('(max-width: 992px)').matches
*/
export function setMatchMedia() {
    function forEachCallback(mediaValue, type) {
        if (!isNumeric(mediaValue))
            return

        const mm = window.matchMedia(`(${type}-width: ${mediaValue}px)`)
        mm.addEventListener('change', () => this.matchMediaMatches[type][mediaValue] = mm.matches)
    }

    forEachCallback = forEachCallback.bind(this)
    const max = this.matchMediaMatches.max
    const min = this.matchMediaMatches.min
    if (max) {
        Object.keys(max)
            .forEach(mediaValue => forEachCallback(mediaValue, 'max'))
    }
    if (min) {
        Object.keys(this.matchMediaMatches.min)
            .forEach(mediaValue => forEachCallback(mediaValue, 'min'))
    }
}