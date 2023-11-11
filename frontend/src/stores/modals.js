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
            modals: []
        }
    },
    actions: {
        addModal(modalData) {
            const usedIds = this.modals.map(obj => obj.id)
            modalData.id = generateRandom(usedIds)

            this.modals.push(modalData)
            return modalData.id
        },
        // удалит первый в списке this.modals окно, ЕСЛИ НЕ передан ИЛИ передан НЕЧИСЛОВОЙ modalId. Если передан ЧИСЛОВОЙ modalId, удалит окно с этим modalId
        removeModal(modalId = null) {
            if (isNaN(parseInt(modalId))) {
                if(!this.modals[0])
                    return
                const id = this.modals[0].id
                document.dispatchEvent(new CustomEvent('modal-deleted', { detail: { id } }))
                this.modals.splice(0, 1)
                return
            }

            const index = this.modals.findIndex(data => data.id === modalId)

            if (index >= 0) {
                const id = this.modals[index].id
                document.dispatchEvent(new CustomEvent('modal-deleted', { detail: { id } }))
                this.modals.splice(index, 1)
            }
        }
    }
})