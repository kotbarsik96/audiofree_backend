import { defineStore } from 'pinia'
import { generateRandom } from '@/assets/js/scripts.js'
import GalleryModal from '@/components/modals/GalleryModal.vue'
import { h } from 'vue'

/* modalData: {
    component: h(ImportedComponent, props = {})|'ComponentName'
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
            if (typeof modalData.component === 'string') {
                switch (modalData.component) {
                    case 'GalleryModal':
                        modalData.component = h(GalleryModal, modalData.props || {})
                        break
                }
            }

            this.modals.unshift(modalData)
            return modalData.id
        },
        // удалит первый в списке this.modals окно, ЕСЛИ НЕ передан ИЛИ передан НЕЧИСЛОВОЙ modalId. Если передан ЧИСЛОВОЙ modalId, удалит окно с этим modalId
        removeModal(modalId = null) {
            function dispatchEvent(id) {
                setTimeout(() => {
                    document.dispatchEvent(new CustomEvent('modal-deleted', { detail: { id } }))
                }, 0);
            }

            if (isNaN(parseInt(modalId))) {
                if (!this.modals[0])
                    return
                const id = this.modals[0].id
                dispatchEvent(id)
                this.modals.splice(0, 1)
                return
            }

            const index = this.modals.findIndex(data => data.id === modalId)

            if (index >= 0) {
                const id = this.modals[index].id
                dispatchEvent(id)
                this.modals.splice(index, 1)
            }
        }
    }
})