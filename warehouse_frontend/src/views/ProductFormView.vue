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
  <div class="mx-auto max-w-lg">
    <h1 class="mb-6 text-2xl font-bold text-gray-900 dark:text-gray-100">
      {{ isEdit ? 'Edit Product' : 'New Product' }}
    </h1>

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
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
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
          <input
            v-model="form.sku"
            required
            class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
          />
        </div>

        <div class="flex flex-col gap-1">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
          <select
            v-model="form.category_id"
            required
            class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
          >
            <option value="" disabled>Select a category</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
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

        <p v-if="errorMessage" class="text-sm text-red-600 dark:text-red-400">{{ errorMessage }}</p>

        <div class="mt-2 flex justify-end gap-2">
          <button
            type="button"
            @click="router.push('/products')"
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
</template>
