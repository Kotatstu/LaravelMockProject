<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

function handleLogout() {
  authStore.logout()
  router.push('/login')
}

const navLinks = [
  { to: '/dashboard', label: 'Dashboard' },
  { to: '/warehouses', label: 'Warehouses' },
  { to: '/categories', label: 'Categories' },
  { to: '/suppliers', label: 'Suppliers' },
  { to: '/products', label: 'Products' },
  { to: '/stocks', label: 'Stocks' },
  { to: '/purchaseOrders', label: 'Purchase Orders' },
  { to: '/stockExports', label: 'Stock Exports' },
]
</script>

<template>
  <div class="flex min-h-screen bg-gray-50 dark:bg-gray-900">
    <aside class="flex w-64 flex-col border-r border-gray-200 bg-white px-4 py-6 dark:border-gray-800 dark:bg-gray-950">
      <h2 class="mb-8 px-2 text-xl font-bold text-gray-800 dark:text-gray-100">Warehouse</h2>

      <nav class="flex flex-1 flex-col gap-1">
        <RouterLink
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          class="rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-100"
          active-class="bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400"
        >
          {{ link.label }}
        </RouterLink>
      </nav>

      <button
        @click="handleLogout"
        class="mt-4 rounded-md px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40"
      >
        Logout
      </button>
    </aside>

    <main class="flex-1 overflow-y-auto p-8 text-gray-900 dark:text-gray-100">
      <RouterView />
    </main>
  </div>
</template>
