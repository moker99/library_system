import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Swal from 'sweetalert2'

const app = createApp(App)
app.config.globalProperties.$swal = Swal
app.use(router)
app.mount('#app')
// createApp(App).use(router).mount('#app')
