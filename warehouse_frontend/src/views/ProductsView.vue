<script setup>
import { useAuthStore } from '@/stores/auth';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api/axios';

const products = ref([])
const authStore = useAuthStore()
const router = useRouter()
const errorMessage = ref('')

async function  fetchProducts()
{
  const response = await api.get('/product', {headers: {Authorization: `Bearer ${authStore.token}`}})

  products.value = response.data
}

async function deleteProduct(id)
{
  if(!confirm('Are you sure you want to delete this product ?'))
    return

  errorMessage.value = ''

  try
  {
    await api.delete(`/product/delete/${id}`, {headers: {Authorization: `Bearer ${authStore.token}`}})
  }
  catch (error)
  {
    errorMessage.value = error.response?.data?.message || 'Failed to delete product.'
  }


  await fetchProducts()
}

onMounted(() => {
  fetchProducts()
})

</script>

<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Products</h1>
      <p v-if="errorMessage" class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>
      <button
        @click="router.push('/products/create')"
        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
      >
        + New Product
      </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950">
      <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
          <tr>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Name</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">SKU</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Category</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Supplier</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50 dark:hover:bg-gray-900">
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ product.name }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ product.sku }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ product.category.name }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ product.supplier.name }}</td>
            <td class="px-4 py-3">
              <button
                @click="router.push(`/products/${product.id}/edit`)"
                class="mr-3 text-blue-600 hover:underline dark:text-blue-400"
              >
                Edit
              </button>
              <button @click="deleteProduct(product.id)" class="text-red-600 hover:underline dark:text-red-400">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
