<template>
  <div class="p-4 border-b bg-gray-50">
    <div class="max-w-xl m-auto">
      <h3 class="font-bold text-sm mb-3">Notify me when {{ stock.symbol }} reaches:</h3>
      <div class="mb-6">
        <div>Set rate</div>

        <div v-if="priceAlert" class="border rounded-lg flex justify-between overflow-hidden">
          <button class="hover:bg-gray-100" @click="priceAlert.desired_price--">
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
          <button class="" @click="priceAlert.desired_price++">
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
      <div class="mb-4 flex flex-col gap-3">
        <button
          class="w-full bg-green-600 font-bold text-white rounded-md py-2 px-4 disabled:bg-slate-500 disabled:animate-pulse"
          @click="createPriceAlert"
          :disabled="isUpdating"
        >
          {{ mode === 'create' ? 'Create alert' : 'Update alert' }}
        </button>
        <button
          v-if="mode === 'update'"
          class="w-full bg-red-500 font-bold text-white rounded-md py-2 px-4 disabled:bg-slate-500 disabled:animate-pulse"
          @click="deletePriceAlert"
          :disabled="isUpdating"
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

export interface Props {
  stock: Stock
}
const props = defineProps<Props>()
const emit = defineEmits(['update:stock'])

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
const userId = window.userId || 484

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
const createPriceAlert = async () => {
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
    if (mode.value === 'create') {
      stock.value.price_alert = priceAlert.value
      mode.value = 'update'
    }
  } catch (error) {
    console.log(error)
  } finally {
    isUpdating.value = false
  }
}
const deletePriceAlert = async () => {
  try {
    if (!priceAlert.value) return
    isUpdating.value = true
    const formData = new FormData()
    formData.append('action', 'watchlist_delete_user_stock_alert_data')
    formData.append('symbol', props.stock.symbol)
    formData.append('user_id', userId)
    const stocks = await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data)

    if (mode.value === 'update') {
      mode.value = 'create'
    }
    stock.value.price_alert = null
  } catch (error) {
    console.log(error)
  } finally {
    isUpdating.value = false
  }
}
</script>
