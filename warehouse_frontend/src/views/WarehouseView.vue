<script setup>
import { ref, onMounted } from 'vue';
import api from '@/api/axios';
import { useAuthStore } from '@/stores/auth';

const warehouses = ref([])//list of warehouse fetch from calling API
const authStore = useAuthStore()//for token authentication

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

  //check is either using the modal for edit or create
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
    <h1>Warehouses</h1>
    <button @click="openCreateModal">+ New Warehouse</button>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Location</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="warehouse in warehouses" :key="warehouse.id">
          <td>{{ warehouse.name }}</td>
          <td>{{ warehouse.location }}</td>
          <td>
            <button @click="openEditModal(warehouse)">Edit</button>
            <button @click="deleteWarehouse(warehouse.id)">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="showModal" class="modal-backdrop">
      <div class="modal">
        <h2>{{ isEditing ? 'Edit Warehouse' : 'New Warehouse' }}</h2>
        <form @submit.prevent="submitForm">
          <div>
            <label>Name</label>
            <input v-model="form.name" required />
          </div>
          <div>
            <label>Location</label>
            <input v-model="form.location" />
          </div>
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
  background: rgb(154, 113, 113);
  padding: 2rem;
  border-radius: 8px;
  min-width: 300px;
}
</style>
