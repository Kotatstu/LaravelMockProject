import { useAuthStore } from '@/stores/auth'
import { createRouter, createWebHistory } from 'vue-router'
import AppLayout from '@/components/AppLayout.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue'),
    },
    {
      path: '/',
      component: AppLayout,
      meta: { requiresAuth: true},
      children: [
        {
          path: '/dashboard',
          name: 'dashboard',
          component: () => import('@/views/DashboardView.vue'),
        },
      ],
    },
  ],
})

//run before every single route
router.beforeEach((to, _from) => {
  const authStore = useAuthStore()

  //if that page need auth token and do not have auth token, return to login page
  if(to.meta.requiresAuth && !authStore.token) {
    return '/login'
  }
})

export default router
