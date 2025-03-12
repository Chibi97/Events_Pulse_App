import { createRouter, createWebHistory } from 'vue-router'
import MainContainer from '../containers/MainContainer.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: MainContainer,
    },
    {
      path: '/cart',
      name: 'cart',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../containers/CartContainer.vue'),
    },
  ],
})

export default router
