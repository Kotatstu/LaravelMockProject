<script setup>
import { onMounted, ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import api from '@/api/axios';

const categories = ref([])
const authStore = useAuthStore()
const errorMessage = ref('')

const showModal = ref(false)
const isEdit = ref(false)
const editID = ref(null)
const form = ref({name: ''})

async function fetchCategories()
{
  const respone = await api.get('/category', {
    headers: {Authorization: `Bearer ${authStore.token}`}
  })

  categories.value = respone.data
}

function openCreateModal()
{
  showModal.value = true
  isEdit.value = false
  editID.value = null

  errorMessage.value = ''

  form.value = {name: ''}
}

function openEditModal(category)
{
  showModal.value = true
  isEdit.value = true
  editID.value = category.id

  form.value = {name: category.name}
}

async function submitForm()
{
  const headers = {Authorization: `Bearer ${authStore.token}`}
  errorMessage.value = ''

  try
  {
    if (isEdit.value)
    {
      await api.put(`/category/update/${editID.value}`, form.value, {headers})
    }
    else
    {
      await api.post('/category/create', form.value, {headers})
    }

    showModal.value = false
    await fetchCategories()
  }
  catch (error)
  {
    errorMessage.value = error.response?.data?.message || 'Something went wrong.'
  }

}

async function deleteCategory(id)
{
  if (!confirm('Are you sure you want to delete this category?'))
    return

  await api.delete(`/category/delete/${id}`, {headers: {Authorization: `Bearer ${authStore.token}`}})
  await fetchCategories()
}

onMounted(() => {
  fetchCategories()
}
)
</script>

<template>
  <div>
    <h1>Categories</h1>
    <button @click="openCreateModal">+ New Category</button>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="category in categories" :key="category.id">
          <td>{{ category.name }}</td>
          <td>
            <button @click="openEditModal(category)">Edit</button>
            <button @click="deleteCategory(category.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="showModal" class="modal-backdrop">
      <div class="modal">
        <h2>{{ isEdit ? 'Edit Category' : 'New Category' }}</h2>
        <form @submit.prevent="submitForm">
          <div>
            <label>Name</label>
            <input v-model="form.name" required />
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
  background: rgb(97, 44, 44);
  padding: 2rem;
  border-radius: 8px;
  min-width: 300px;
}
</style>
