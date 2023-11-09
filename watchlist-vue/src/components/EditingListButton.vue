<template>
  <div>
    <button @click="openModal" class="p-3 aspect-square hover:bg-black/10 rounded-full transition-colors">
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
          d="M19.06 3.59L20.41 4.94C21.2 5.72 21.2 6.99 20.41 7.77L7.18 21H3V16.82L13.4 6.41L16.23 3.59C17.01 2.81 18.28 2.81 19.06 3.59ZM5 19L6.41 19.06L16.23 9.23L14.82 7.82L5 17.64V19Z"
          fill="#3F3F3F"
        />
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
                  class="text-lg font-medium leading-6 pl-2 pr-2 py-2 bg-[#E41E15] text-white flex justify-between items-center"
                >
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
                        d="M20 11H7.83L13.42 5.41L12 4L4 12L12 20L13.41 18.59L7.83 13H20V11Z"
                        fill="white"
                      />
                    </svg>
                  </button>
                  <div class="flex-1 pl-2">Editing watchlist</div>

                  <button
                    @click="applyChanges"
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
                        d="M9.00016 16.17L4.83016 12L3.41016 13.41L9.00016 19L21.0002 7.00003L19.5902 5.59003L9.00016 16.17Z"
                        fill="white"
                      />
                    </svg>
                  </button>
                </DialogTitle>
                <draggable v-model="userStocks" group="stocks" item-key="symbol" handle=".handle">
                  <template #header>
                    <div v-if="userStocks.length === 0">
                      <h2 class="text-lg text-center py-4 text-slate-500">No stocks added</h2>
                    </div>
                  </template>
                  <template #item="{ element: stock }">
                    <WatchListStock :stock="stock" class="cursor-grab">
                      <template #left>
                        <Transition
                          mode="out-in"
                          enter-active-class="transform transition duration-300 ease-out"
                          enter-from-class="-translate-x-8 opacity-0"
                          enter-to-class="translate-x-0 opacity-100"
                          leave-active-class="transform transition duration-150 ease-in"
                          leave-from-class="translate-x-0 opacity-100"
                          leave-to-class="-translate-x-8 opacity-0"
                        >
                          <button
                            class="p-2 -ml-2 hover:bg-slate-50 rounded-full"
                            @click="removeUserStock(stock)"
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
                                d="M5.45391 5.45383C5.85337 5.05437 6.50102 5.05437 6.90048 5.45383L12.0001 10.5534L17.0996 5.45386C17.4991 5.0544 18.1467 5.0544 18.5462 5.45386C18.9457 5.85332 18.9457 6.50097 18.5462 6.90043L13.4466 12L18.5462 17.0996C18.9457 17.499 18.9457 18.1467 18.5462 18.5461C18.1468 18.9456 17.4991 18.9456 17.0997 18.5461L12.0001 13.4466L6.90046 18.5462C6.501 18.9456 5.85335 18.9456 5.45389 18.5462C5.05443 18.1467 5.05443 17.4991 5.45389 17.0996L10.5535 12L5.45391 6.90039C5.05445 6.50094 5.05445 5.85329 5.45391 5.45383Z"
                                fill="#FF3A3A"
                              />
                            </svg>
                          </button>
                        </Transition>
                      </template>
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
                          <div class="watchlist-item-right flex flex-wrap justify-end">
                            <div class="handle p-2 -ml-2 hover:bg-slate-50 rounded-full">
                              <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                              >
                                <rect
                                  width="2.04575"
                                  height="18.5153"
                                  rx="1.02288"
                                  transform="matrix(-4.37114e-08 1 1 4.37114e-08 2.74219 7.95422)"
                                  fill="#A6A6A6"
                                />
                                <rect
                                  width="2.04575"
                                  height="18.5153"
                                  rx="1.02288"
                                  transform="matrix(-4.37114e-08 1 1 4.37114e-08 2.74219 14)"
                                  fill="#A6A6A6"
                                />
                              </svg>
                            </div>
                          </div>
                        </Transition>
                      </template>
                    </WatchListStock>
                  </template>
                </draggable>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>
<script setup lang="ts">
import { ref, watch } from 'vue'
import WatchListStock from './WatchListStock.vue'
import type { Stock } from '@/types'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue'
import draggable from 'vuedraggable'

export interface Props {
  userStocks: Stock[]
}
const props = defineProps<Props>()
const emit = defineEmits(['updateUserStocks'])

const userStocks = ref(props.userStocks)

const applyChanges = () => {
  emit('updateUserStocks', userStocks.value)
  closeModal()
}

const isOpen = ref(false)

function closeModal() {
  isOpen.value = false
}
function openModal() {
  isOpen.value = true
}

const removeUserStock = (stock: Stock) => {
  userStocks.value = userStocks.value.filter((s) => s.symbol !== stock.symbol)
}

watch(
  () => props.userStocks,
  (newVal) => {
    userStocks.value = newVal
  }
)
</script>
