<template>
  <div>
    <button @click="openModal" class="p-3 hover:bg-blue-100 aspect-square">
      <svg
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path d="M20 13H13V20H11V13H4V11H11V4H13V11H20V13Z" fill="#3F3F3F" />
      </svg>
    </button>
    <TransitionRoot appear :show="isOpen" as="template">
      <Dialog as="div" @close="closeModal" class="relative z-10">
        <TransitionChild
          as="template"
          enter="duration-300 ease-out"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-black bg-opacity-25" />
        </TransitionChild>

        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full md:items-center justify-center md:p-4 text-center">
            <TransitionChild
              as="template"
              enter="duration-300 ease-out"
              enter-from="opacity-0 scale-95"
              enter-to="opacity-100 scale-100"
              leave="duration-200 ease-in"
              leave-from="opacity-100 scale-100"
              leave-to="opacity-0 scale-95"
            >
              <DialogPanel
                class="w-full max-w-md transform overflow-hidden bg-white text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg font-medium leading-6 pl-4 pr-2 py-2 bg-[#E41E15] text-white flex justify-between items-center"
                >
                  <div>Add stocks to your watchlist</div>

                  <button
                    @click="closeModal"
                    class="p-2 hover:bg-black/10 rounded-full transition-colors"
                  >
                    <svg
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M18.295 7.115C18.6844 6.72564 18.6844 6.09436 18.295 5.705C17.9056 5.31564 17.2744 5.31564 16.885 5.705L12 10.59L7.115 5.705C6.72564 5.31564 6.09436 5.31564 5.705 5.705C5.31564 6.09436 5.31564 6.72564 5.705 7.115L10.59 12L5.705 16.885C5.31564 17.2744 5.31564 17.9056 5.705 18.295C6.09436 18.6844 6.72564 18.6844 7.115 18.295L12 13.41L16.885 18.295C17.2744 18.6844 17.9056 18.6844 18.295 18.295C18.6844 17.9056 18.6844 17.2744 18.295 16.885L13.41 12L18.295 7.115Z"
                        fill="white"
                      />
                    </svg>
                  </button>
                </DialogTitle>
                <div class="bg-blue-50 flex gap-2 shadow-sm border-b border-slate-200">
                  <input
                    ref="searchInput"
                    type="text"
                    v-model="searchQuery"
                    class="w-full bg-transparent px-4 py-2 right-0 outline-1 outline-slate-300"
                    placeholder="Search"
                  />
                </div>

                <div
                  class="watchlist-list overflow-auto h-[calc(100vh-14rem)] transition-all transform"
                >
                  <div v-if="isSearching" class="p-4 min-h-[8rem] flex items-center justify-center">
                    <svg
                      aria-hidden="true"
                      class="w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-red-600"
                      viewBox="0 0 100 101"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"
                      />
                      <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"
                      />
                    </svg>
                    <span class="sr-only">Searching...</span>
                  </div>
                  <template v-else>
                    <WatchListStock :stock="stock" v-for="stock in searchList" :key="stock.symbol">
                      <template #right>
                        <div class="watchlist-item-right flex flex-wrap justify-end">
                          <button
                            @click="toggleUserStock(stock)"
                            class="p-3 text-2xl hover:bg-slate-100 rounded-full -mr-2"
                          >
                            <span class="text-sm" v-if="userStocksSymbols.includes(stock.symbol)"
                              ><svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path
                                  d="M9.00016 16.17L4.83016 12L3.41016 13.41L9.00016 19L21.0002 7L19.5902 5.59L9.00016 16.17Z"
                                  fill="black"
                                />
                              </svg>
                            </span>
                            <template v-else
                              ><svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <path d="M20 13H13V20H11V13H4V11H11V4H13V11H20V13Z" fill="black" />
                              </svg>
                            </template>
                          </button></div
                      ></template>
                    </WatchListStock>
                  </template>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>
<script setup lang="ts">
import { computed, nextTick, ref, watch } from 'vue'
import WatchListStock from './WatchListStock.vue'
import axios from 'axios'
import { watchDebounced } from '@vueuse/core'
import type { SearchStock, Stock } from '@/types'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue'
export interface Props {
  userStocksSymbols: string[]
}
const props = defineProps<Props>()
const emit = defineEmits(['update:userStocksSymbols'])

const searchQuery = ref('')
const userStocksSymbols = computed<string[]>({
  get() {
    return props.userStocksSymbols
  },
  set(value) {
    emit('update:userStocksSymbols', value)
  }
})

const defaultSearchList: SearchStock[] = [
  {
    symbol: 'AAPL',
    company_name: 'Apple Inc.',
    type: 'EQUITY',
    exchange: 'NMS',
    exchange_name: 'NasdaqGS',
    price: 177.97,
    currency: 'USD',
    open: 176.48,
    close: 175.01,
    low: 176.44,
    high: 179.38,
    previous_close: 175.01,
    change: 2.9600067,
    change_percent: 0.016913358,
    volume: 65315230,
    market_cap: 2782418436096,
    '52_week_low': 124.17,
    '52_week_low_change': 53.800003,
    '52_week_low_change_percent': 0.43327698,
    '52_week_high': 198.23,
    '52_week_high_change': -20.259995,
    '52_week_high_change_percent': -0.10220449,
    shares_outstanding: 15634199552,
    eps_ttm: 5.96,
    dividend_yield_ta: 0.53139823,
    dividend_rate_ta: 0.93,
    last_update: 1695067201000
  },
  {
    symbol: 'TSLA',
    company_name: 'Tesla, Inc.',
    type: 'EQUITY',
    exchange: 'NMS',
    exchange_name: 'NasdaqGS',
    price: 265.28,
    currency: 'USD',
    open: 271.16,
    close: 274.39,
    low: 263.7601,
    high: 271.44,
    previous_close: 274.39,
    change: -9.110016,
    change_percent: -0.033200974,
    volume: 100293452,
    market_cap: 841996042240,
    '52_week_low': 101.81,
    '52_week_low_change': 163.47,
    '52_week_low_change_percent': 1.605638,
    '52_week_high': 313.8,
    '52_week_high_change': -48.51999,
    '52_week_high_change_percent': -0.15462075,
    shares_outstanding: 3173989888,
    eps_ttm: 3.4,
    dividend_yield_ta: 0,
    dividend_rate_ta: 0,
    last_update: 1695067201000
  },
  {
    symbol: 'GOOGL',
    company_name: 'Alphabet Inc.',
    type: 'EQUITY',
    exchange: 'NMS',
    exchange_name: 'NasdaqGS',
    price: 138.21,
    currency: 'USD',
    open: 136.61,
    close: 137.4,
    low: 136.61,
    high: 139.16,
    previous_close: 137.4,
    change: 0.8100128,
    change_percent: 0.00589529,
    volume: 21842646,
    market_cap: 1747043614720,
    '52_week_low': 83.34,
    '52_week_low_change': 54.87001,
    '52_week_low_change_percent': 0.6583875,
    '52_week_high': 139.16,
    '52_week_high_change': -0.94999695,
    '52_week_high_change_percent': -0.006826652,
    shares_outstanding: 5933000192,
    eps_ttm: 4.75,
    dividend_yield_ta: 0,
    dividend_rate_ta: 0,
    last_update: 1695067201000
  },
  {
    symbol: 'AMZN',
    company_name: 'Amazon.com, Inc.',
    type: 'EQUITY',
    exchange: 'NMS',
    exchange_name: 'NasdaqGS',
    price: 139.98,
    currency: 'USD',
    open: 140.48,
    close: 140.39,
    low: 139.22,
    high: 141.73,
    previous_close: 140.39,
    change: -0.41000366,
    change_percent: -0.0029204622,
    volume: 39984835,
    market_cap: 1444285644800,
    '52_week_low': 81.43,
    '52_week_low_change': 58.549995,
    '52_week_low_change_percent': 0.7190224,
    '52_week_high': 145.86,
    '52_week_high_change': -5.880005,
    '52_week_high_change_percent': -0.040312663,
    shares_outstanding: 10317800448,
    eps_ttm: 1.22,
    dividend_yield_ta: 0,
    dividend_rate_ta: 0,
    last_update: 1695067201000
  },
  {
    symbol: 'AMD',
    company_name: 'Advanced Micro Devices, Inc.',
    type: 'EQUITY',
    exchange: 'NMS',
    exchange_name: 'NasdaqGS',
    price: 102.37,
    currency: 'USD',
    open: 100.815,
    close: 101.49,
    low: 99.45,
    high: 102.99,
    previous_close: 101.49,
    change: 0.8800049,
    change_percent: 0.008670853400000001,
    volume: 48993956,
    market_cap: 165396135936,
    '52_week_low': 54.57,
    '52_week_low_change': 47.800003,
    '52_week_low_change_percent': 0.87593925,
    '52_week_high': 132.83,
    '52_week_high_change': -30.46,
    '52_week_high_change_percent': -0.22931565,
    shares_outstanding: 1615670016,
    eps_ttm: -0.04,
    dividend_yield_ta: 0,
    dividend_rate_ta: 0,
    last_update: 1695067201000
  },
  {
    symbol: 'NVDA',
    company_name: 'NVIDIA Corporation',
    type: 'EQUITY',
    exchange: 'NMS',
    exchange_name: 'NasdaqGS',
    price: 439.66,
    currency: 'USD',
    open: 427.48,
    close: 439,
    low: 423.03,
    high: 442.4193,
    previous_close: 439,
    change: 0.66000366,
    change_percent: 0.0015034252000000001,
    volume: 48176685,
    market_cap: 1085960290304,
    '52_week_low': 108.13,
    '52_week_low_change': 331.53,
    '52_week_low_change_percent': 3.0660317,
    '52_week_high': 502.66,
    '52_week_high_change': -63,
    '52_week_high_change_percent': -0.12533322,
    shares_outstanding: 2470000128,
    eps_ttm: 4.13,
    dividend_yield_ta: 0.036446467999999996,
    dividend_rate_ta: 0.16,
    last_update: 1695067200000
  },
  {
    symbol: 'MSFT',
    company_name: 'Microsoft Corporation',
    type: 'EQUITY',
    exchange: 'NMS',
    exchange_name: 'NasdaqGS',
    price: 329.06,
    currency: 'USD',
    open: 327.8,
    close: 330.22,
    low: 326.36,
    high: 330.4,
    previous_close: 330.22,
    change: -1.1600037,
    change_percent: -0.0035128206,
    volume: 15576537,
    market_cap: 2444836732928,
    '52_week_low': 213.43,
    '52_week_low_change': 115.630005,
    '52_week_low_change_percent': 0.54177016,
    '52_week_high': 366.78,
    '52_week_high_change': -37.72,
    '52_week_high_change_percent': -0.102840945,
    shares_outstanding: 7429760000,
    eps_ttm: 9.67,
    dividend_yield_ta: 0.8236933,
    dividend_rate_ta: 2.72,
    last_update: 1695067201000
  },
  {
    symbol: 'NFLX',
    company_name: 'Netflix, Inc.',
    type: 'EQUITY',
    exchange: 'NMS',
    exchange_name: 'NasdaqGS',
    price: 394.4,
    currency: 'USD',
    open: 395.5,
    close: 396.94,
    low: 392.6,
    high: 399.465,
    previous_close: 396.94,
    change: -2.5400085,
    change_percent: -0.0063989735,
    volume: 4700447,
    market_cap: 174777171968,
    '52_week_low': 211.73,
    '52_week_low_change': 182.67,
    '52_week_low_change_percent': 0.86274976,
    '52_week_high': 485,
    '52_week_high_change': -90.600006,
    '52_week_high_change_percent': -0.18680413,
    shares_outstanding: 443147008,
    eps_ttm: 9.27,
    dividend_yield_ta: 0,
    dividend_rate_ta: 0,
    last_update: 1695067200000
  },
  {
    symbol: 'GOOG',
    company_name: 'Alphabet Inc.',
    type: 'EQUITY',
    exchange: 'NMS',
    exchange_name: 'NasdaqGS',
    price: 138.96,
    currency: 'USD',
    open: 137.63,
    close: 138.3,
    low: 137.705,
    high: 139.93,
    previous_close: 138.3,
    change: 0.66000366,
    change_percent: 0.0047722608,
    volume: 15895395,
    market_cap: 1747046891520,
    '52_week_low': 83.45,
    '52_week_low_change': 55.51001,
    '52_week_low_change_percent': 0.66518885,
    '52_week_high': 139.93,
    '52_week_high_change': -0.96998596,
    '52_week_high_change_percent': -0.0069319373,
    shares_outstanding: 5800999936,
    eps_ttm: 4.72,
    dividend_yield_ta: 0,
    dividend_rate_ta: 0,
    last_update: 1695067201000
  },
  {
    symbol: 'DIS',
    company_name: 'Walt Disney Company (The)',
    type: 'EQUITY',
    exchange: 'NYQ',
    exchange_name: 'NYSE',
    price: 85.02,
    currency: 'USD',
    open: 85.24,
    close: 85.58,
    low: 84.98,
    high: 85.92,
    previous_close: 85.58,
    change: -0.5600052,
    change_percent: -0.006543645999999999,
    volume: 11785375,
    market_cap: 155567882240,
    '52_week_low': 79.75,
    '52_week_low_change': 5.2699966,
    '52_week_low_change_percent': 0.066081464,
    '52_week_high': 118.18,
    '52_week_high_change': -33.160004,
    '52_week_high_change_percent': -0.28058895,
    shares_outstanding: 1829779968,
    eps_ttm: 1.25,
    dividend_yield_ta: 0,
    dividend_rate_ta: 0,
    last_update: 1695067361000
  },
  {
    symbol: 'BA',
    company_name: 'Boeing Company (The)',
    type: 'EQUITY',
    exchange: 'NYQ',
    exchange_name: 'NYSE',
    price: 205.12,
    currency: 'USD',
    open: 207.91,
    close: 208.11,
    low: 204.98,
    high: 207.91,
    previous_close: 208.11,
    change: -2.9900055,
    change_percent: -0.014367429000000001,
    volume: 3558177,
    market_cap: 123729190912,
    '52_week_low': 120.99,
    '52_week_low_change': 84.13,
    '52_week_low_change_percent': 0.6953467,
    '52_week_high': 243.1,
    '52_week_high_change': -37.98001,
    '52_week_high_change_percent': -0.15623204,
    shares_outstanding: 603203968,
    eps_ttm: -7.38,
    dividend_yield_ta: 0,
    dividend_rate_ta: 0,
    last_update: 1695067202000
  },
  {
    symbol: 'EBAY',
    company_name: 'eBay Inc.',
    type: 'EQUITY',
    exchange: 'NMS',
    exchange_name: 'NasdaqGS',
    price: 43.91,
    currency: 'USD',
    open: 44.34,
    close: 44.56,
    low: 43.815,
    high: 44.455,
    previous_close: 44.56,
    change: -0.6500015,
    change_percent: -0.014587106999999998,
    volume: 4460355,
    market_cap: 23367014400,
    '52_week_low': 35.92,
    '52_week_low_change': 7.9900017,
    '52_week_low_change_percent': 0.22243881,
    '52_week_high': 52.23,
    '52_week_high_change': -8.32,
    '52_week_high_change_percent': -0.15929542,
    shares_outstanding: 532156992,
    eps_ttm: 2.37,
    dividend_yield_ta: 2.1992818,
    dividend_rate_ta: 0.98,
    last_update: 1695067201000
  },
  {
    symbol: 'BABA',
    company_name: 'Alibaba Group Holding Limited',
    type: 'EQUITY',
    exchange: 'NYQ',
    exchange_name: 'NYSE',
    price: 87.02,
    currency: 'USD',
    open: 86.15,
    close: 87.07,
    low: 85.395,
    high: 87.17,
    previous_close: 87.07,
    change: -0.05000305,
    change_percent: -0.0005742856,
    volume: 9059601,
    market_cap: 221620781056,
    '52_week_low': 58.01,
    '52_week_low_change': 29.009998,
    '52_week_low_change_percent': 0.5000862,
    '52_week_high': 121.3,
    '52_week_high_change': -34.280006,
    '52_week_high_change_percent': -0.28260517,
    shares_outstanding: 2546779904,
    eps_ttm: 4.37,
    dividend_yield_ta: 0,
    dividend_rate_ta: 0,
    last_update: 1695067415000
  }
]

const searchList = ref<SearchStock[]>(defaultSearchList)

const toggleUserStock = (searchStock: SearchStock) => {
  if (userStocksSymbols.value.includes(searchStock.symbol)) {
    userStocksSymbols.value = userStocksSymbols.value.filter(
      (symbol) => symbol !== searchStock.symbol
    )
  } else {
    userStocksSymbols.value = [...userStocksSymbols.value, searchStock.symbol]
  }
}

watchDebounced(
  () => searchQuery.value,
  async (newValue) => {
    if (newValue === '') {
      searchList.value = defaultSearchList
    } else {
      searchList.value = await getSearchResult(newValue)
    }
  },
  { debounce: 500, maxWait: 1000 }
)

const isSearching = ref(false)
const getSearchResult = async (searchQuery: string) => {
  try {
    isSearching.value = true
    const formData = new FormData()
    formData.append('action', 'msw_search_stocks')
    formData.append('query', searchQuery)
    const result = await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data?.data || [])
    console.log(result)
    return result
  } catch (error) {
    console.log(error)
  } finally {
    isSearching.value = false
  }
}

const isOpen = ref(false)

function closeModal() {
  isOpen.value = false
}
const searchInput = ref<HTMLInputElement | null>(null)
async function openModal() {
  isOpen.value = true
  await nextTick()
  if (searchInput.value) {
    searchInput.value.focus()
  }
}
</script>
