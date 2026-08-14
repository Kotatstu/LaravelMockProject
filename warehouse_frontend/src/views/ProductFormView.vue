<script setup>
import { useAuthStore } from '@/stores/auth';
import { useRoute, useRouter } from 'vue-router';
import { computed, onMounted, ref } from 'vue';
import api from '@/api/axios';

const authStore = useAuthStore()
const route = useRoute()
const router = useRouter()

//Computed to make the result reactive
//!! to change the result into boolen (undefined(no id) = false, anythings else = true)
//isEdit will catch the URL to see if this page using it for edit or create
const isEdit = computed(() => !!route.params.id)

const categories = ref([])
const suppliers = ref([])
const form = ref({ name: '', sku: '', category_id: '', supplier_id: '' })
const errorMessage = ref('')

//Cuz I'll have to repeat header quite alot of times
function autoHeaders()
{
  return { Authorization: `Bearer ${authStore.token}`}
}

async function fetchCategories()
{
  const response = await api.get('/category', { headers: autoHeaders() })

  categories.value = response.data
}

async function  fetchSuppliers()
{
  const response = await api.get('/supplier', { headers: autoHeaders() })

  suppliers.value = response.data
}

async function fetchProducts()
{
  const response = await api.get(`/product/${route.params.id}`, { headers: autoHeaders() })
  form.value = {
    name: response.data.name,
    sku: response.data.sku,
    category_id: response.data.category_id,
    supplier_id: response.data.supplier_id
  }
}

async function submitForm()
{
  errorMessage.value = ''

  try
  {
    if (isEdit.value)
    {
      await api.put(`/product/update/${route.params.id}`, form.value, { headers: autoHeaders() })
    }
    else
    {
      await api.post('/product/create', form.value, { headers: autoHeaders() })
    }

    router.push('/products')
  }
  catch (error)
  {
    errorMessage.value = error.response?.data?.message || 'Something went wrong.'
  }
}

onMounted(async () => {
  await fetchCategories()
  await fetchSuppliers()

  if(isEdit.value)
    await fetchProducts()
})

</script>

<template>
  <div>
    <h1>{{ isEdit ? 'Edit Product' : 'New Product' }}</h1>

    <form @submit.prevent="submitForm">
      <div>
        <label>Name</label>
        <input v-model="form.name" required />
      </div>

      <div>
        <label>SKU</label>
        <input v-model="form.sku" required />
      </div>

      <div>
        <label>Category</label>
        <select v-model="form.category_id" required>
          <option value="" disabled>Select a category</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <div>
        <label>Supplier</label>
        <select v-model="form.supplier_id" required>
          <option value="" disabled>Select a supplier</option>
          <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
            {{ supplier.name }}
          </option>
        </select>
      </div>

      <button type="submit">Save</button>
      <button type="button" @click="router.push('/products')">Cancel</button>
    </form>

    <p v-if="errorMessage" style="color: red;">{{ errorMessage }}</p>
  </div>
</template>
