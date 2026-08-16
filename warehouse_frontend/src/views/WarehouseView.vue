<script setup>
import { ref, onMounted } from 'vue';
import api from '@/api/axios';
import { useAuthStore } from '@/stores/auth';

const warehouses = ref([])//list of warehouse fetch from calling API
const authStore = useAuthStore()//for token authentication
const errorMessage = ref('')//Catch the exeption happen throw by API respone

const showModal = ref(false)//Flag to open create new warehouse or not
const form = ref({name: '', location: ''})//Blank form info
const isEdit = ref(false)
const editId = ref(null)

//fetch data from warehouse table calling API
async function fetchWarehouse() {
  const respone = await api.get('/warehouse', {
    headers: { Authorization: `Bearer ${authStore.token}` },
  })

  warehouses.value = respone.data
}

//Flag to show create form
function openCreateModal()
{
  isEdit.value = false
  editId.value = null
  showModal.value = true

  errorMessage.value = ''

  form.value = {name: '', location: ''}
}

function openEditModal(warehouse)
{
  isEdit.value = true
  editId.value = warehouse.id
  showModal.value = true

  form.value = {name: warehouse.name, location: warehouse.location}
}

//Create new warehouse vial calling API and load the list again
async function submitForm() {
  //token key for less code repeating
  const headers = {Authorization: `Bearer ${authStore.token}`}
  errorMessage.value = ''

  try
  {
    if (isEdit.value)
    {
      await api.put(`/warehouse/${editId.value}`, form.value, {headers})
    }
    else
    {
      await api.post('/warehouse', form.value, {headers})
    }

    showModal.value = false
    await fetchWarehouse()
  }
  catch (error)
  {
    errorMessage.value = error.response?.data?.message || 'Something went wrong.'
  }
  //check is either using the modal for edit or create

}

//Delete the selected warehouse (vial ID)
async function deleteWarehouse(id)
{
  if (!confirm('Are you sure you want to delete this warehouse?'))
    return

    await api.delete(`/warehouse/${id}`, {
      headers: {Authorization: `Bearer ${authStore.token}`}
    })

  await fetchWarehouse()
}

//Run when loaded
onMounted(() => {
  fetchWarehouse()
})
</script>


<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Warehouses</h1>
      <button
        @click="openCreateModal"
        class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
      >
        + New Warehouse
      </button>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950">
      <table class="w-full text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
          <tr>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Name</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Location</th>
            <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          <tr v-for="warehouse in warehouses" :key="warehouse.id" class="hover:bg-gray-50 dark:hover:bg-gray-900">
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ warehouse.name }}</td>
            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ warehouse.location }}</td>
            <td class="px-4 py-3">
              <button @click="openEditModal(warehouse)" class="mr-3 text-blue-600 hover:underline dark:text-blue-400">
                Edit
              </button>
              <button @click="deleteWarehouse(warehouse.id)" class="text-red-600 hover:underline dark:text-red-400">
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
          {{ isEditing ? 'Edit Warehouse' : 'New Warehouse' }}
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
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
            <input
              v-model="form.location"
              class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />
          </div>

          <p v-if="errorMessage" class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>

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
