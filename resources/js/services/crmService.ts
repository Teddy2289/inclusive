import api from './api'

export interface CrmAccount {
  id              : string
  accountname     : string
  phone           : string
  bill_city       : string
  bill_code       : string
  industry        : string
  employees       : string
  annual_revenue  : string
  accounttype     : string
  statut_prospect : string
  siccode         : string
}

export interface CrmFilters {
  search?  : string
  page?    : number
  per_page?: number
}

export const crmService = {
  async accounts(filters: CrmFilters = {}): Promise<{
    data: CrmAccount[]
    meta: { current_page: number; per_page: number; total: number; last_page: number }
  }> {
    const { data } = await api.get('/crm/accounts', { params: filters })
    return data
  },
}
