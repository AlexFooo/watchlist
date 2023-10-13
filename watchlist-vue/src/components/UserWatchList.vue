<template>
  <div class="mx-auto px-4">
    <div class="wathhlist-top-bar bg-slate-100 overflow-hidden mt-1">
      <Transition mode="out-in" enter-active-class="transform transition duration-150 ease-out"
        enter-from-class="-translate-y-8 opacity-0" enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transform transition duration-150 ease-in" leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-8 opacity-0">
        <div v-if="isAdding" class="bg-blue-50 flex gap-2">
          <button @click="toggleAddMenu" class="p-3">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z" fill="#42474E" />
            </svg>
          </button>
          <input type="text" v-model="searchQuery" class="w-full bg-transparent px-4 py-2" placeholder="Search" />
        </div>
        <div v-else-if="isSorting" class="bg-blue-50 flex gap-2">
          <button @click="toggleSorting" class="p-3 hover:bg-blue-100 aspect-square">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z" fill="#42474E" />
            </svg>
          </button>
        </div>
        <div v-else-if="isEditing" class="flex gap-2 justify-between">
          <button @click="cancelEditing" class="p-3 hover:bg-blue-100 aspect-square">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z" fill="#42474E" />
            </svg>
          </button>
          <button @click="applyEditingChanges" class="p-3 hover:bg-blue-100 aspect-square">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9.00016 16.17L4.83016 12L3.41016 13.41L9.00016 19L21.0002 7L19.5902 5.59L9.00016 16.17Z"
                fill="black" />
            </svg>
          </button>
        </div>
        <div v-else class="flex gap-2">
          <button @click="toggleAddMenu" class="p-3 hover:bg-blue-100 aspect-square">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 13H13V20H11V13H4V11H11V4H13V11H20V13Z" fill="#3F3F3F" />
            </svg>
          </button>
          <button @click="toggleSorting" class="p-3 hover:bg-blue-100 aspect-square">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M23 15L21.59 13.59L19 16.17V5H17V16.17L14.41 13.59L13 15L18 20L23 15Z" fill="#3F3F3F" />
              <path d="M11 9L9.59 10.41L7 7.83V19H5V7.83L2.41 10.41L1 9L6 4L11 9Z" fill="#3F3F3F" />
            </svg>
          </button>
          <button @click="openEditing" class="p-3 hover:bg-blue-100 aspect-square">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M19.06 3.59L20.41 4.94C21.2 5.72 21.2 6.99 20.41 7.77L7.18 21H3V16.82L13.4 6.41L16.23 3.59C17.01 2.81 18.28 2.81 19.06 3.59ZM5 19L6.41 19.06L16.23 9.23L14.82 7.82L5 17.64V19Z"
                fill="#3F3F3F" />
            </svg>
          </button>
        </div>
      </Transition>
    </div>
    <Transition mode="out-in" enter-active-class="transform transition duration-300 ease-out"
      enter-from-class="translate-y-40 opacity-0" enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transform transition duration-150 ease-in" leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-96 opacity-0">
      <AddingList v-if="isAdding" :searchQuery="searchQuery" v-model:user-stocks-symbols="userStocksSymbols" />
      <SortingList v-else-if="isSorting" @update-sort-field="updateSortField" :sort-field="sortField" />
      <div v-else class="watchlist-list">
        <Transition mode="out-in" enter-active-class="transform transition duration-300 ease-out"
          enter-from-class="-translate-y-8 opacity-0" enter-to-class="translate-y-0 opacity-100"
          leave-active-class="transform transition duration-150 ease-in" leave-from-class="translate-y-0 opacity-100"
          leave-to-class="-translate-y-8 opacity-0">
          <div v-if="isLoading" class="w-full py-8 flex items-center justify-center">
            <svg aria-hidden="true" class="w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
              viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                fill="currentColor" />
              <path
                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                fill="currentFill" />
            </svg>
            <span class="sr-only">Loading...</span>
          </div>

          <draggable v-else v-model="userStocksToShow" group="stocks" item-key="symbol" handle=".handle">
            <template #header>
              <div class="flex gap-2 py-3">
                <button :class="stockType === selectedStocksType ? 'bg-red-600 text-white' : 'text-slate-500'
                  " class="px-5 py-2 text-xs border font-bold transition-all" v-for="stockType in stockTypes"
                  :key="stockType" @click="selectedStocksType = stockType">
                  {{ stockType }}
                </button>
              </div>
              <div
                class="px-4 py-3 border-y md:flex hidden gap-2 md:gap-20 md:items-center font-sans text-xs font-normal leading-none text-[#5E5E5E]">
                <div class="md:flex w-60">COMPANY</div>
                <div class="md:flex md:gap-10 md:justify-between w-full md:pr-20">
                  <div class="md:flex md:w-20">LAST</div>
                  <div class="md:w-24 md:flex">CHANGE</div>
                  <div class="md:w-24 md:flex">CHANGE %</div>
                  <div class="md:w-24 md:flex">MARKET CAP</div>
                  <div class=""></div>
                </div>
              </div>
              <div v-if="userStocks.length === 0">
                <h2 class="text-lg text-center py-4 text-slate-500">No stocks added</h2>
                <button @click="toggleAddMenu" v-if="!isEditing" class="px-4 py-3 bg-blue-800 text-white w-full">
                  Add stock to your watch list
                </button>
              </div>
            </template>
            <template #item="{ element: stock }">
              <WatchListStock :stock="stock">
                <template #left>
                  <Transition mode="out-in" enter-active-class="transform transition duration-300 ease-out"
                    enter-from-class="-translate-x-8 opacity-0" enter-to-class="translate-x-0 opacity-100"
                    leave-active-class="transform transition duration-150 ease-in"
                    leave-from-class="translate-x-0 opacity-100" leave-to-class="-translate-x-8 opacity-0">
                    <button class="p-3 hover:bg-blue-100 aspect-square" v-if="isEditing" @click="removeUserStock(stock)">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="24" fill="white" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M5.45391 5.45383C5.85337 5.05437 6.50102 5.05437 6.90048 5.45383L12.0001 10.5534L17.0996 5.45386C17.4991 5.0544 18.1467 5.0544 18.5462 5.45386C18.9457 5.85332 18.9457 6.50097 18.5462 6.90043L13.4466 12L18.5462 17.0996C18.9457 17.499 18.9457 18.1467 18.5462 18.5461C18.1468 18.9456 17.4991 18.9456 17.0997 18.5461L12.0001 13.4466L6.90046 18.5462C6.501 18.9456 5.85335 18.9456 5.45389 18.5462C5.05443 18.1467 5.05443 17.4991 5.45389 17.0996L10.5535 12L5.45391 6.90039C5.05445 6.50094 5.05445 5.85329 5.45391 5.45383Z"
                          fill="#FF3A3A" />
                      </svg>
                    </button>
                  </Transition>
                </template>
                <template #right>
                  <Transition mode="out-in" enter-active-class="transform transition duration-300 ease-out"
                    enter-from-class="-translate-y-8 opacity-0" enter-to-class="translate-y-0 opacity-100"
                    leave-active-class="transform transition duration-150 ease-in"
                    leave-from-class="translate-y-0 opacity-100" leave-to-class="-translate-y-8 opacity-0">
                    <div v-if="isEditing" class="watchlist-item-right flex flex-wrap justify-end">
                      <div class="handle">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <rect width="24" height="24" fill="white" />
                          <rect width="2.04575" height="18.5153" rx="1.02288"
                            transform="matrix(-4.37114e-08 1 1 4.37114e-08 2.74219 7.95422)" fill="#A6A6A6" />
                          <rect width="2.04575" height="18.5153" rx="1.02288"
                            transform="matrix(-4.37114e-08 1 1 4.37114e-08 2.74219 14)" fill="#A6A6A6" />
                        </svg>
                      </div>
                    </div>
                    <div v-else class="watchlist-item-right md:w-full">
                      <div class="flex md:flex-nowrap flex-wrap md:items-center justify-end md:justify-between md:gap-10">
                        <div
                          class="item-price w-full md:w-20 flex justify-end md:justify-start font-sans text-base font-semibold text-black leading-none">
                          ${{ stock.price }}
                        </div>
                        <div v-if="stock.change" class="item-change md:w-24 font-sans text-sm font-normal leading-none"
                          :class="stock.change > 0 ? 'text-[#12A600]' : 'text-[#FF3A3A]'">
                          ${{ stock.change.toFixed(2) }}
                        </div>
                        <div v-if="stock.change_percent"
                          class="item-change-percent md:w-24 ml-1 font-sans text-sm font-normal leading-none"
                          :class="stock.change > 0 ? 'text-[#12A600]' : 'text-[#FF3A3A]'">
                          {{ stock.change_percent.toFixed(2) }}%
                        </div>
                        <div class="md:flex hidden md:w-24">
                          {{ formatter.format(stock.market_cap) }}
                        </div>
                        <div>
                          <a class="px-5 py-2 md:flex hidden text-white bg-[#12A600] font-sans text-xs font-bold rounded">
                            TRADE</a>
                        </div>
                      </div>
                    </div>
                  </Transition>
                </template>
              </WatchListStock>
            </template>
          </draggable>
        </Transition>
      </div>
    </Transition>
  </div>
</template>
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import WatchListStock from './WatchListStock.vue'
import AddingList from './AddingList.vue'
import axios from 'axios'
import type { Stock } from '@/types'
import { watchDebounced } from '@vueuse/core'
import draggable from 'vuedraggable'
import SortingList from './SortingList.vue'

export interface Props {
  userStocksSymbolsString: string
}
const props = defineProps<Props>()

const userId = window.userId || 2

const userStocksSymbols = ref<string[]>(props.userStocksSymbolsString.split(',') || [])
const userStocks = ref<Stock[]>([])

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

onMounted(async () => {
  userStocks.value = await getUserStocks(props.userStocksSymbolsString)
})
watchDebounced(
  () => userStocksSymbols.value,
  async (newValue) => {
    await saveUserStocksString()
    userStocks.value = await getUserStocks(newValue.join(',') || '')
  },
  { debounce: 500, maxWait: 1000 }
)

const searchQuery = ref('')

const isAdding = ref(false)
const toggleAddMenu = () => {
  isAdding.value = !isAdding.value
}

const isLoading = ref(true)
const getUserStocks = async (userStocksSymbolsString: string): Promise<Stock[]> => {
  try {
    isLoading.value = true
    const formData = new FormData()
    formData.append('action', 'watchlist_letizo_get_stocks_data')
    // formData.append('symbols_string', userStocksSymbolsString)
    formData.append('user_id', userId)

    const result = await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data?.stocks_data || [])
    console.log(result)

    return result
  } catch (error) {
    console.log(error)
    return []
  } finally {
    isLoading.value = false
  }
}

const isEditing = ref(false)
const isSorting = ref(false)
const sortField = ref('custom')
const cachedUserStocks = ref<Stock[]>([])

const openEditing = () => {
  cachedUserStocks.value = JSON.parse(JSON.stringify(userStocks.value))
  isEditing.value = true
}

const cancelEditing = () => {
  userStocks.value = cachedUserStocks.value
  isEditing.value = false
}
const applyEditingChanges = async () => {
  isEditing.value = false
  userStocksSymbols.value = userStocks.value.map((s) => s.symbol)
  // userStocks.value = await getUserStocks(userStocksSymbols.value.join(',') || '')
}

const removeUserStock = (stock: Stock) => {
  userStocks.value = userStocks.value.filter((s) => s.symbol !== stock.symbol)
}

const isSaving = ref(false)
const saveUserStocksString = async () => {
  try {
    isLoading.value = true
    isSaving.value = true
    const formData = new FormData()
    formData.append('action', 'watchlist_letizo_save_stocks_data_by_user_id')
    formData.append('symbols_string', userStocksSymbols.value.join(',') || '')
    formData.append('user_id', userId)
    const result = await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data?.stocks_data || [])
    console.log(result)

    return result
  } catch (error) {
    console.log(error)
    return []
  } finally {
    isSaving.value = false
    isLoading.value = true
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

const toggleSorting = () => {
  isSorting.value = !isSorting.value
}

const updateSortField = (value: string) => {
  sortField.value = value
  isSorting.value = false
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
</script>
