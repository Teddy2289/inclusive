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

export interface ImportItem {
  id                : number
  original_filename : string
  rows_imported     : number
  rows_skipped      : number
  status            : 'pending' | 'success' | 'failed'
  error_message     : string | null
  user              : { name: string } | null
  created_at        : string
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

  async clearCrm(): Promise<{ message: string; deleted: number }> {
    const { data } = await api.post('/dashboard/clear-crm')
    return data
  },

  async resyncAll(): Promise<{ message: string; dispatched: number }> {
    const { data } = await api.post('/dashboard/resync-all')
    return data
  },

  async recentImports(): Promise<ImportItem[]> {
    const { data } = await api.get('/imports/history')
    return data.data ?? data
  },
}
