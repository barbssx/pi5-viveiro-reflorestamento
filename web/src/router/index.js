import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/latest',
      name: 'latest',
      component: HomeView,
    },
    {
      path: '/readings',
      name: 'readings',
      component: HomeView,
    },
    {
      path: '/devices',
      name: 'devices',
      component: HomeView,
    },
  ],
})

export default router
