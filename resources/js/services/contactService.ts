import api from './api'
import type { Paginated, Contact, ContactForm } from '@/types'

export interface ContactFilters {
  partenaire_id? : number
  statut?        : string
  search?        : string
  per_page?      : number
  page?          : number
}

export const contactService = {
  async list(filters: ContactFilters = {}): Promise<Paginated<Contact>> {
    const { data } = await api.get('/contacts', { params: filters })
    return data
  },

  async find(id: number): Promise<Contact> {
    const { data } = await api.get(`/contacts/${id}`)
    return data.data
  },

  async create(payload: ContactForm): Promise<Contact> {
    const { data } = await api.post('/contacts', payload)
    return data.data
  },

  async update(id: number, payload: ContactForm): Promise<Contact> {
    const { data } = await api.put(`/contacts/${id}`, payload)
    return data.data
  },

  async remove(id: number): Promise<void> {
    await api.delete(`/contacts/${id}`)
  },

  async changerStatut(id: number, statut: string): Promise<Contact> {
    const { data } = await api.patch(`/contacts/${id}/statut`, { statut })
    return data.data
  },
}
