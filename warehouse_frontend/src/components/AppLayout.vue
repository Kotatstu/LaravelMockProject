<script setup>
import { RouterLink, RouterView, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore()//store the token
const router = useRouter()//for routing purpose

function handleLogout() {
  authStore.logout()
  router.push('/login')
}
</script>

<template>
  <div class="layout">
    <aside class="sidebar">
      <h2>Warehouse</h2>
      <nav>
        <RouterLink to="/dashboard">Dashboard</RouterLink>
        <RouterLink to="/warehouses">Warehouses</RouterLink>
        <RouterLink to="/categories">Categories</RouterLink>
        <RouterLink to="/suppliers">Suppliers</RouterLink>
        <RouterLink to="/products">Products</RouterLink>
        <RouterLink to="/stocks">Stocks</RouterLink>
        <RouterLink to="/purchase-orders">Purchase Orders</RouterLink>
        <RouterLink to="/stock-exports">Stock Exports</RouterLink>
      </nav>
      <button @click="handleLogout">Logout</button>
    </aside>

    <main class="content">
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.layout {
  display: flex;
  min-height: 100vh;
}
.sidebar {
  width: 220px;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  border-right: 1px solid #ddd;
}
.sidebar nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
}
.content {
  flex: 1;
  padding: 2rem;
}
</style>
