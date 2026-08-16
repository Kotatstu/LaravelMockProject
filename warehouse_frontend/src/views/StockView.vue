<script setup>
import api from '@/api/axios';
import { useAuthStore } from '@/stores/auth';
import { onMounted, ref } from 'vue';

const authStore = useAuthStore()
const stocks = ref([])
const errorMessage = ref('')

const showModal = ref(false)
const isEdit = ref(false)
const editID = ref(null)
const form = ref({warehouse_id: '', product_id: '', quantity: ''})

async function fetchStock()
{
  const respone = await api.get('/stock', {headers: {Authorization: `Bearer ${authStore.token}`}})

  stocks.value = respone.data
}

function openCreateModal()
{
  showModal.value = true
  isEdit.value = false
  editID.value = null

  errorMessage.value = ''

  form.value = {warehouse_id: '', product_id: '', quantity: ''}
}

function openEditModal(stock)
{
  showModal.value = true
  isEdit.value = true
  editID.value = stock.id

  form.value = {quantity: stock.quantity}
}

async function submitForm()
{
  const headers = {Authorization: `Bearer ${authStore.token}`}
  errorMessage.value = ''

  try
  {
    if(isEdit.value)
    {
      await api.put(`/stock/update/${editID.value}`, form.value, {headers})
    }
    else
    {
      await api.post('/stock/create', form.value, {headers})
    }

    showModal.value = false
    await fetchStock()
  }
  catch (error)
  {
    errorMessage.value = error.response?.data?.message || 'Something went wrong.'
  }

}

async function deleteStock(id)
{
  if(!confirm('Are you sure you want to delete this stock?'))
    return

  await api.delete(`/stock/delete/${id}`, {headers: {Authorization: `Bearer ${authStore.token}`}})
  await fetchStock()
}

onMounted(() => {
  fetchStock()
})
</script>

<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Stocks</h1>
      <button
        @click="openCreateModal"
        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
      >
        + New Stock
      </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950">
      <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
          <tr>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Warehouse</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Product</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Quantity</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <tr v-for="stock in stocks" :key="stock.id" class="hover:bg-gray-50 dark:hover:bg-gray-900">
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ stock.warehouse.name }}</td>
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ stock.product.name }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ stock.quantity }}</td>
            <td class="px-4 py-3">
              <button @click="openEditModal(stock)" class="mr-3 text-blue-600 hover:underline dark:text-blue-400">
                Edit
              </button>
              <button @click="deleteStock(stock.id)" class="text-red-600 hover:underline dark:text-red-400">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black/50">
      <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-lg dark:bg-gray-950">
        <h2 class="mb-4 text-lg font-bold text-gray-900 dark:text-gray-100">
          {{ isEdit ? 'Edit Stock' : 'New Stock' }}
        </h2>

        <form @submit.prevent="submitForm" class="flex flex-col gap-4">
          <div v-if="!isEdit" class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Warehouse ID</label>
            <input
              v-model="form.warehouse_id"
              type="number"
              required
              class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />
          </div>

          <div v-if="!isEdit" class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Product ID</label>
            <input
              v-model="form.product_id"
              type="number"
              required
              class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />
          </div>

          <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
            <input
              v-model="form.quantity"
              type="number"
              required
              class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />
          </div>

          <div class="mt-2 flex justify-end gap-2">
            <button
              type="button"
              @click="showModal = false"
              class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
            >
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
