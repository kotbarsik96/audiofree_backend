import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import components from './components'
import icons from './components/icons'

const app = createApp(App)

app.use(createPinia())
app.use(router)
components.forEach(comp => app.component(comp.name, comp))
icons.forEach(obj => app.component(obj.name, obj.component))

app.mount('#app')