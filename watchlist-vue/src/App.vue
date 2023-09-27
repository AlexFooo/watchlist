<template>
  <UserWatchList :userStocksSymbolsString="userStocksSymbolsString" />
</template>
<script setup lang="ts">
import UserWatchList from './components/UserWatchList.vue'

const userStocksSymbolsString = 'AAPL,MSFT,GOOG,AMZN,FB,TSLA'
</script>
