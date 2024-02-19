<template>
  <div>
    <div
      class="watchlist-item flex justify-between w-full py-3 px-4 border-b-2 overflow-hidden gap-5 md:gap-20 md:items-center"
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
            {{ stock.symbol }}
          </p>
          <p
            class="stock-name truncate max-w-[120px] font-sans text-sm font-light text-[#555] leading-none"
          >
            {{ stock.company_name || stock.name }}
          </p>
        </div>
      </div>
      <slot name="right" />
      <button @click="alertSettingsVisible = !alertSettingsVisible">Alert</button>
    </div>
    <div v-if="alertSettingsVisible" class="p-4 border-b bg-gray-50">
      <h3 class="font-bold text-sm">Notify me when {{ stock.symbol }} reaches:</h3>
      <div>
        <div>Set rate</div>

        <div class="border rounded-lg flex justify-between max-w-xl overflow-hidden">
          <button class="hover:bg-gray-100">
            <svg
              width="44"
              height="44"
              viewBox="0 0 44 44"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <rect
                x="30"
                y="20"
                width="4"
                height="16"
                rx="1"
                transform="rotate(90 30 20)"
                fill="#E41E15"
              />
            </svg>
          </button>
          <input type="number" class="flex-1 text-center" />
          <button class="">
            <svg
              width="44"
              height="44"
              viewBox="0 0 44 44"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M21 14C20.4477 14 20 14.4477 20 15V20H15C14.4477 20 14 20.4477 14 21V23C14 23.5523 14.4477 24 15 24H20V29C20 29.5523 20.4477 30 21 30H23C23.5523 30 24 29.5523 24 29V24H29C29.5523 24 30 23.5523 30 23V21C30 20.4477 29.5523 20 29 20H24V15C24 14.4477 23.5523 14 23 14H21Z"
                fill="#E41E15"
              />
            </svg>
          </button>
        </div>
      </div>
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

const slots = useSlots()

const alertSettingsVisible = ref(false)
</script>
