import { defineStore } from 'pinia'
import { generateRandom, isNumeric } from '@/assets/js/scripts.js'
import NotificationItem from '@/components/notifications/NotificationItem.vue'
import { h } from 'vue'

/* notificationData === {
    timeout: this.defaultTimeout (по умолчанию) - "время жизни" уведомления в мс
    message: '' - для компонента по умолчанию. Передает в props.message. Перезапишет props.message, уже указанный ранее у компонента, если компонент тоже передан
    (не обязательно) component: по умолчанию - NotificationItem, иначе можно передать другой компонент
    (не обязательно) props: {} - для компонента по умолчанию. 
        Работает, только если не передан component; иначе нужно создавать компонент через h(ComponentName, props = {}) и указывать props'ы там, в этой функции. 
}

*/
export const useNotificationsStore = defineStore('notifications', {
    state: () => {
        return {
            notifications: [],
            defaultTimeout: 3000
        }
    },
    actions: {
        addNotification(notificationData) {
            if(!notificationData.component && !notificationData.message)
                return

            // определить props
            if (!notificationData.props || typeof notificationData.props !== 'object')
                notificationData.props = {}

            // определить текст уведомления
            if (notificationData.message)
                notificationData.props.message = notificationData.message

            // определить "время жизни" уведомления
            if (!isNumeric(notificationData.timeout))
                notificationData.timeout = this.defaultTimeout
            notificationData.props.timeout = notificationData.timeout

            // если не передан компонент, создать по умолчанию, передав ему props при создании
            if (!notificationData.component || typeof notificationData.component !== 'object')
                notificationData.component = h(NotificationItem, notificationData.props)
            // иначе просто передать props в уже созданный компонент
            else {
                for (let key in notificationData.props) {
                    notificationData.component[key] = notificationData.props[key]
                }
            }

            const usedIds = this.notifications.map(obj => obj.id)
            notificationData.id = generateRandom(usedIds)

            this.notifications.push(notificationData)
            notificationData.component.notificationId = notificationData.id
            return notificationData.id
        },
        // удалит первый в списке this.notifications окно, ЕСЛИ НЕ передан ИЛИ передан НЕЧИСЛОВОЙ notificationId. Если передан ЧИСЛОВОЙ notificationId, удалит окно с этим notificationId
        removeNotification(notificationId = null) {
            if (isNaN(parseInt(notificationId))) {
                this.notifications.splice(0, 1)
                return
            }

            const index = this.notifications.findIndex(data => data.id === id)
            if (index >= 0)
                this.notifications.splice(index, 1)
            else
                this.notifications.splice(0, 1)
        }
    }
})