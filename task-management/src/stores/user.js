import { defineStore } from 'pinia'
import api from '@/utils/api'



export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null,
    isAdmin:false,
    plan: null,
    notifications: [],
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    isTokenValid: (state) => !!state.user && !!state.token,
    userName: (state) => state.user?.name || null,
    userID: (state) => state.user?.id || null,
    userEmail: (state) => state.user?.email || null,
    userPlan: (state) => (state.plan === 1) ? 'free': 'pro',
    userAvatar: (state) => {
    const avatar = state.user?.avatar;

    if (!avatar) {
      // لا يوجد avatar → استخدم الصورة الافتراضية
      return 'http://localhost:8000/storage/avatars/default.jpg';
    }

    // إذا كان الرابط يبدأ بـ http أو https → اتركه كما هو
    if (avatar.startsWith('http://') || avatar.startsWith('https://')) {
      return avatar;
    }

    // خلاف ذلك → رابط نسبي على السيرفر
    return `http://localhost:8000/storage/${avatar}`;
  }
    

  },

  actions: {
    /**
     * Register a new user
     * @param {Object} credentials - { email, password, name }
     */
    async register(credentials) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/register', {
          email: credentials.email,
          password: credentials.password,
          name: credentials.name,
        })

        const { token, user } = response.data
        
        this.token = token
        this.user = user
        this.plan = user.plan_id;
        localStorage.setItem('token', token)
        localStorage.setItem('currentComponent', 'tasks')
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`

        return { success: true, user }
      } catch (error) {
        this.error = error.response?.data?.message || 'Registration failed'
        this.loading = false
        return {success: false, error: error.response.data}
      } 
    },

    /**
     * Login user with email and password
     * @param {Object} credentials - { email, password }
     */
    async login(credentials) {
      this.loading = true
      this.error = null

      try {
        const response = await api.post('/login', {
          email: credentials.email,
          password: credentials.password,
        })

        const { token, user } = response.data

        this.token = token
        this.user = user
        this.plan = user.plan_id;
        localStorage.setItem('token', token)
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        
        return { success: true, user }

      } catch (error) {
        this.error = error.response?.data?.message || 'Login failed'
        this.loading = false
        return { success: false, error: error.response.data }
      }

    },

    /**
     * Login user with a pre-existing token
     **/
    loginWithToken(token, user) {
      this.token = token
      this.user = user
      this.plan = user.plan_id;
      localStorage.setItem('token', token)
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`
    },

    /**
     * Verify the stored token
     */
    async verifyToken() {
      if (!this.token) {
        this.user = null
        return false
      }

      this.loading = true
      this.error = null

      try {
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        const response = await api.get('/user')

        this.user = response.data.user
        this.plan = response.data.user.plan_id;
        return true
      } catch (error) {
        this.error = 'Token is invalid or expired'
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        delete api.defaults.headers.common['Authorization']
        console.error('Token verification error:', error)
        return false
      } finally {
        this.loading = false
      }
    },

    /**
     * Fetch current user data using valid token
     */
    async fetchUserData() {
      if (!this.token) {
        this.error = 'No token available'
        return null
      }

      this.loading = true
      this.error = null

      try {
        const response = await api.get('/user')
        this.user = response.data.user
        this.plan = response.data.user.plan_id;
        return this.user
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch user data'
        if (error.response?.status === 401) {
          // Token is invalid, clear auth state
          this.logout()
        }
        console.error('Error fetching user data:', error)
        return null
      } finally {
        this.loading = false
      }
    },

    /**
     * Logout user and clear session
     */
    logout() {
      this.token = null
      this.user = null
      this.error = null
      localStorage.removeItem('token')
      delete api.defaults.headers.common['Authorization']
      api.post('/logout').catch((error) => {
        this.error = `Logout failed: ${error.response?.data?.message || error.message}`
      })
    },
    
    async getUserRole(teamId){
       if (!this.token) {
        this.error = 'No token available'
        return null
      }

      this.loading = true
      this.error = null

      try {
        
        const response = await api.post(`/check-role/${teamId}`)
        this.isAdmin = response.data ? true : false;
        
        
        return true
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch user data'
        if (error.response?.status === 401) {
          // Token is invalid, clear auth state
          this.logout()
        }
        console.error('Error fetching user data:', error)
        return false
      } finally {
        this.loading = false
      }
    },
    /**
     * Clear error message
     */
    clearError() {
      this.error = null
    },
  },
})
