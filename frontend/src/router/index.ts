import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/login',
    component: () => import('@/pages/LoginPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/register',
    component: () => import('@/pages/RegisterPage.vue'),
    meta: { guest: true },
  },
  {
    path: '/',
    component: () => import('@/layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', redirect: '/dashboard' },
      { path: 'dashboard', component: () => import('@/pages/DashboardPage.vue') },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, _from) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    return '/login'
  } else if (to.meta.guest && token) {
    return '/dashboard'
  }
})

export default router