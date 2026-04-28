<template>
  <div class="crm-page">
    <div class="page-header">
      <h1 class="page-title">Comptes CRM</h1>
      <span class="crm-badge">vTiger</span>
    </div>

    <div class="filters">
      <input
        v-model="search"
        class="input"
        placeholder="Rechercher un compte..."
        @input="debouncedFetch"
      />
    </div>

    <div class="table-container">
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Chargement depuis vTiger CRM...</p>
      </div>

      <table v-else class="table">
        <thead>
          <tr>
            <th>Nom du compte</th>
            <th>Ville</th>
            <th>Téléphone</th>
            <th>Secteur</th>
            <th>Effectif</th>
            <th>Statut Prospect</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="a in accounts" :key="a.id">
            <td class="td-bold">{{ a.accountname }}</td>
            <td>{{ a.bill_city || '—' }}</td>
            <td>{{ a.phone || '—' }}</td>
            <td>{{ a.industry || '—' }}</td>
            <td>{{ a.employees || '—' }}</td>
            <td>
              <span class="statut-badge" :class="getStatutClass(a.statut_prospect)">
                {{ a.statut_prospect || '—' }}
              </span>
            </td>
          </tr>
          <tr v-if="accounts.length === 0">
            <td colspan="6" class="empty">Aucun compte trouvé.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <button
        v-for="p in totalPages"
        :key="p"
        class="page-btn"
        :class="{ active: p === currentPage }"
        @click="goToPage(p)"
      >{{ p }}</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { crmService, type CrmAccount } from '@/services/crmService'

const accounts    = ref<CrmAccount[]>([])
const loading     = ref(false)
const search      = ref('')
const currentPage = ref(1)
const total       = ref(0)
const perPage     = ref(20)

const totalPages = computed(() => Math.ceil(total.value / perPage.value))

async function fetchData(): Promise<void> {
  loading.value = true
  try {
    const result      = await crmService.accounts({
      search  : search.value,
      page    : currentPage.value,
      per_page: perPage.value,
    })
    accounts.value = result.data
    total.value    = result.meta.total
  } finally {
    loading.value = false
  }
}

const debouncedFetch = useDebounceFn(fetchData, 400)

function goToPage(p: number): void {
  currentPage.value = p
  fetchData()
}

function getStatutClass(statut: string): string {
  if (!statut) return ''
  if (statut.includes('QF'))  return 's-qf'
  if (statut.includes('KO'))  return 's-ko'
  if (statut.includes('RPC')) return 's-rpc'
  if (statut.includes('AC'))  return 's-ac'
  return 's-default'
}

onMounted(fetchData)
</script>

<style scoped>
.crm-page      { padding: 2rem; background: #f8fafc; min-height: 100vh; }
.page-header   { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
.page-title    { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0; }
.crm-badge     { background: #0ea5e9; color: #fff; padding: .25rem .75rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
.filters       { margin-bottom: 1.25rem; }
.input         { padding: .575rem .75rem; border: 1px solid #e2e8f0; border-radius: .5rem; font-size: .875rem; width: 300px; }
.table-container { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.table         { width: 100%; border-collapse: collapse; font-size: .875rem; }
.table th      { padding: .9rem 1rem; text-align: left; color: #64748b; font-size: .75rem; font-weight: 600; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
.table td      { padding: .85rem 1rem; border-bottom: 1px solid #f1f5f9; color: #334155; }
.td-bold       { font-weight: 600; color: #0f172a; }
.empty         { text-align: center; padding: 3rem; color: #94a3b8; }
.statut-badge  { padding: .2rem .65rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
.s-qf          { background: #dcfce7; color: #15803d; }
.s-ko          { background: #fee2e2; color: #b91c1c; }
.s-rpc         { background: #dbeafe; color: #1d4ed8; }
.s-ac          { background: #f1f5f9; color: #475569; }
.s-default     { background: #f1f5f9; color: #475569; }
.loading       { text-align: center; padding: 3rem; color: #64748b; }
.spinner       { width: 2.5rem; height: 2.5rem; border: 3px solid #e2e8f0; border-top-color: #0ea5e9; border-radius: 50%; margin: 0 auto 1rem; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.pagination    { display: flex; gap: .4rem; margin-top: 1rem; justify-content: center; }
.page-btn      { padding: .4rem .7rem; border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; cursor: pointer; font-size: .825rem; }
.page-btn.active { background: #0ea5e9; color: #fff; border-color: #0ea5e9; }
</style>
