import { defineStore } from 'pinia'
import api from '@/utils/api'

export const useTeamStore = defineStore('team', {
  state: () => ({
    teams:[],
    userTeams:[],
    loading: false,
    error:null
  }),

  getters: {
    getAllTeams: (state) => state.teams,
    getUserTeams: (state) => state.userTeams,
  },

  actions: {
    
    async createTeam(team) {
      this.loading = true
      this.error = null

      try {
        const {data} = await api.post('/team', {
          name: team.name,
          description: team.description,
        })

        
        this.userTeams.push(data.data)
        

        return { success: true, team: data.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Creation failed'
        console.error('Creation Error:', error)
        throw error
      } finally {
        this.loading = false
      }
    },
    async UpdateTeam(team, id) {
        this.loading = true
        this.error = null
        
        try {
          const response = await api.put(`/team/${id}`, {
            name: team.name,
            description: team.description,
          }) 
            const { data } = response.data
            const index = this.teams.findIndex(t => t.id === id);
            if (index !== -1) {
              this.teams[index] = data;
            }
            return { success: true, team: data }
        } catch (error) {
          this.error = error.response?.data?.message || 'Update failed'
          console.error('Update Error:', error)
          throw error
        } finally {
          this.loading = false
        }
    }
    ,
    async deleteTeam(id) {
        this.loading = true
        this.error = null
        try {
            await api.delete(`/team/${id}`) 
            this.teams = this.teams.filter(t => t.id !== id);
            return { success: true }
            }
            catch (error) {
            this.error = error.response?.data?.message || 'Deletion failed'
            console.error('Deletion Error:', error)
            throw error
            } finally {
            this.loading = false
            }
    },

    async fetchUserTeams() {
      this.loading = true
      this.error = null

      try {
        const {data} = await api.get('/teams')        
        this.userTeams = data.data
        return { success: true, teams: data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Fetching teams failed'
        console.error('Fetch Teams Error:', error)
        throw error
      } finally {
        this.loading = false
      }
    },
    async fetchAllTeams() {
      this.loading = true
      this.error = null

      try {
        const response = await api.get('/teams')
        const { data } = response.data
        this.teams = data
        return { success: true, teams: data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Fetching teams failed'
        console.error('Fetch Teams Error:', error)
        throw error
      } finally {
        this.loading = false
        }
    },
    async fetchTeamById(id) {
      this.loading = true
      this.error = null

      try {
        const response = await api.get(`/team/${id}`)
        const { data } = response.data
        return { success: true, team: data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Fetching team failed'
        console.error('Fetch Team Error:', error)
        throw error
      } finally {
        this.loading = false
      }
    },
    async clearError() {
      this.error = null
    },

    
  },
})
