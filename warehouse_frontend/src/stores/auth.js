import { defineStore } from "pinia";
import { ref } from "vue";
import api from '@/api/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)//get the token from browser storage if not its null

  async function login(email, password) {
    const respone = await api.post('/login', {email, password})

    user.value = respone.data.user
    token.value = respone.data.token

    localStorage.setItem('token', token.value)//set the token return from API and save it to browser storage
  }

  function logout() {
    user.value = null
    token.value = null

    localStorage.removeItem('token')
  }

  async function register(name, email, password)
  {
    const response = await api.post('/register', { name, email, password })

    user.value = response.data.user
    token.value = response.data.token

    localStorage.setItem('token', token.value)
  }

  return { user, token, login, logout, register }
})
