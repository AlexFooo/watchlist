import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import VueLazyLoad from 'vue3-lazyload'

//default app type is watchlist
let appType = 'watchlist'
let elementSelector = '#app'

const stockSymbol = window.stockSymbol || null
let isSidebar = window.isSidebarStocks || false

if (stockSymbol) {
  appType = 'add-to-watchlist-button'
  elementSelector = '#add-to-watchlist-button-app'
  delete window.stockSymbol
} else if (isSidebar) {
  appType = 'sidebar'
  elementSelector = '#sidebar-stocks-app'
  delete window.isSidebarStocks
}

const app = createApp(App, { type: appType, stockSymbol })
app.use(VueLazyLoad, {})
app.mount(elementSelector)
