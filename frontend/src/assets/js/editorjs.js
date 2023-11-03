export function renderEditorDataHTML(data) {
    if (!data)
        return

    if (typeof data === 'string')
        data = JSON.parse(data)

    const blocks = data.blocks
    if (!Array.isArray(blocks))
        return null
    if (blocks.length < 1)
        return null

    let result = ''
    blocks.forEach(obj => {
        let tagName
        switch (obj.type) {
            case 'paragraph':
            default:
                tagName = 'p'
                break
        }
        result += `<${tagName}>${obj.data.text}</${tagName}>`
    })

    return result
}