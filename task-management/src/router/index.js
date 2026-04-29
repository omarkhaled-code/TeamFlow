import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '../stores/user'

const Registration = ()=> import('../views/registration/Registeration.vue')
const Dashboard = ()=> import('../views/Dashboard.vue')
const Settings = ()=> import('../views/Settings.vue')
const NotFound = ()=> import('../views/NotFound.vue')
const myTeams = ()=> import('../views/dashboard/MyTeams.vue')
const CreateTeam = ()=> import('../views/dashboard/CreateTeam.vue')
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path:"/registration",
      name:"registration",
      component: Registration,
      meta: { requireAuth: false }
    },
    {
      path: '/auth/callback',
      component: () => import('@/views/AuthCallback.vue')
    },
    {
      path: '/',
      name: 'dashboard',
      component: Dashboard,
      meta: { requireAuth: true}
    },
    {
      path: '/settings',
      name: 'settings',
      component: Settings,
      meta: { requireAuth: true }
    },
    {
      path: '/create-team',
      name: 'create-team',
      component:CreateTeam,
      meta: { requireAuth: true }
    },
    {
      path: '/update-team/:id',
      name: 'update-team',
      component: () => import('@/views/dashboard/UpdateTeam.vue'),
      meta: { requireAuth: true }
    },
    {
      path:'/:pathMatch(.*)*',
      name:'NotFound',
      component: NotFound,
      meta: { requireAuth: true }
    },
    {
      path:'/join-requests',
      name:'join-requests',
      component: () => import('@/views/dashboard/JoinRequests.vue'),
      meta: { requireAuth: true }
    },
    {
      path:'/notifications',
      name:'notifications',
      component: () => import('@/views/dashboard/Notifications.vue'),
      meta: { requireAuth: true }
    },
    {
      path:'/my-teams',
      name:'my-teams',
      component: myTeams,
      meta: { requireAuth: true }
    },
    {
      path:'/teams/team/:id',
      name:'team',
      props:true,
      component: () => import('@/views/dashboard/TeamWorkSpace.vue'),
      meta: { requireAuth: true }
    },
    {
      path:'/update-user',
      name:'update-user',
      component: () => import('@/views/dashboard/UpdateUser.vue'),
      meta: {requireAuth: true}
    },
    {
      path:'/join-team',
      name:'join-team',
      component: () => import('@/views/dashboard/JoinTeam.vue'),
      meta: {requireAuth: true}
    },
    {
      path:'/our-plans',
      name:"our-plans",
      component: () => import('@/views/dashboard/OurPlans.vue'),
      meta: {requireAuth: true}
    },
    {
      path:'/upgrade-plan',
      name:"/upgrade-plan",
      component: () => import('@/views/dashboard/UpgradePlan.vue'),
      meta: {requireAuth: true}
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: () => import('@/views/dashboard/Checkout.vue'),
      meta: {requireAuth: true}
    },
  
  ]
})


router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore();

  // Wait for auth state
  if (userStore.user === null && userStore.token) {
    try {
      await userStore.fetchUserData();
    } catch (e) {
      console.error(e);
    }
  }

  const loggedIn = !!userStore.token;
  const role = userStore.role;
  
  
  // 🔐 Protected routes
  if (to.meta.requireAuth && !loggedIn) {
    return next("/registration");
  }

  // 👑 Admin routes (optional)
  if (to.meta.requireAdmin && role !== "admin") {
    return next("/");
  }


  // Protected registration route
  if (!to.meta.requireAuth && loggedIn) {
    return next("/");
  }
  
  next();
});



export default router
