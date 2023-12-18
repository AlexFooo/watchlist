import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import VueLazyLoad from 'vue3-lazyload'


let elementSelector = '#app'
if(window.stockSymbol) {
    elementSelector = '#add-to-watchlist-app'
}

const app = createApp(App)
app.use(VueLazyLoad, {
})
app.mount(elementSelector)