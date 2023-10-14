import { createApp } from 'vue'
import App from './App.vue'
import { createPinia } from 'pinia'
import router from './router'

import pageComponents from '@/components/index.js'

const pinia = createPinia()
const app = createApp(App)

pageComponents.forEach(comp => app.component(comp.name, comp))

app.use(pinia)
app.use(router)

app.mount('#app')
