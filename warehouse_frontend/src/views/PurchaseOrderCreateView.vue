<script setup>
import api from '@/api/axios';
import { useAuthStore } from '@/stores/auth';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';


const router = useRouter()
const authStore = useAuthStore()

const suppliers = ref([])
const products = ref([])
const errorMessage = ref([])

const form = ref({reference: '', supplier_id: '', status: 'pending'})
const items = ref([{ product_id: '', quantity: ''}])

function autoHeaders()
{
  return { Authorization: `Bearer ${authStore.token}`}
}

async function fetchSuppliers()
{
  const respone = await api.get('/supplier', {headers: autoHeaders()})

  suppliers.value = respone.data
}

async function fetchProducts()
{
  const respone = await api.get('/product', {headers: autoHeaders()})

  products.value = respone.data
}

function addItem()
{
  items.value.push({product_id: '', quantity: ''})
}

function removeItem(index)
{
  items.value.splice(index, 1)
}

async function submitForm()
{
  errorMessage.value = ''

  try
  {
    await api.post('/purchaseOrder/create', {...form.value, items: items.value}, {headers: autoHeaders()})

    router.push('/purchaseOrders')
  }
  catch (error)
  {
    errorMessage.value = error.response?.data?.message || 'Something went wrong.'
  }
}

onMounted(() => {
  fetchProducts()
  fetchSuppliers()
})
</script>

<template>
  <div class="mx-auto max-w-2xl">
    <h1 class="mb-6 text-2xl font-bold text-gray-900 dark:text-gray-100">New Purchase Order</h1>

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
      <form @submit.prevent="submitForm" class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Reference</label>
          <input
            v-model="form.reference"
            required
            class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
          />
        </div>

        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
          <select
            v-model="form.supplier_id"
            required
            class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
          >
            <option value="" disabled>Select a supplier</option>
            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
              {{ supplier.name }}
            </option>
          </select>
        </div>

        <h2 class="mt-2 text-sm font-bold text-gray-900 dark:text-gray-100">Items</h2>
        <div v-for="(item, index) in items" :key="index" class="flex items-end gap-2">
          <div class="flex flex-1 flex-col gap-1">
            <label class="text-xs font-medium text-gray-700 dark:text-gray-300">Product</label>
            <select
              v-model="item.product_id"
              required
              class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            >
              <option value="" disabled>Select a product</option>
              <option v-for="product in products" :key="product.id" :value="product.id">
                {{ product.name }}
              </option>
            </select>
          </div>

          <div class="flex w-28 flex-col gap-1">
            <label class="text-xs font-medium text-gray-700 dark:text-gray-300">Quantity</label>
            <input
              v-model="item.quantity"
              type="number"
              min="1"
              required
              class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />
          </div>

          <button
            type="button"
            @click="removeItem(index)"
            :disabled="items.length === 1"
            class="rounded-md px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40 dark:text-red-400 dark:hover:bg-red-950/40"
          >
            Remove
          </button>
        </div>

        <button
          type="button"
          @click="addItem"
          class="self-start rounded-md px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950/40"
        >
          + Add Item
        </button>

        <p v-if="errorMessage" class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>

        <div class="mt-2 flex justify-end gap-2 border-t border-gray-100 pt-4 dark:border-gray-800">
          <button
            type="button"
            @click="router.push('/purchase-orders')"
            class="rounded-md px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
          >
            Create Order
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
