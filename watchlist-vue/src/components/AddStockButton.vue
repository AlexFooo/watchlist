<template>
  <div class="flex gap-1 items-center h-10">
    <button
      @click="buttonClickHandler"
      class="relative px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-md h-full text-sm transition-all w-fit"
      :class="{ 'text-transparent': isLoading }"
    >
      {{ added ? 'Added' : 'Add to Watchlist' }}
    </button>
    <button
      ref="menu"
      @click="isMenuOpen = !isMenuOpen"
      class="hover:bg-slate-100 aspect-square flex items-center justify-center relative p-2 rounded-md h-full"
      :class="{ 'bg-slate-100': isMenuOpen }"
    >
      <svg
        width="32"
        height="32"
        viewBox="0 0 32 32"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <circle cx="16" cy="9.5" r="1.75" fill="#8E8E8E" />
        <circle cx="16" cy="16" r="1.75" fill="#8E8E8E" />
        <circle cx="16" cy="22.5" r="1.75" fill="#8E8E8E" />
      </svg>
      <div
        v-if="isMenuOpen"
        class="absolute top-[calc(100%+0.3rem)] bg-white shadow-lg right-0 rounded-lg overflow-hidden border border-slate-100 z-50"
      >
        <a
          class="py-2 px-3 hover:bg-slate-100 flex gap-2 items-center w-full truncate"
          href="/watchlist/"
          ><svg
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M6.59062 0.685059C4.54555 0.685059 2.8877 2.34292 2.8877 4.38798L2.8877 22.0435C2.8877 23.028 3.95971 23.6379 4.80598 23.1348L12.0003 18.8583L19.1947 23.1348C20.041 23.6379 21.113 23.028 21.113 22.0435L21.113 4.38798C21.113 2.34292 19.4551 0.685059 17.41 0.685059H6.59062ZM10.8225 6.40149C10.8225 5.75097 11.3498 5.22362 12.0003 5.22362C12.6509 5.22362 13.1782 5.75097 13.1782 6.40149V8.5705H15.3472C15.9977 8.5705 16.5251 9.09785 16.5251 9.74836C16.5251 10.3989 15.9977 10.9262 15.3472 10.9262H13.1782V13.0952C13.1782 13.7457 12.6509 14.2731 12.0003 14.2731C11.3498 14.2731 10.8225 13.7457 10.8225 13.0952V10.9262H8.65346C8.00295 10.9262 7.4756 10.3989 7.4756 9.74836C7.4756 9.09785 8.00295 8.5705 8.65346 8.5705H10.8225V6.40149Z"
              fill="#E41E15"
            />
          </svg>
          <span> Go to Watchlist</span></a
        >
        <button
          v-if="added"
          @click="removeStock"
          class="py-2 px-3 hover:bg-slate-100 flex gap-2 items-center w-full"
        >
          <svg
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M12 10.0239L8.38533 6.40926C7.83965 5.86358 6.95494 5.86358 6.40926 6.40926C5.86358 6.95494 5.86358 7.83966 6.40926 8.38533L10.0239 12L6.40926 15.6147C5.86358 16.1603 5.86358 17.0451 6.40926 17.5907C6.95494 18.1364 7.83965 18.1364 8.38533 17.5907L12 13.9761L15.6147 17.5907C16.1603 18.1364 17.0451 18.1364 17.5907 17.5907C18.1364 17.0451 18.1364 16.1603 17.5907 15.6147L13.9761 12L17.5907 8.38533C18.1364 7.83966 18.1364 6.95494 17.5907 6.40926C17.0451 5.86358 16.1603 5.86358 15.6147 6.40926L12 10.0239Z"
              fill="#828282"
            />
          </svg>
          <span>Remove</span>
        </button>
      </div>
    </button>
  </div>
</template>
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import type { Stock } from '@/types'
import { onClickOutside } from '@vueuse/core'

export interface Props {
  stockSymbol: string
}
const props = defineProps<Props>()

const isMenuOpen = ref(false)
const menu = ref(null)

onClickOutside(menu, (event) => (isMenuOpen.value = false))
const userStocksSymbols = ref<string[] | null>(null)

const added = computed(() => {
  return userStocksSymbols.value?.includes(props.stockSymbol) || false
})
const userId = window.userId || null

const isLoading = ref(true)
const setUserStocksSymbols = async (): Promise<void> => {
  try {
    if (!userId) {
      userStocksSymbols.value = localStorage.getItem('userStocksSymbols')?.split(',') || []
    } else {
      isLoading.value = true
      const formData = new FormData()
      formData.append('action', 'watchlist_letizo_get_stocks_data')
      formData.append('user_id', userId)

      const stocks = await axios
        .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
        .then((data) => data.data?.stocks_data || [])
      userStocksSymbols.value = stocks.map((s: Stock) => s.symbol)
    }
  } catch (error) {
    console.log(error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  setUserStocksSymbols()
})

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

      return stocks
    } else {
      localStorage.setItem(
        'userStocksSymbols',
        userStocksSymbolsString || userStocksSymbols.value?.join(',') || ''
      )
    }
  } catch (error) {
    console.log(error)
    return []
  } finally {
    isSaving.value = false
  }
}

const buttonClickHandler = async () => {
  if (added.value) {
    userStocksSymbols.value = userStocksSymbols.value?.filter((s) => s !== props.stockSymbol) || []
  } else {
    userStocksSymbols.value?.push(props.stockSymbol)
  }
  saveUserStocksString(userStocksSymbols.value?.join(','))
}

const removeStock = async () => {
  userStocksSymbols.value = userStocksSymbols.value?.filter((s) => s !== props.stockSymbol) || []
  saveUserStocksString(userStocksSymbols.value?.join(','))
}
</script>
