import api from './api'

export interface DashboardStats {
  partenaires: {
    total      : number
    synced     : number
    not_synced : number
    deleted    : number
  }
  contacts: {
    total  : number
    synced : number
  }
  jobs: {
    pending : number
    failed  : number
  }
}

export interface SyncStatus {
  pending : number
  failed  : number
  synced  : number
  total   : number
  percent : number
}

export const dashboardService = {
  async stats(): Promise<DashboardStats> {
    const { data } = await api.get('/dashboard/stats')
    return data
  },

  async syncStatus(): Promise<SyncStatus> {
    const { data } = await api.get('/dashboard/sync-status')
    return data
  },
}
