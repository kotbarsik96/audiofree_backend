import { defineStore } from 'pinia'
import { generateRandom } from '@/assets/js/scripts.js'

/* modalData: {
    component: h(ImportedComponent, props = {})
    id: ... (добавляется в addModal)
}
 */
export const useModalsStore = defineStore('modals', {
    state: () => {
        return {
            modals: [

            ]
        }
    },
    actions: {
        addModal(modalData) {
            const usedIds = this.modals.map(obj => obj.id)
            modalData.id = generateRandom(usedIds)

            this.modals.push(modalData)
        },
        removeFirstModal(){
            this.modals.splice(0, 1);
        }
    }
})