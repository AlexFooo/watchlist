<template>
  <Menu as="div" class="relative inline-block text-left">
    <MenuButton
      class="px-4 py-2 hover:bg-black/10 rounded-full transition-colors flex items-center gap-2 text-slate-500"
      @click="readNotifications"
    >
      <div class="relative">
        <svg
          width="31"
          height="27"
          viewBox="0 0 31 27"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M12.0001 7.71118C11.4033 7.71121 10.9196 8.20115 10.9196 8.8055V9.21897C10.87 9.26921 10.7963 9.30544 10.6883 9.34407L10.6733 9.34947C10.5842 9.38124 10.4949 9.41308 10.4063 9.44682L10.3242 9.47878C10.2417 9.51169 10.1602 9.54675 10.0807 9.58542C9.15166 10.0372 8.42694 10.694 8.12578 11.7248C8.02744 12.0616 7.9924 12.4257 7.98396 12.7789C7.97172 13.2938 7.97067 13.8089 7.96961 14.324C7.96856 14.7782 7.9675 15.2323 7.95885 15.6864C7.94851 16.2318 7.71784 16.68 7.27929 17.0095L7.11616 17.1322L6.95724 17.2519L6.86185 17.324C6.61873 17.5095 6.44103 17.7422 6.34121 18.0367C6.1384 18.6371 6.36653 19.4675 7.33269 19.4507C8.42694 19.4319 9.52162 19.4359 10.6176 19.44L10.6226 19.44C11.0819 19.4417 11.5411 19.4434 12.0001 19.4434V7.71118ZM13.378 19.44C12.9188 19.4417 12.4595 19.4434 12.0005 19.4434V7.71118C12.5973 7.71121 13.0808 8.20115 13.0808 8.8055V9.21897C13.1304 9.26921 13.2041 9.30544 13.3121 9.34407L13.3273 9.34947C13.444 9.39107 13.561 9.43284 13.6762 9.47878C13.7587 9.51169 13.8402 9.54675 13.9197 9.58542C14.8487 10.0372 15.5735 10.694 15.8746 11.7248C15.973 12.0616 16.008 12.4257 16.0164 12.7789C16.0287 13.2938 16.0297 13.8089 16.0308 14.324C16.0318 14.7782 16.0329 15.2323 16.0416 15.6864C16.0519 16.2318 16.2826 16.68 16.7211 17.0095L16.8842 17.1322C16.9691 17.196 17.0539 17.2597 17.1386 17.324C17.3817 17.5095 17.5594 17.7422 17.6592 18.0367C17.862 18.6371 17.6339 19.4675 16.6677 19.4507C15.5735 19.4319 14.4788 19.4359 13.3831 19.44L13.378 19.44ZM13.095 21.9467C13.6507 21.594 13.9733 21.0931 13.9818 20.3972C12.6472 20.3972 11.3397 20.3972 10.019 20.3977C10.037 21.114 10.374 21.6235 10.9544 21.9735C11.6688 22.4044 12.3918 22.3926 13.095 21.9467Z"
            fill="#3F3F3F"
          />
        </svg>
        <div
          v-if="notificationsCount"
          class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-4 h-4 text-xs px-0.5"
        >
          {{ notificationsCount < 9 ? notificationsCount : '9+' }}
        </div>
      </div>
    </MenuButton>

    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <MenuItems
        class="fixed md:absolute right-0 mt-2 w-full md:w-96 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-40 max-h-[50vh] overflow-y-auto"
      >
        <div class="">
          <div
            v-for="alertNotification in alertNotifications"
            :key="alertNotification.id"
            class="hover:bg-slate-50 transition-colors"
            :class="{
              'bg-blue-50': !alertNotification.read
            }"
          >
            <!-- <div class="px-4 pt-1 text-sm">
              The stock hit your desired price {{ useTimeAgo(alertNotification.created_at) }}
            </div> -->
            <div class="">
              <WatchListStock :stock="alertNotification" class="">
                <template #afterSymbol>
                  <span class="font-normal">- just hit</span>
                </template>
                <template #right>
                  <div
                    class="flex flex-col justify-between items-center bg-slate-200 px-3 py-1 rounded-lg"
                  >
                    <div class="font-bold text-slate-600">
                      ${{ alertNotification.desired_price }}
                    </div>
                  </div>
                </template>
                <template #underSymbol>
                  <div class="font-normal mt-1">{{ getTimeAgo(alertNotification.created_at) }}</div>
                </template>
              </WatchListStock>
            </div>
            <div></div>
          </div>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>
<script setup lang="ts">
import { computed, ref } from 'vue'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import axios from 'axios'
import WatchListStock from './WatchListStock.vue'

export interface Props {
  // sortField: string
}

const props = defineProps<Props>()
const emit = defineEmits(['updateSortField'])

const userId = window.userId || 484

const alertNotifications = ref([
  {
    symbol: 'AMZN',
    id: 96442,
    desired_price: '178.12',
    timestamp: '2024-03-06 12:21:53',
    read: false
  },
  {
    symbol: 'TSLA',
    id: 83140,
    desired_price: '185.74',
    timestamp: '2024-03-06 12:21:53',
    read: false
  },
  {
    symbol: 'AA2PL',
    id: 831140,
    desired_price: '185.74',
    timestamp: '2024-03-06 12:21:53',
    read: true
  },
  {
    symbol: 'AA2PL',
    id: 831140,
    desired_price: '185.74',
    timestamp: '2024-03-06 12:21:53',
    read: true
  },
  {
    symbol: 'AA2PL',
    id: 8311140,
    desired_price: '185.74',
    timestamp: '2024-03-06 12:21:53',
    read: true
  },
  {
    symbol: 'AA2PL',
    id: 8231140,
    desired_price: '185.74',
    timestamp: '2024-03-06 12:21:53',
    read: true
  },
  {
    symbol: 'AA2PL',
    id: 8313140,
    desired_price: '185.74',
    timestamp: '2024-03-06 12:21:53',
    read: true
  },
  {
    symbol: 'AA2PL',
    id: 8311340,
    desired_price: '185.74',
    timestamp: '2024-03-06 12:21:53',
    read: true
  },
  {
    symbol: 'AA2PL',
    id: 8311410,
    desired_price: '185.74',
    timestamp: '2024-03-06 12:21:53',
    read: true
  }
])
const isLoading = ref(false)
const getAlertNotifications = async (): Promise<Stock[]> => {
  try {
    if (!userId) return []
    isLoading.value = true
    const formData = new FormData()
    formData.append('action', 'get_watchlist_user_alert_notifications')
    formData.append('user_id', userId)

    const notifications = await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data || [])

    alertNotifications.value = notifications
    return notifications
  } catch (error) {
    console.log(error)
    return []
  } finally {
    isLoading.value = false
  }
}

getAlertNotifications()

const notificationsCount = computed(() => alertNotifications.value.filter((n) => !n.read).length)

const readNotifications = async () => {
  try {
    // const formData = new FormData()
    // formData.append('action', 'read_watchlist_user_alert_notifications')
    // formData.append('user_id', userId)

    // const notifications = await axios
    //   .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
    //   .then((data) => data.data || [])

    setTimeout(() => {
      alertNotifications.value.forEach((n) => (n.read = true))
    }, 500)
  } catch (error) {
    console.log(error)
  }
}
function getTimeAgo(dateString: string): string {
  const date = new Date(dateString)
  const diffMillis = Date.now() - date.getTime()
  const seconds = Math.floor(diffMillis / 1000)

  if (seconds < 60) {
    return `${seconds} second${seconds > 1 ? 's' : ''} ago`
  }

  const minutes = Math.floor(seconds / 60)
  if (minutes < 60) {
    return `${minutes} minute${minutes > 1 ? 's' : ''} ago`
  }

  const hours = Math.floor(minutes / 60)
  if (hours < 24) {
    return `${hours} hour${hours > 1 ? 's' : ''} ago`
  }

  const days = Math.floor(hours / 24)
  if (days < 30) {
    return `${days} day${days > 1 ? 's' : ''} ago`
  }

  const months = Math.floor(days / 30)
  if (months < 12) {
    return `${months} month${months > 1 ? 's' : ''} ago`
  }

  const years = Math.floor(months / 12)
  if (years < 2) {
    return `${years} year ago`
  }

  return 'more than a year ago'
}
</script>
