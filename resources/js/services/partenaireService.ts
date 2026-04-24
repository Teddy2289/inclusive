import api from './api'
import type { Paginated, Partenaire, PartenaireForm } from '@/types'

export interface PartenaireFilters {
  search?    : string
  ville?     : string
  statut?    : string
  per_page?  : number
  page?      : number
}

export const partenaireService = {
  async list(filters: PartenaireFilters = {}): Promise<Paginated<Partenaire>> {
    const { data } = await api.get('/partenaires', { params: filters })
    return data
  },

  async find(id: number): Promise<Partenaire> {
    const { data } = await api.get(`/partenaires/${id}`)
    return data.data
  },

  async create(payload: PartenaireForm): Promise<Partenaire> {
    const { data } = await api.post('/partenaires', payload)
    return data.data
  },

  async update(id: number, payload: PartenaireForm): Promise<Partenaire> {
    const { data } = await api.put(`/partenaires/${id}`, payload)
    return data.data
  },

  async remove(id: number): Promise<void> {
    await api.delete(`/partenaires/${id}`)
  },

  async changerStatut(id: number, statut: string): Promise<Partenaire> {
    const { data } = await api.patch(`/partenaires/${id}/statut`, { statut })
    return data.data
  },

  async transitions(id: number) {
    const { data } = await api.get(`/partenaires/${id}/transitions`)
    return data
  },
}
