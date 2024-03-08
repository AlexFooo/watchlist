<template>
  <Menu as="div" class="relative inline-block text-left">
    <MenuButton
      class="px-4 py-2 hover:bg-black/10 rounded-full transition-colors flex items-center gap-2 text-slate-500"
      @click="readNotifications"
    >
      <div class="relative">
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
            fill="#000"
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
          <div v-if="isLoading" class="p-4 flex items-center justify-center">
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
          </div>
          <div
            v-for="alertNotification in alertNotifications"
            :key="alertNotification.id"
            class="hover:bg-slate-50 transition-colors"
            :class="{
              'bg-blue-50': !alertNotification.read
            }"
          >
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

const alertNotifications = ref([])
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
    setTimeout(() => {
      alertNotifications.value.forEach((n) => (n.read = true))
    }, 500)

    const formData = new FormData()
    formData.append('action', 'mark_all_notifications_as_read')
    formData.append('user_id', userId)

    await axios
      .post('https://letizo.com/wp-admin/admin-ajax.php', formData)
      .then((data) => data.data || [])
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
