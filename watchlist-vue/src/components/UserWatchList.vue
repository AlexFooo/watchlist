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
                </Transition>
              </template>
            </WatchListStock>
          </template>
        </div>
      </Transition>
    </div>
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

const userId = window.userId || null

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
</script>
