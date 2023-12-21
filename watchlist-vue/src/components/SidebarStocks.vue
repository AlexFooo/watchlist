<template>
  <div class="mx-auto max-w-3xl shadow-md">
    <div class="watchlist-list">
      <Transition
        mode="out-in"
        enter-active-class="transform transition duration-300 ease-out"
        enter-from-class="translate-y-0 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transform transition duration-150 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-0 opacity-0"
      >
        <div
          v-if="isLoading && userStocks.length === 0"
          class="w-full py-8 flex items-center justify-center min-h-[24rem]"
        >
          <svg
            aria-hidden="true"
            class="w-10 h-10 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-red-600"
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
          <div class="flex items-center flex-wrap flex-col bg-gray-100">
            <div class="flex justify-between w-full">
              <button
                :class="
                  stockType === selectedStocksType
                    ? 'text-red-600 '
                    : 'text-slate-500 hover:text-slate-700'
                "
                class="flex-1 py-4 text-xs font-bold transition-all uppercase"
                v-for="stockType in stockTypes"
                :key="stockType"
                @click="selectedStocksType = stockType"
              >
                {{ stockType }}
              </button>
            </div>
          </div>
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
              class="absolute inset-0 bg-white/10 w-full py-8 flex items-center justify-center mih-h-[20rem]"
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

          <template v-for="stock in userStocksToShow" :key="stock.id">
            <a :href="`/${stock.slug}/${stock.symbol}`">
              <WatchListStock :stock="stock" class="hover:bg-slate-50 transition-colors px-4">
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
                    <div class="watchlist-item-right">
                      <div class="flex flex-wrap justify-end">
                        <div
                          class="item-price w-full flex justify-end font-sans text-base font-semibold text-black leading-none"
                        >
                          ${{ stock.price }}
                        </div>
                        <div
                          class="item-change font-sans text-sm font-normal leading-none"
                          :class="
                            stock.change !== undefined && stock.change > 0
                              ? 'text-[#12A600]'
                              : 'text-[#FF3A3A]'
                          "
                        >
                          ${{ stock.change !== undefined ? stock.change.toFixed(2) : '-' }}
                        </div>
                        <div
                          class="item-change-percent ml-1 font-sans text-sm font-normal leading-none"
                          :class="stock.change > 0 ? 'text-[#12A600]' : 'text-[#FF3A3A]'"
                        >
                          {{
                            stock.change_percent !== undefined
                              ? stock.change_percent.toFixed(2) + '%'
                              : '-'
                          }}
                        </div>
                      </div>
                    </div>
                  </Transition>
                </template>
              </WatchListStock>
            </a>
          </template>
          <div v-if="isError" class="p-4 text-red-600 bg-white font-bold">Error</div>
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

const userStocks = ref<Stock[]>([])

const userStocksToShow = computed({
  get() {
    let stocks = userStocks.value
    // if (selectedStocksType.value !== 'All') {
    stocks = stocks.filter((s) => s.category === selectedStocksType.value)
    // }
    // return sortByField(stocks, sortField.value)
    return stocks
  },
  set(val) {
    userStocks.value = val
  }
})

const isLoading = ref(true)
const isError = ref(false)
const getSidebarStocks = async (): Promise<Stock[]> => {
  try {
    isLoading.value = true
    const formData = new FormData()
    // formData.append('action', 'watchlist_get_sidebar_stocks')
    // formData.append('action', 'watchlist_get_default_stocks_data')

    formData.append('action', 'watchlist_get_sidebar_stocks')
    // formData.append('user_id', '2')
    const stocks = await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data?.stocks_data || [])

    userStocks.value = stocks

    return stocks
  } catch (error) {
    console.log(error)
    isError.value = true
    return []
  } finally {
    isLoading.value = false
  }
}
onMounted(() => {
  getSidebarStocks()
})

const stockTypes = computed(() => {
  const userStocksTypes = new Set<string>()
  //   userStocksTypes.add('All')

  userStocks.value.forEach((stock) => {
    // userStocksTypes.add(stock.category)
    userStocksTypes.add(stock.category)
  })
  return Array.from(userStocksTypes)
})
const selectedStocksType = ref<string>('stocks')
</script>
