<template>
    <div>
    
    </div>
  </template>
  <script setup lang="ts">
  export interface Props {
    userStocksSymbol: string[]
  }
  const props = defineProps<Props>()
  const emit = defineEmits(['update:userStocksSymbols'])
  
  
  </script>
  