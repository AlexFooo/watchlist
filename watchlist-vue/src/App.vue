<template>
  <AddStockButton v-if="stockSymbol" :stockSymbol="stockSymbol" />
  <SidebarStocks v-else-if="isSidebar" />
  <UserWatchList v-else />
</template>
<script setup lang="ts">
import UserWatchList from './components/UserWatchList.vue'
import SidebarStocks from './components/SidebarStocks.vue'
import { computed } from 'vue'
import AddStockButton from './components/AddStockButton.vue'

const stockSymbol = window.stockSymbol || 'aapl'
const isSidebar = window.isSidebarStocks
if (isSidebar) {
  window.isStockPage = false
}
const isStockPage = window
</script>
