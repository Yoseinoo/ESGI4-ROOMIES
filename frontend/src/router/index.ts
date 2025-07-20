import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/HomeView.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
    },
    { 
      path: '/room/:id',
      name: 'room',
      component: () => import('../views/RoomView.vue'),
    },
    { 
      path: '/profile',
      name: 'profile',
      component: () => import('../views/UserView.vue'),
    },
  ],
})

// Navigation Guard
router.beforeEach(async (to, _, next) => {
  const user = localStorage.getItem('user');
  console.log(user)

  // Skip guard for public routes
  const publicPages = ['login']
  const authRequired = !publicPages.includes(to.name as string);

  if (authRequired && !user) {
    next({ name: 'login' })
  } else {
    next()
  }
})

export default router
