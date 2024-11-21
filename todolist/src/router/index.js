import { createRouter, createWebHistory } from 'vue-router'
import GameUpdate from '@/components/games/GameUpdate.vue'
import GameCreate from '@/components/games/GameCreate.vue'
import Games from '@/components/games/Games.vue'
import HomeView from '@/views/HomeView.vue'
import Login from '@/components/auth/Login.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  
  history: createWebHistory(import.meta.env.BASE_URL),
  
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/games',
      name: 'games',
      component: Games
    },
    {
      path: '/games/:id',
      name: 'updateGame',
      component: GameUpdate,
      props: route => ({ id: parseInt(route.params.id) })
    },    
    {
      path: '/games/new',
      name: 'createGame',
      component: GameCreate,
    },    
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue')
    }
  ]
  
})
let handlingFirstRoute = true

router.beforeEach(async (to, from, next) => {
  const storeAuth = useAuthStore()

  // Only handle the first route
  if (handlingFirstRoute) {
    handlingFirstRoute = false
    await storeAuth.restoreToken() // Restore token if necessary
  }

  // Prevent infinite redirection to login if already on the login page
  if (!storeAuth.user && to.name !== 'login') {
    next({ name: 'login' }) // Redirect to login if user is not authenticated
  } else {
    next() // Proceed to the requested route
  }
})

export default router
