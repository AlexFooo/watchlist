import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import VueLazyLoad from 'vue3-lazyload'

const apps = document.querySelectorAll('.letizo-vue-app')
apps.forEach((app) => {
  const appType = app.dataset.type
  const stockSymbol = app.dataset.symbol || null

  const appInstance = createApp(App, { type: appType, stockSymbol })
  appInstance.use(VueLazyLoad, {})
  appInstance.mount(app)
})
