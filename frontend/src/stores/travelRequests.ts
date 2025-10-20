import { defineStore } from 'pinia'
import  api  from '../services/api'

export const useTravelStore = defineStore('travelStore', {
  state: () => ({
    travelRequests: [] as any[],
    loading: false,
  }),
  actions: {
    async fetchTravelRequests(filters?: { status?: string; user_id?: number }) {
      this.loading = true
      try {
        const res = await api.get('/travel-requests', {params: filters})
        this.travelRequests = res.data.data
      } catch (err) {
        console.error('Erro ao buscar pedidos:', err)
      } finally {
        this.loading = false
      }
    },

    async createTravelRequest(payload: { user_id: number; destination: string; status: string }) {
      const { data } = await api.post('/travel-requests', payload)
      this.travelRequests.unshift(data)
      return data
    },

    async updateStatus(id: number, status: string) {
      try {
        await api.patch(`/travel-requests/${id}/status`, { status })
        const req = this.travelRequests.find(r => r.id === id)
        if (req) req.status = status
      } catch (err: any) {
        throw new Error(err.response?.data?.message || 'Erro ao atualizar status')
      }
    }
  },
})
