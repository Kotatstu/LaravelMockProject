<script setup>
import api from '@/api/axios';
import { useAuthStore } from '@/stores/auth';
import { onMounted, ref } from 'vue';

const authStore = useAuthStore()
const suppliers = ref([])
const errorMessage = ref('')

const showModal = ref(false)
const isEdit = ref(false)
const editID = ref(null)
const form = ref({name: '', email: '', phone: ''})

async function fetchSuppliers()
{
  const respone = await api.get('/supplier', { headers: {Authorization: `Bearer ${authStore.token}`}})

  suppliers.value = respone.data
}

function openCreateModal()
{
  showModal.value = true
  isEdit.value = false
  editID.value = null

  errorMessage.value = ''

  form.value = {name: '', email: '', phone: ''}
}

function openEditModal(supplier)
{
  showModal.value = true
  isEdit.value = true
  editID.value = supplier.id

  form.value = { name: supplier.name, email: supplier.email, phone: supplier.phone}
}

async function submitForm()
{
  const headers = {Authorization: `Bearer ${authStore.token}`}
  errorMessage.value = ''

  try
  {
    if (isEdit.value)
    {
      await api.put(`/supplier/update/${editID.value}`, form.value, {headers})
    }
    else
    {
      await api.post('/supplier/create', form.value, {headers})
    }

    showModal.value = false
    await fetchSuppliers()
  }
  catch (error)
  {
    errorMessage.value = error.response?.data?.message || 'Something went wrong.'
  }
}

async function deleteSupplier(id)
{
  if(!confirm('Are you sure you want to delete this supplier?'))
    return

  await api.delete(`/supplier/delete/${id}`, {headers: {Authorization: `Bearer ${authStore.token}`}})
  await fetchSuppliers()
}

onMounted(() =>{
  fetchSuppliers()
})

</script>

<template>
  <div>
    <h1>Suppliers</h1>
    <button @click="openCreateModal">+ New Supplier</button>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="supplier in suppliers" :key="supplier.id">
          <td>{{ supplier.name }}</td>
          <td>{{ supplier.email }}</td>
          <td>{{ supplier.phone }}</td>
          <td>
            <button @click="openEditModal(supplier)">Edit</button>
            <button @click="deleteSupplier(supplier.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="showModal" class="modal-backdrop">
      <div class="modal">
        <h2>{{ isEdit ? 'Edit Supplier' : 'New Supplier' }}</h2>
        <form @submit.prevent="submitForm">
          <div>
            <label>Name</label>
            <input v-model="form.name" required />
          </div>
          <div>
            <label>Email</label>
            <input v-model="form.email" type="email" />
          </div>
          <div>
            <label>Phone</label>
            <input v-model="form.phone" />
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
  background: rgb(117, 52, 52);
  padding: 2rem;
  border-radius: 8px;
  min-width: 300px;
}
</style>
