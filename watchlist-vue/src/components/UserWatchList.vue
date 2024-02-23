<template>
  <div class="mx-auto">
    <div class="watchlist-list">
      <div class="flex items-center flex-wrap flex-col md:flex-row-reverse">
        <div class="flex gap-2 md:justify-end w-full md:w-fit flex-1 bg-slate-50 md:bg-transparent">
          <AddingListButton
            v-if="userStocksSymbols"
            v-model:user-stocks-symbols="userStocksSymbols"
          />
          <SortingListButton @update-sort-field="updateSortField" :sort-field="sortField" />
          <EditingListButton :user-stocks="userStocks" @update-user-stocks="setUserStocks" />
        </div>
        <div class="flex gap-2 py-3 w-full md:w-fit">
          <button
            :class="stockType === selectedStocksType ? 'bg-red-600 text-white' : 'text-slate-500'"
            class="px-5 py-2 text-xs border font-bold transition-all"
            v-for="stockType in stockTypes"
            :key="stockType"
            @click="selectedStocksType = stockType"
          >
            {{ stockType === 'EQUITY' ? 'STOCKS' : stockType }}
          </button>
        </div>
      </div>
      <Transition
        mode="out-in"
        enter-active-class="transform transition duration-300 ease-out"
        enter-from-class="-translate-y-8 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transform transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="-translate-y-8 opacity-0"
      >
        <div
          v-if="isLoading && userStocks.length === 0"
          class="w-full py-8 flex items-center justify-center"
        >
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
          <span class="sr-only">Loading...</span>
        </div>

        <div v-else class="relative">
          <Transition
            mode="out-in"
            enter-active-class="transform transition duration-300 ease-out"
            enter-from-class=" opacity-0"
            enter-to-class=" opacity-100"
            leave-active-class="transform transition duration-150 ease-in"
            leave-from-class=" opacity-100"
            leave-to-class=" opacity-0"
          >
            <div
              v-if="isLoading"
              class="absolute inset-0 bg-white/10 w-full py-8 flex items-center justify-center"
            >
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
              <span class="sr-only">Loading...</span>
            </div>
          </Transition>
          <div
            class="py-3 border-y md:flex hidden gap-2 md:gap-20 md:items-center font-sans text-xs font-normal leading-none text-[#5E5E5E]"
          >
            <div class="md:flex w-60">COMPANY</div>
            <div class="md:flex md:gap-10 md:justify-between w-full md:pr-4">
              <div class="md:flex md:w-20">LAST</div>
              <div class="md:w-24 md:flex">CHANGE</div>
              <div class="md:w-24 md:flex">CHANGE %</div>
              <div class="md:w-24 md:flex">MARKET CAP</div>
              <div class="md:w-24"></div>
            </div>
          </div>
          <div v-if="userStocks.length === 0">
            <h2 class="text-lg text-center py-4 text-slate-500">No stocks added</h2>
          </div>
          <template v-for="stock in userStocksToShow" :key="stock.id">
            <WatchListStock :stock="stock">
              <template #right>
                <Transition
                  mode="out-in"
                  enter-active-class="transform transition duration-300 ease-out"
                  enter-from-class="-translate-y-8 opacity-0"
                  enter-to-class="translate-y-0 opacity-100"
                  leave-active-class="transform transition duration-150 ease-in"
                  leave-from-class="translate-y-0 opacity-100"
                  leave-to-class="-translate-y-8 opacity-0"
                >
                  <div class="watchlist-item-right md:w-full">
                    <div
                      class="flex md:flex-nowrap flex-wrap md:items-center justify-end md:justify-between md:gap-10"
                    >
                      <div
                        class="item-price w-full md:w-20 flex justify-end md:justify-start font-sans text-base font-semibold text-black leading-none"
                      >
                        ${{ stock.price }}
                      </div>
                      <div
                        class="item-change md:w-24 font-sans text-sm font-normal leading-none"
                        :class="
                          stock.change !== undefined && stock.change > 0
                            ? 'text-[#12A600]'
                            : 'text-[#FF3A3A]'
                        "
                      >
                        ${{ stock.change !== undefined ? stock.change.toFixed(2) : '-' }}
                      </div>
                      <div
                        class="item-change-percent md:w-24 ml-1 font-sans text-sm font-normal leading-none"
                        :class="stock.change > 0 ? 'text-[#12A600]' : 'text-[#FF3A3A]'"
                      >
                        {{
                          stock.change_percent !== undefined
                            ? stock.change_percent.toFixed(2) + '%'
                            : '-'
                        }}
                      </div>

                      <div class="md:flex hidden md:w-24">
                        {{ stock.market_cap ? formatter.format(stock.market_cap) : '-' }}
                      </div>
                      <div class="trade-referal-link md:w-28">
                        <a
                          v-if="startTradingButtonLink"
                          :href="startTradingButtonLink"
                          class="bg-green-600 cursor-pointer hover:bg-green-700 transition-colors px-2 py-1 text-white text-xs uppercase font-bold truncate"
                          target="_blank"
                          >Start trading</a
                        >
                      </div>
                      
                    </div>
                    
                  </div>
                  <button
                        @click="openPriceAlertSettings(stock)"
                        class="hover:bg-gray-100 rounded-full p-2 transition-colors"
                      >
                        <svg
                          width="24"
                          height="24"
                          viewBox="0 0 24 24"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <rect width="24" height="24" rx="12" />
                          <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M12.0001 4.71094C11.4033 4.71096 10.9196 5.20091 10.9196 5.80526V6.21873C10.87 6.26897 10.7963 6.3052 10.6883 6.34383L10.6733 6.34922C10.5842 6.38099 10.4949 6.41284 10.4063 6.44658L10.3242 6.47853C10.2417 6.51145 10.1602 6.5465 10.0807 6.58517C9.15166 7.03697 8.42694 7.69375 8.12578 8.72459C8.02744 9.06138 7.9924 9.42545 7.98396 9.77861C7.97172 10.2936 7.97067 10.8087 7.96961 11.3238C7.96856 11.7779 7.9675 12.2321 7.95885 12.6862C7.94851 13.2316 7.71784 13.6797 7.27929 14.0093L7.11616 14.1319L6.95724 14.2516L6.86185 14.3238C6.61873 14.5092 6.44103 14.7419 6.34121 15.0365C6.1384 15.6369 6.36653 16.4673 7.33269 16.4505C8.42694 16.4316 9.52162 16.4357 10.6176 16.4398L10.6226 16.4398C11.0819 16.4415 11.5411 16.4432 12.0001 16.4432V4.71094ZM13.378 16.4398C12.9188 16.4415 12.4595 16.4432 12.0005 16.4432V4.71094C12.5973 4.71096 13.0808 5.20091 13.0808 5.80526V6.21873C13.1304 6.26897 13.2041 6.3052 13.3121 6.34383L13.3273 6.34922C13.444 6.39082 13.561 6.43259 13.6762 6.47853C13.7587 6.51145 13.8402 6.5465 13.9197 6.58517C14.8487 7.03697 15.5735 7.69375 15.8746 8.72459C15.973 9.06138 16.008 9.42545 16.0164 9.77861C16.0287 10.2936 16.0297 10.8087 16.0308 11.3238C16.0318 11.7779 16.0329 12.2321 16.0416 12.6862C16.0519 13.2316 16.2826 13.6797 16.7211 14.0093L16.8842 14.1319C16.9691 14.1957 17.0539 14.2595 17.1386 14.3238C17.3817 14.5092 17.5594 14.7419 17.6592 15.0365C17.862 15.6369 17.6339 16.4673 16.6677 16.4505C15.5735 16.4316 14.4788 16.4357 13.3831 16.4398L13.378 16.4398ZM13.095 18.9464C13.6507 18.5937 13.9733 18.0928 13.9818 17.397C12.6472 17.397 11.3397 17.397 10.019 17.3974C10.037 18.1138 10.374 18.6233 10.9544 18.9732C11.6688 19.4041 12.3918 19.3923 13.095 18.9464Z"
                            :fill="stock.price_alert ? '#3664ef' : '#D1D1D1'"
                          />
                        </svg>
                      </button>
                  
                </Transition>
              </template>
            </WatchListStock>
          </template>
        </div>
      </Transition>
    </div>
    <TransitionRoot appear :show="isAlertSettingsVisible" as="template">
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
                  <div>Set a Price Alert</div>

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
                <AlertSettings
                  v-if="stockToSetAlert"
                  v-model:stock="stockToSetAlert"
                  @alert-updated="alertUpdatedHandler"
                />
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import WatchListStock from './WatchListStock.vue'
import axios from 'axios'
import type { Stock } from '@/types'
import { useStorage, watchDebounced } from '@vueuse/core'
import SortingListButton from './SortingListButton.vue'
import AddingListButton from './AddingListButton.vue'
import EditingListButton from './EditingListButton.vue'
import AlertSettings from './AlertSettings.vue'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue'

const userId = window.userId || 484

const userStocksSymbols = ref<string[] | null>(null)
const userStocks = ref<Stock[]>([])
const startTradingButtonLink = computed<string | null>(() => window.startTradingButtonLink || null)

const userStocksToShow = computed({
  get() {
    let stocks = userStocks.value
    if (selectedStocksType.value !== 'All') {
      stocks = stocks.filter((s) => s.type === selectedStocksType.value)
    }
    return sortByField(stocks, sortField.value)
  },
  set(val) {
    userStocks.value = val
  }
})

watchDebounced(
  () => userStocksSymbols.value,
  async (newValue, oldValue) => {
    if (oldValue === null || !newValue) return
    saveUserStocksString(newValue.join(',') || '')
  },
  { debounce: 500, maxWait: 1000 }
)

const isLoading = ref(true)
const getUserStocks = async (): Promise<Stock[]> => {
  try {
    isLoading.value = true
    const formData = new FormData()
    formData.append('action', 'watchlist_letizo_get_stocks_data')
    if (userId) {
      formData.append('user_id', userId)
    } else {
      formData.append('symbols_string', localStorage.getItem('userStocksSymbols') || '')
    }

    const stocks = await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data?.stocks_data || [])
    // .catch((error) => {
    //   return [
    //     {
    //       symbol: 'TSLA',
    //       company_name: 'Tesla, Inc.',
    //       type: 'EQUITY',
    //       exchange: 'NMS',
    //       exchange_name: 'NasdaqGS',
    //       price: 188.86,
    //       currency: 'USD',
    //       open: 188.5,
    //       close: 187.29,
    //       low: 184.28,
    //       high: 189.87,
    //       previous_close: 187.29,
    //       change: 1.5700073,
    //       change_percent: 0.008382762,
    //       volume: 91171516,
    //       market_cap: 601479446528,
    //       '52_week_low': 152.37,
    //       '52_week_low_change': 36.490005,
    //       '52_week_low_change_percent': 0.23948288,
    //       '52_week_high': 299.29,
    //       '52_week_high_change': -110.43001,
    //       '52_week_high_change_percent': -0.36897326,
    //       shares_outstanding: 3184790016,
    //       eps_ttm: 4.31,
    //       dividend_yield_ta: 0,
    //       dividend_rate_ta: 0,
    //       last_update: 1706821200000,
    //       logo: 'https://letizo.com/wp-content/uploads/massive-stock-widgets/TSLA.svg',
    //       category: null
    //     },
    //     {
    //       symbol: 'AAPL',
    //       company_name: 'Apple Inc.',
    //       type: 'EQUITY',
    //       exchange: 'NMS',
    //       exchange_name: 'NasdaqGS',
    //       price: 186.86,
    //       currency: 'USD',
    //       open: 183.985,
    //       close: 184.4,
    //       low: 183.82,
    //       high: 186.95,
    //       previous_close: 184.4,
    //       change: 2.4600067,
    //       change_percent: 0.013340601,
    //       volume: 59458051,
    //       market_cap: 2889210658816,
    //       '52_week_low': 143.9,
    //       '52_week_low_change': 42.960007,
    //       '52_week_low_change_percent': 0.2985407,
    //       '52_week_high': 199.62,
    //       '52_week_high_change': -12.7599945,
    //       '52_week_high_change_percent': -0.06392142,
    //       shares_outstanding: 15461900288,
    //       eps_ttm: 6.13,
    //       dividend_yield_ta: 0.5097614,
    //       dividend_rate_ta: 0.94,
    //       last_update: 1706821201000,
    //       logo: 'https://letizo.com/wp-content/uploads/massive-stock-widgets/AAPL.svg',
    //       category: null
    //     },
    //     {
    //       symbol: 'GOOGL',
    //       company_name: 'Alphabet Inc.',
    //       type: 'EQUITY',
    //       exchange: 'NMS',
    //       exchange_name: 'NasdaqGS',
    //       price: 141.16,
    //       currency: 'USD',
    //       open: 142.12,
    //       close: 140.1,
    //       low: 140.79,
    //       high: 143.06,
    //       previous_close: 140.1,
    //       change: 1.0599976,
    //       change_percent: 0.007566005999999999,
    //       volume: 37611134,
    //       market_cap: 1763836559360,
    //       '52_week_low': 88.58,
    //       '52_week_low_change': 52.58,
    //       '52_week_low_change_percent': 0.5935877,
    //       '52_week_high': 153.78,
    //       '52_week_high_change': -12.619995,
    //       '52_week_high_change_percent': -0.082065254,
    //       shares_outstanding: 5893000192,
    //       eps_ttm: 5.8,
    //       dividend_yield_ta: 0,
    //       dividend_rate_ta: 0,
    //       last_update: 1706821201000,
    //       logo: 'https://letizo.com/wp-content/plugins/massive-stock-widgets/assets/public/img/placeholders/G.svg',
    //       category: null
    //     },
    //     {
    //       symbol: 'AMZN',
    //       company_name: 'Amazon.com, Inc.',
    //       type: 'EQUITY',
    //       exchange: 'NMS',
    //       exchange_name: 'NasdaqGS',
    //       price: 159.28,
    //       currency: 'USD',
    //       open: 155.87,
    //       close: 155.2,
    //       low: 155.62,
    //       high: 159.76,
    //       previous_close: 155.2,
    //       change: 4.080002,
    //       change_percent: 0.026288671,
    //       volume: 70640228,
    //       market_cap: 1645999554560,
    //       '52_week_low': 88.12,
    //       '52_week_low_change': 71.159996,
    //       '52_week_low_change_percent': 0.8075351,
    //       '52_week_high': 161.73,
    //       '52_week_high_change': -2.449997,
    //       '52_week_high_change_percent': -0.015148686,
    //       shares_outstanding: 10334000128,
    //       eps_ttm: 2.9,
    //       dividend_yield_ta: 0,
    //       dividend_rate_ta: 0,
    //       last_update: 1706821201000,
    //       logo: 'https://letizo.com/wp-content/uploads/massive-stock-widgets/AMZN.svg',
    //       category: null
    //     }
    //   ]
    // })

    userStocks.value = stocks

    return stocks
  } catch (error) {
    console.log(error)
    return []
  } finally {
    isLoading.value = false
  }
}

onMounted(async () => {
  if (!userId && localStorage.getItem('userStocksSymbols') === null) {
    await getDefaultStocks()
  } else {
    await getUserStocks()
  }
  userStocksSymbols.value = userStocks.value.map((s) => s.symbol)
})

const sortField = useStorage('d12edf32esvbwe', 'custom')

const isSaving = ref(false)
const saveUserStocksString = async (userStocksSymbolsString?: string) => {
  try {
    if (userId) {
      isSaving.value = true
      const formData = new FormData()
      formData.append('action', 'watchlist_letizo_save_stocks_data_by_user_id')
      formData.append(
        'symbols_string',
        userStocksSymbolsString || userStocksSymbols.value.join(',') || ''
      )
      formData.append('user_id', userId)
      const stocks = await axios
        .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
        .then((data) => data.data?.stocks_data || [])

      userStocks.value = stocks
      return stocks
    } else {
      localStorage.setItem(
        'userStocksSymbols',
        userStocksSymbolsString || userStocksSymbols.value?.join(',') || ''
      )
      getUserStocks()
    }
  } catch (error) {
    console.log(error)
    return []
  } finally {
    isSaving.value = false
  }
}

const stockTypes = computed(() => {
  const userStocksTypes = new Set<string>()
  userStocksTypes.add('All')

  userStocks.value.forEach((stock) => {
    userStocksTypes.add(stock.type)
  })
  return Array.from(userStocksTypes)
})
const selectedStocksType = ref<string>(stockTypes.value[0])

let formatter = Intl.NumberFormat('en', { notation: 'compact', maximumSignificantDigits: 6 })

const updateSortField = (value: string) => {
  sortField.value = value
}

function sortByField<T>(arr: T[], fieldName: string): T[] {
  return arr.slice().sort((a, b) => {
    if (typeof a[fieldName] === 'string' && typeof b[fieldName] === 'string') {
      // Sort strings alphabetically
      return a[fieldName].localeCompare(b[fieldName])
    } else if (typeof a[fieldName] === 'number' && typeof b[fieldName] === 'number') {
      // Sort numbers in descending order
      return b[fieldName] - a[fieldName]
    }
    return 0
  })
}

const setUserStocks = (value: Stock[]) => {
  userStocks.value = value
  userStocksSymbols.value = userStocks.value.map((s) => s.symbol)
}

const getDefaultStocks = async (): Promise<Stock[]> => {
  try {
    isLoading.value = true
    const formData = new FormData()
    formData.append('action', 'watchlist_get_default_stocks_data')

    const stocks = await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data?.stocks_data || [])

    userStocks.value = stocks

    return stocks
  } catch (error) {
    console.log(error)
    return []
  } finally {
    isLoading.value = false
  }
}
const isAlertSettingsVisible = ref(false)
const stockToSetAlert = ref<Stock | null>(null)

function closeModal() {
  isAlertSettingsVisible.value = false
}

const openPriceAlertSettings = (stock: Stock) => {
  stockToSetAlert.value = stock
  isAlertSettingsVisible.value = true
}

const alertUpdatedHandler = (stock: Stock) => {
  closeModal()
  getUserStocks()
}
</script>
