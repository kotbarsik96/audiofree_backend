export function isCreated(itemId) {
    return this.$refs.adminListTable
        ? this.$refs.adminListTable.isCreated(itemId)
        : false
}
export function deleteFromArrays(itemId) {
    this.$refs.adminListTable.deleteById(itemId)
}