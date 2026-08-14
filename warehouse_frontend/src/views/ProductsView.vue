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
    <h1>Products</h1>
    <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
    <button @click="router.push('/products/create')">+ New Product</button>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>SKU</th>
          <th>Category</th>
          <th>Supplier</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products" :key="product.id">
          <td>{{ product.name }}</td>
          <td>{{ product.sku }}</td>
          <td>{{ product.category.name }}</td>
          <td>{{ product.supplier.name }}</td>
          <td>
            <button @click="router.push(`/products/${product.id}/edit`)">Edit</button>
            <button @click="deleteProduct(product.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
