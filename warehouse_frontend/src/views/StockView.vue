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
    <h1>Stocks</h1>
    <button @click="openCreateModal">+ New Stock</button>

    <table>
      <thead>
        <tr>
          <th>Warehouse</th>
          <th>Product</th>
          <th>Quantity</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="stock in stocks" :key="stock.id">
          <td>{{ stock.warehouse.name }}</td>
          <td>{{ stock.product.name }}</td>
          <td>{{ stock.quantity }}</td>
          <td>
            <button @click="openEditModal(stock)">Edit</button>
            <button @click="deleteStock(stock.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="showModal" class="modal-backdrop">
      <div class="modal">
        <h2>{{ isEdit ? 'Edit Stock' : 'New Stock' }}</h2>
        <form @submit.prevent="submitForm">
          <div v-if="!isEdit">
            <label>Warehouse ID</label>
            <input v-model="form.warehouse_id" type="number" required />
          </div>
          <div v-if="!isEdit">
            <label>Product ID</label>
            <input v-model="form.product_id" type="number" required />
          </div>
          <div>
            <label>Quantity</label>
            <input v-model="form.quantity" type="number" required />
          </div>
          <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
          <button type="submit">Save</button>
          <button type="button" @click="showModal = false">Cancel</button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal {
  background: rgb(128, 62, 62);
  padding: 2rem;
  border-radius: 8px;
  min-width: 300px;
}
</style>
