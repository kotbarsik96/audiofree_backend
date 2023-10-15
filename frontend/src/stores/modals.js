import { defineStore } from 'pinia'
import { generateRandom } from '@/assets/js/scripts.js'

/* modalData: {
    component: h(ImportedComponent, props = {})
    id: ... (добавляется в addModal)
}
ImportedComponent example: import RegisterModal from '@/components/modals/RegisterModal.vue'
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
            modalData.component.modalId = modalData.id
            return modalData.id
        },
        // удалит первый в списке this.modals окно, ЕСЛИ НЕ передан ИЛИ передан НЕЧИСЛОВОЙ modalId. Если передан ЧИСЛОВОЙ modalId, удалит окно с этим modalId
        removeModal(modalId = null) {
            if (isNaN(parseInt(modalId))) {
                this.modals.splice(0, 1)
                return
            }

            const index = this.modals.findIndex(data => data.id === id)
            if (index >= 0)
                this.modals.splice(index, 1)
            else 
                this.modals.splice(0, 1)
        }
    }
})