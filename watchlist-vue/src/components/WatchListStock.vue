<template>
  <div
    class="watchlist-item flex justify-between items-center w-full px-4 py-3 border-b-2 overflow-hidden"
  >
    <div class="watchlist-item-left flex gap-2 items-center">
      <slot name="left" />

      <slot name="image" v-if="slots.image" />
      <img
        v-else
        :src="`https://letizo.com/wp-content/uploads/massive-stock-widgets/${stock.symbol}.svg`"
        alt=""
        class="w-9 rounded-full"
      />
      <div class="stock flex flex-col relative items-start justify-center">
        <p
          class="stock-index relative font-sans text-base font-semibold text-black leading-none after:content-link after:flex after:items-center after:absolute after:w-2 after:h-1 after:top-0.5 after:-right-2.5"
        >
          {{ stock.symbol }}
        </p>
        <p class="stock-name truncate font-sans text-sm font-light text-[#555] leading-none">
          {{ stock.company_name || stock.name }}
        </p>
      </div>
    </div>
    <slot name="right" />
  </div>
</template>
<script setup lang="ts">
import type { SearchStock, Stock } from '@/types'
import { useSlots } from 'vue'
export interface Props {
  stock: Stock | SearchStock
}

const props = defineProps<Props>()

const slots = useSlots()
</script>
