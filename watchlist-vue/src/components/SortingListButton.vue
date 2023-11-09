<template>
  <Menu as="div" class="relative inline-block text-left">
    <MenuButton class="p-3 aspect-square hover:bg-black/10 rounded-full transition-colors">
      <svg
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M23 15L21.59 13.59L19 16.17V5H17V16.17L14.41 13.59L13 15L18 20L23 15Z"
          fill="#3F3F3F"
        />
        <path d="M11 9L9.59 10.41L7 7.83V19H5V7.83L2.41 10.41L1 9L6 4L11 9Z" fill="#3F3F3F" />
      </svg>
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
        class="fixed md:absolute right-0 mt-2 w-full md:w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-40"
      >
        <div class="">
          <MenuItem v-slot="{ active }" v-for="sortOrder in sortOrders" :key="sortOrder.field">
            <button
              class="text-base px-4 py-2 block w-full text-left"
              :class="{
                'bg-blue-200': sortField === sortOrder.field
              }"
              @click="select(sortOrder.field)"
            >
              {{ sortOrder.displayName }}
            </button>
          </MenuItem>
        </div>
      </MenuItems>
    </transition>
  </Menu>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
export interface Props {
  sortField: string
}
const props = defineProps<Props>()
const emit = defineEmits(['updateSortField'])

const sortOrders = [
  {
    displayName: 'Custom Order',
    field: 'custom'
  },
  {
    displayName: 'Sort By Symbol',
    field: 'symbol'
  },
  {
    displayName: 'Sort By Price',
    field: 'price'
  },
  {
    displayName: 'Sort By Change',
    field: 'change'
  },
  {
    displayName: 'Sort By Market Cap',
    field: 'market_cap'
  }
]

const select = (value) => {
  emit('updateSortField', value)
}
</script>
