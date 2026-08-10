<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';//for routing purpose

const email = ref('')
const password = ref('')
const errorMessage = ref('')

const authStore = useAuthStore()
const router = useRouter()

async function handleLogin() {
  errorMessage.value = ''

  try {
    //Call login function in auth store
    await authStore.login(email.value, password.value)
    router.push('/dashboard')
  }
  catch
  {
    errorMessage.value = 'Invalid email or password.'
  }
}
</script>


<template>
  <div>
    <h1>Login</h1>
    <!--.prevent stopping browser default action to refresh the page after the submit happen-->
    <form @submit.prevent="handleLogin">
      <div>
        <label>Email</label>
        <input v-model="email" type="email" required />
      </div>

      <div>
        <label>Password</label>
        <input v-model="password" type="password" required />
      </div>

      <button type="submit">Log In</button>
    </form>

    <p v-if="errorMessage">{{ errorMessage }}</p>
  </div>
</template>
