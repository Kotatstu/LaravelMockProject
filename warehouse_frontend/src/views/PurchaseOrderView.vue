<script setup>
import api from '@/api/axios';
import { useAuthStore } from '@/stores/auth';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

const authStore = useAuthStore()
const purchaseOrders = ref([])
const router = useRouter()

function  autoHeaders()
{
  return {Authorization: `Bearer ${authStore.token}`}
}

async function fetchPurchaseOrders()
{
  const response = await api.get('/purchaseOrder', {headers: autoHeaders()})

  purchaseOrders.value = response.data.map((order) => ({
    ...order,
    warehouseId: '',
    errorMessage: '',
    items: order.items.map((item) => ({ ...item, receiveQty: '' })),
  }))
}

async function submitReceive(order)
{
  order.errorMessage = ''

  const itemsToReceive = order.items
    .filter((item) => item.receiveQty && item.receiveQty > 0)
    .map((item) => ({
      purchase_order_item_id: item.id,
      quantity: Number(item.receiveQty),
    }))

  if (itemsToReceive.length === 0) {
    order.errorMessage = 'Enter a quantity for at least one item.'
    return
  }

  try {
    await api.post(
      `/purchaseOrder/${order.id}/receive`,
      { warehouse_id: order.warehouseId, items: itemsToReceive },
      { headers: autoHeaders() },
    )

    await fetchPurchaseOrders()
  } catch (error) {
    order.errorMessage = error.response?.data?.message || 'Failed to receive stock.'
  }
}

onMounted(() => {
  fetchPurchaseOrders()
})

</script>

<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Purchase Orders</h1>
      <button
        @click="router.push('/purchaseOrder/create')"
        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
      >
        + New Purchase Order
      </button>
    </div>

    <div class="flex flex-col gap-6">
      <div
        v-for="order in purchaseOrders"
        :key="order.id"
        class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950"
      >
        <div class="mb-4 flex items-start justify-between">
          <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ order.reference }}</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Supplier: {{ order.supplier.name }}</p>
          </div>
          <span
            class="rounded-full px-3 py-1 text-xs font-medium"
            :class="{
              'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400': order.status === 'pending',
              'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400': order.status === 'partly_received',
              'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400': order.status === 'received',
            }"
          >
            {{ order.status }}
          </span>
        </div>

        <table class="w-full text-left text-sm">
          <thead class="border-b border-gray-200 dark:border-gray-800">
            <tr>
              <th class="pb-2 font-medium text-gray-600 dark:text-gray-300">Product</th>
              <th class="pb-2 font-medium text-gray-600 dark:text-gray-300">Ordered</th>
              <th class="pb-2 font-medium text-gray-600 dark:text-gray-300">Received</th>
              <th class="pb-2 font-medium text-gray-600 dark:text-gray-300">Remaining</th>
              <th v-if="order.status !== 'received'" class="pb-2 font-medium text-gray-600 dark:text-gray-300">
                Receive Now
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr v-for="item in order.items" :key="item.id">
              <td class="py-2 text-gray-900 dark:text-gray-100">{{ item.product.name }}</td>
              <td class="py-2 text-gray-600 dark:text-gray-400">{{ item.quantity }}</td>
              <td class="py-2 text-gray-600 dark:text-gray-400">{{ item.quantity_received }}</td>
              <td class="py-2 text-gray-600 dark:text-gray-400">{{ item.quantity - item.quantity_received }}</td>
              <td v-if="order.status !== 'received'" class="py-2">
                <input
                  v-if="item.quantity - item.quantity_received > 0"
                  v-model="item.receiveQty"
                  type="number"
                  min="1"
                  :max="item.quantity - item.quantity_received"
                  class="w-20 rounded-md border border-gray-300 bg-white px-2 py-1 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                />
              </td>
            </tr>
          </tbody>
        </table>

        <div v-if="order.status !== 'received'" class="mt-4 flex flex-wrap items-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
          <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-gray-700 dark:text-gray-300">Warehouse ID</label>
            <input
              v-model="order.warehouseId"
              type="number"
              required
              class="w-32 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />
          </div>
          <button
            @click="submitReceive(order)"
            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
          >
            Receive
          </button>
          <p v-if="order.errorMessage" class="text-sm text-red-600 dark:text-red-400">{{ order.errorMessage }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
