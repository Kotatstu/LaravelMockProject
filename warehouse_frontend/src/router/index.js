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
      path: '/register',
      name: 'register',
      component: () => import('@/views/RegisterView.vue')
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

        {
          path: '/warehouses',
          name: 'warehouses',
          component: () => import('@/views/WarehouseView.vue')
        },

        {
          path: '/categories',
          name: 'catergories',
          component: () => import('@/views/CatergoriesView.vue')
        },

        {
          path: '/suppliers',
          name: 'suppliers',
          component: () => import('@/views/SupplierView.vue')
        },

        {
          path: '/stocks',
          name: 'stocks',
          component: () => import('@/views/StockView.vue')
        },

        {
          path: '/products',
          name: 'products',
          component: () => import('@/views/ProductsView.vue')
        },

        {
          path: '/products/create',
          name: 'product-create',
          component: () => import('@/views/ProductFormView.vue')
        },

        {
          path: '/products/:id/edit',
          name: 'product-edit',
          component: () => import('@/views/ProductFormView.vue')
        },

        {
          path: '/purchaseOrders',
          name: 'purchase-orders',
          component: () => import('@/views/PurchaseOrderView.vue')
        },

        {
          path: '/purchaseOrder/create',
          name: 'purchase-order-create',
          component: () => import('@/views/PurchaseOrderCreateView.vue')
        },

        {
          path: '/stockExports',
          name: 'stock-export',
          component: () => import('@/views/StockExportView.vue')
        },

        {
          path: '/stockExport/create',
          name: 'stock-export-create',
          component: () => import('@/views/StockExportCreateView.vue')
        }
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
