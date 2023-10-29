export function isCreated(itemId) {
    return this.$refs.adminListTable.isCreated(itemId)
}

export function deleteFromArrays(itemId) {
    this.$refs.adminListTable.deleteById(itemId)
}

export function updateList() {
    this.$refs.paginationComponent.loadList(true)
    this.selectedItems = this.selectedItems.filter(id => list.find(o => o.id === parseInt(id)))
}

/* использование: <ListPagination ref="paginationComponent" @updateLoaded="getSavedList"></ListPagination> */
export function getSavedList(loadedArray) {
    const saved = {}
    loadedArray.forEach(obj => saved[obj.id] = JSON.stringify(obj))
    this.listSaved = saved
}

/* работает в связке с функцией getSavedList(). Требуется, чтобы в data был listSaved: {} */
export function isUnsaved(itemId) {
    const item = this.list.find(o => o.id === itemId)
    if (!item || !this.listSaved[itemId])
        return true

    return JSON.stringify(item).replace(/"/g, '') !== this.listSaved[itemId].replace(/"/g, '')
}