import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import VueLazyLoad from 'vue3-lazyload'

//default app type is watchlist
let appType = 'watchlist'
let elementSelector = '#app'

const stockSymbol = window.stockSymbol || null
if (stockSymbol) {
  appType = 'add-to-watchlist-button'
  elementSelector = '#add-to-watchlist-button-app'
  window.stockSymbol = null
}

let isSidebar = window.isSidebar || false
if (isSidebar) {
  appType = 'sidebar'
  elementSelector = '#sidebar-stocks-app'
  window.isSidebar = null
}

const app = createApp(App, { type: appType, stockSymbol })
app.use(VueLazyLoad, {})
app.mount(elementSelector)
