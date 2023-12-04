<template>
  <SidebarStocks v-if="isSidebar" />
  <UserWatchList v-else />
</template>
<script setup lang="ts">
import UserWatchList from './components/UserWatchList.vue'
import SidebarStocks from './components/SidebarStocks.vue'
import { computed } from 'vue'

const isSidebar = computed(() => {
  return Boolean(window.isSidebarStocks)
})
</script>
