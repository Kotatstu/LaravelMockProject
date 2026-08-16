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
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Suppliers</h1>
      <button
        @click="openCreateModal"
        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
      >
        + New Supplier
      </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950">
      <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
          <tr>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Name</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Email</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Phone</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <tr v-for="supplier in suppliers" :key="supplier.id" class="hover:bg-gray-50 dark:hover:bg-gray-900">
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ supplier.name }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ supplier.email }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ supplier.phone }}</td>
            <td class="px-4 py-3">
              <button @click="openEditModal(supplier)" class="mr-3 text-blue-600 hover:underline dark:text-blue-400">
                Edit
              </button>
              <button @click="deleteSupplier(supplier.id)" class="text-red-600 hover:underline dark:text-red-400">
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
          {{ isEdit ? 'Edit Supplier' : 'New Supplier' }}
        </h2>

        <form @submit.prevent="submitForm" class="flex flex-col gap-4">
          <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
            <input
              v-model="form.name"
              required
              class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />
          </div>

          <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input
              v-model="form.email"
              type="email"
              class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />
          </div>

          <div class="flex flex-col gap-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
            <input
              v-model="form.phone"
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
