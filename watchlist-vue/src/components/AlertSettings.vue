<template>
  <div class="bg-gray-50">
    <WatchListStock :stock="stock" class="bg-white">
      <template #right>
        <div class="flex flex-col justify-between items-center">
          <div class="text-xs">Current price</div>
          <div class="font-bold">${{ stock.price }}</div>
        </div>
      </template>
    </WatchListStock>

    <div class="max-w-xl m-auto py-4 px-6">
      <h3 class="font-bold text-sm mb-2">Notify me when {{ stock.symbol }} reaches:</h3>
      <div class="">
        <div v-if="priceAlert" class="border rounded-lg flex justify-between overflow-hidden">
          <button class="hover:bg-gray-200 bg-slate-100" @click="priceAlert.desired_price--">
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
          <input type="number" class="flex-1 text-center" v-model="priceAlert.desired_price" />
          <button class="hover:bg-gray-200 bg-slate-100" @click="priceAlert.desired_price++">
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
      <div v-if="priceAlert" class="flex justify-between border rounded-lg mb-6 mt-2">
        <button
          v-for="percentage in percentages"
          :key="percentage.value"
          class="flex-1 py-1 px-2 text-xs hover:bg-gray-100 transition-colors"
          @click="
            priceAlert.desired_price = (
              priceAlert.current_price *
              (1 + percentage.value / 100)
            ).toFixed(2)
          "
        >
          {{ percentage.label }}
        </button>
      </div>
      <div class="mb-4 flex flex-col gap-3">
        <button
          class="w-full bg-green-600 hover:bg-green-700 transition-colors font-bold text-white rounded-md py-2 px-4 disabled:bg-slate-500 disabled:animate-pulse"
          @click="updatePriceAlert"
          :disabled="isUpdating"
        >
          {{ mode === 'create' ? 'Create' : 'Update' }}
        </button>
        <button
          v-if="mode === 'update'"
          class="w-full text-red-500 rounded-md py-1 px-4 disabled:bg-slate-500 disabled:animate-pulse hover:bg-red-50 transition-colors"
          @click="deletePriceAlert"
          :disabled="isDeleting"
        >
          Delete alert
        </button>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import type { Stock, PriceAlert } from '@/types'
import axios from 'axios'
import { computed, ref } from 'vue'
import WatchListStock from './WatchListStock.vue'

export interface Props {
  stock: Stock
}
const props = defineProps<Props>()
const emit = defineEmits(['update:stock', 'alertUpdated'])

const stock = computed<Stock>({
  get() {
    return props.stock
  },
  set(value) {
    emit('update:stock', value)
  }
})

const priceAlert = ref<PriceAlert | null>(props.stock?.price_alert || null)
const mode = ref<'create' | 'update'>('create')
const userId = window.userId || null

if (!priceAlert.value) {
  mode.value = 'create'
  priceAlert.value = {
    symbol: props.stock.symbol,
    desired_price: props.stock.price,
    current_price: props.stock.price
  }
} else {
  mode.value = 'update'
  priceAlert.value.current_price = props.stock.price
}

const isUpdating = ref(false)
const updatePriceAlert = async () => {
  try {
    if (!priceAlert.value) return
    isUpdating.value = true
    const formData = new FormData()
    formData.append('action', 'watchlist_save_user_stock_alert_data')
    formData.append('symbol', props.stock.symbol)
    formData.append('desired_price', priceAlert.value.desired_price.toString())
    formData.append('current_price', priceAlert.value.current_price.toString())
    formData.append('user_id', userId)
    const stocks = await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data)
    stock.value.price_alert = priceAlert.value

    emit('alertUpdated', priceAlert.value)
  } catch (error) {
    console.log(error)
  } finally {
    isUpdating.value = false
  }
}

const isDeleting = ref(false)
const deletePriceAlert = async () => {
  try {
    if (!priceAlert.value) return
    isDeleting.value = true
    const formData = new FormData()
    formData.append('action', 'watchlist_delete_user_stock_alert_data')
    formData.append('symbol', props.stock.symbol)
    formData.append('user_id', userId)
    const stocks = await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data)

    stock.value.price_alert = null
    emit('alertUpdated', priceAlert.value)
  } catch (error) {
    console.log(error)
  } finally {
    isDeleting.value = false
  }
}

const percentages = [
  {
    value: -20,
    label: '-20%'
  },
  {
    value: -10,
    label: '-10%'
  },
  {
    value: -5,
    label: '-5%'
  },
  {
    value: 5,
    label: '+5%'
  },
  {
    value: 10,
    label: '+10%'
  },
  {
    value: 20,
    label: '+20%'
  }
]
</script>
