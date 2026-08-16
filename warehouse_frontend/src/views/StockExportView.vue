<script setup>
import api from '@/api/axios';
import { useAuthStore } from '@/stores/auth';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const authStore = useAuthStore()
const stockExports = ref([])
const router = useRouter()

function autoHeaders()
{
  return {Authorization: `Bearer ${authStore.token}`}
}

async function fetchStockExports()
{
  const response = await api.get('/stockExport', { headers: autoHeaders() })

  stockExports.value = response.data.map((stockExport) => ({
    ...stockExport,
    errorMessage: '',
    stockExportItems: stockExport.stock_export_items.map((item) => ({ ...item, dispatchQty: '' })),
  }))
}

async function submitDispatch(stockExport) {
  stockExport.errorMessage = ''

  const itemsToDispatch = stockExport.stockExportItems
    .filter((item) => item.dispatchQty && item.dispatchQty > 0)
    .map((item) => ({
      stock_export_item_id: item.id,
      quantity: Number(item.dispatchQty),
    }))

  if (itemsToDispatch.length === 0) {
    stockExport.errorMessage = 'Enter a quantity for at least one item.'
    return
  }

  try {
    await api.post(
      `/stockExport/${stockExport.id}/dispatch`,
      { items: itemsToDispatch },
      { headers: autoHeaders() },
    )

    await fetchStockExports()
  } catch (error) {
    stockExport.errorMessage = error.response?.data?.message || 'Failed to dispatch stock.'
  }
}

onMounted(() => {
  fetchStockExports()
})
</script>

<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Stock Exports</h1>
      <button
        @click="router.push('/stockExport/create')"
        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
      >
        + New Stock Export
      </button>
    </div>

    <div class="flex flex-col gap-6">
      <div
        v-for="stockExport in stockExports"
        :key="stockExport.id"
        class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950"
      >
        <div class="mb-4 flex items-start justify-between">
          <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ stockExport.reference }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Warehouse: {{ stockExport.warehouse.name }} · Destination: {{ stockExport.destination }}
            </p>
          </div>
          <span
            class="rounded-full px-3 py-1 text-xs font-medium"
            :class="{
              'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400': stockExport.status === 'pending',
              'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400': stockExport.status === 'partly_dispatched',
              'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400': stockExport.status === 'dispatched',
            }"
          >
            {{ stockExport.status }}
          </span>
        </div>

        <table class="w-full text-left text-sm">
          <thead class="border-b border-gray-200 dark:border-gray-800">
            <tr>
              <th class="pb-2 font-medium text-gray-600 dark:text-gray-300">Product</th>
              <th class="pb-2 font-medium text-gray-600 dark:text-gray-300">Ordered</th>
              <th class="pb-2 font-medium text-gray-600 dark:text-gray-300">Dispatched</th>
              <th class="pb-2 font-medium text-gray-600 dark:text-gray-300">Remaining</th>
              <th v-if="stockExport.status !== 'dispatched'" class="pb-2 font-medium text-gray-600 dark:text-gray-300">
                Dispatch Now
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr v-for="item in stockExport.stockExportItems" :key="item.id">
              <td class="py-2 text-gray-900 dark:text-gray-100">{{ item.product.name }}</td>
              <td class="py-2 text-gray-600 dark:text-gray-400">{{ item.quantity }}</td>
              <td class="py-2 text-gray-600 dark:text-gray-400">{{ item.quantity_dispatched }}</td>
              <td class="py-2 text-gray-600 dark:text-gray-400">{{ item.quantity - item.quantity_dispatched }}</td>
              <td v-if="stockExport.status !== 'dispatched'" class="py-2">
                <input
                  v-if="item.quantity - item.quantity_dispatched > 0"
                  v-model="item.dispatchQty"
                  type="number"
                  min="1"
                  :max="item.quantity - item.quantity_dispatched"
                  class="w-20 rounded-md border border-gray-300 bg-white px-2 py-1 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                />
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="stockExport.status !== 'dispatched'" class="mt-4 flex flex-wrap items-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
          <button
            @click="submitDispatch(stockExport)"
            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
          >
            Dispatch
          </button>
          <p v-if="stockExport.errorMessage" class="text-sm text-red-600 dark:text-red-400">{{ stockExport.errorMessage }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
