<template>
  <AddStockButton v-if="stockSymbol && type === 'add-to-watchlist-button'" :stockSymbol="stockSymbol" />
  <SidebarStocks v-else-if="type === 'sidebar'" />
  <UserWatchList v-else />
</template>
<script setup lang="ts">
import UserWatchList from './components/UserWatchList.vue'
import SidebarStocks from './components/SidebarStocks.vue'
import AddStockButton from './components/AddStockButton.vue'

export interface Props {
  type: 'add-to-watchlist-button' | 'sidebar' | 'watchlist'
  stockSymbol: string | null
}
const props = defineProps<Props>()


</script>
