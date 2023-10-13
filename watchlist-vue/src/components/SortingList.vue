<template>
  <div>
    <button
      v-for="sortOrder in sortOrders"
      :key="sortOrder.field"
      class="text-base font-bold px-4 py-2 block hover:bg-blue-100 w-full text-left"
      :class="{
        'bg-blue-200': sortField === sortOrder.field
      }"
      @click="select(sortOrder.field)"
    >
      {{ sortOrder.displayName }}
    </button>
  </div>
</template>
<script setup lang="ts">
export interface Props {
  sortField: string
}
const props = defineProps<Props>()
const emit = defineEmits(['updateSortField'])

const sortOrders = [
  {
    displayName: 'Custom Order',
    field: 'custom'
  },
  {
    displayName: 'Sort By Symbol',
    field: 'symbol'
  },
  {
    displayName: 'Sort By Price',
    field: 'price'
  },
  {
    displayName: 'Sort By Change',
    field: 'change'
  },
  {
    displayName: 'Sort By Market Cap',
    field: 'market_cap'
  }
]

const select = (value) => {
  emit('updateSortField', value)
}
</script>
