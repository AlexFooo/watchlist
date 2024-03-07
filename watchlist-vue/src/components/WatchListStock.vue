<template>
  <div>
    <div
      class="watchlist-item flex justify-between w-full py-3 px-4 border-b overflow-hidden gap-5 md:gap-20 md:items-center"
    >
      <div class="watchlist-item-left flex gap-2 md:items-center md:w-60">
        <slot name="left" />

        <slot name="image" v-if="slots.image" />
        <img
          v-else-if="stock.logo"
          v-lazy="{
            src: stock.logo,
            loading: 'your loading image url',
            error: `https://letizo.com/wp-content/plugins/massive-stock-widgets/assets/public/img/placeholders/${stock.symbol.charAt(
              0
            )}.svg`
          }"
          :alt="stock.symbol.toUpperCase()"
          class="w-9 rounded-full"
        />
        <div class="stock flex flex-col relative items-start justify-center">
          <p
            class="stock-index relative font-sans text-base font-semibold text-black leading-none after:content-link after:flex after:items-center after:absolute after:w-2 after:h-1 after:top-0.5 after:-right-2.5"
          >
            {{ stock.symbol }} <slot name="afterSymbol" />
          </p>
          <p
            class="stock-name truncate max-w-[120px] font-sans text-sm font-light text-[#555] leading-none"
          >
            <slot v-if="slots.underSymbol" name="underSymbol" />
            <template v-else>{{ stock.company_name || stock.name }}</template>
          </p>
        </div>
      </div>
      <slot name="right" />
    </div>
  </div>
</template>
<script setup lang="ts">
import type { SearchStock, Stock } from '@/types'
import { ref, useSlots } from 'vue'

export interface Props {
  stock: Stock | SearchStock
}

const props = defineProps<Props>()

const stock = ref<Stock | SearchStock>(props.stock)
const slots = useSlots()

// const alertSettingsVisible = ref(false)
</script>
