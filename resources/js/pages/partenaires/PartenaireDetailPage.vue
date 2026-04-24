<template>
  <div v-if="partenaire">
    <!-- Header -->
    <div class="page-header">
      <div>
        <RouterLink to="/partenaires" class="back-link">← Partenaires</RouterLink>
        <h1 class="page-title">{{ partenaire.raison_sociale }}</h1>
      </div>
      <StatutBadge :statut="partenaire.statut" size="lg" />
    </div>

    <!-- Infos -->
    <div class="info-grid">
      <div class="info-card">
        <h3>Informations générales</h3>
        <dl>
          <div class="dl-row"><dt>Adresse</dt><dd>{{ partenaire.adresse ?? '—' }}</dd></div>
          <div class="dl-row"><dt>CP / Ville</dt><dd>{{ partenaire.cp }} {{ partenaire.ville }}</dd></div>
          <div class="dl-row"><dt>Secteur</dt><dd>{{ partenaire.secteur_activite ?? '—' }}</dd></div>
          <div class="dl-row"><dt>Salariés</dt><dd>{{ partenaire.nbrs_salaries ?? '—' }}</dd></div>
          <div class="dl-row"><dt>CA</dt><dd>{{ partenaire.ca ? `${partenaire.ca} €` : '—' }}</dd></div>
          <div class="dl-row"><dt>SIRET</dt><dd>{{ partenaire.siret ?? '—' }}</dd></div>
          <div class="dl-row"><dt>Tél. 1</dt><dd>{{ partenaire.telephone_1 ?? '—' }}</dd></div>
          <div class="dl-row"><dt>Tél. 2</dt><dd>{{ partenaire.telephone_2 ?? '—' }}</dd></div>
        </dl>
      </div>

      <!-- Changer statut -->
      <div class="info-card">
        <h3>Changer le statut</h3>
        <div class="statut-actions">
          <button
            v-for="t in transitions"
            :key="t.value"
            class="btn-transition"
            :style="{ borderColor: colorMap[t.color], color: colorMap[t.color] }"
            @click="changerStatut(t.value)"
          >
            {{ t.label }}
          </button>
          <p v-if="transitions.length === 0" class="no-transitions">Aucune transition disponible.</p>
        </div>
      </div>
    </div>

    <!-- Contacts liés -->
    <div class="section">
      <div class="section-header">
        <h2>Contacts liés</h2>
        <button class="btn-primary" @click="openContactModal()">+ Ajouter contact</button>
      </div>

      <!-- Filtres contacts -->
      <div class="filters">
        <input v-model="contactFilters.search" class="input" placeholder="Rechercher..." @input="debouncedFetchContacts" />
        <select v-model="contactFilters.statut" class="input" @change="fetchContacts">
          <option value="">Tous les statuts</option>
          <option v-for="s in contactStatutOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
        </select>
      </div>

      <div class="table-wrapper">
        <table class="table">
          <thead>
            <tr>
              <th>Conseiller</th>
              <th>Fonction</th>
              <th>Statut</th>
              <th>Date RDV</th>
              <th>Heure RDV</th>
              <th>Tél</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in contacts" :key="c.id">
              <td class="td-bold">{{ c.conseiller ?? '—' }}</td>
              <td>{{ c.fonction ?? '—' }}</td>
              <td><StatutBadge :statut="c.statut" /></td>
              <td>{{ c.date_rdv ?? '—' }}</td>
              <td>{{ c.heure_rdv ?? '—' }}</td>
              <td>{{ c.tel ?? '—' }}</td>
              <td class="td-actions">
                <button class="btn-icon edit"   @click="openContactModal(c)">✏️</button>
                <button class="btn-icon danger" @click="confirmDeleteContact(c)">🗑️</button>
              </td>
            </tr>
            <tr v-if="contacts.length === 0">
              <td colspan="7" class="td-empty">Aucun contact lié.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modals -->
    <ContactModal
      v-if="showContactModal"
      :contact="selectedContact"
      :partenaire-id="partenaire.id"
      :statut-options="contactStatutOptions"
      @close="showContactModal = false"
      @saved="onContactSaved"
    />

    <ConfirmDialog
      v-if="showConfirm"
      message="Supprimer ce contact ?"
      @confirm="doDeleteContact"
      @cancel="showConfirm = false"
    />
  </div>

  <div v-else class="loading">Chargement...</div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted }  from 'vue'
import { useRoute }                  from 'vue-router'
import { useDebounceFn }             from '@vueuse/core'
import type { Partenaire, Contact, EnumOption } from '@/types'
import { partenaireService }         from '@/services/partenaireService'
import { contactService }            from '@/services/contactService'
import api                           from '@/services/api'
import StatutBadge                   from '@/components/StatutBadge.vue'
import ContactModal                  from '@/components/contacts/ContactModal.vue'
import ConfirmDialog                 from '@/components/ConfirmDialog.vue'

const route       = useRoute()
const id          = Number(route.params.id)

const partenaire          = ref<Partenaire | null>(null)
const contacts            = ref<Contact[]>([])
const transitions         = ref<any[]>([])
const contactStatutOptions = ref<EnumOption[]>([])
const showContactModal    = ref(false)
const showConfirm         = ref(false)
const selectedContact     = ref<Contact | null>(null)
const toDeleteContact     = ref<Contact | null>(null)

const colorMap: Record<string, string> = {
  gray: '#6b7280', blue: '#3b82f6', purple: '#8b5cf6',
  yellow: '#d97706', red: '#ef4444', green: '#16a34a', emerald: '#059669',
}

const contactFilters = reactive({ search: '', statut: '' })

onMounted(async () => {
  const { data } = await api.get('/enums')
  contactStatutOptions.value = data.contact_statuts
  await Promise.all([loadPartenaire(), fetchContacts()])
})

async function loadPartenaire(): Promise<void> {
  partenaire.value = await partenaireService.find(id)
  const t          = await partenaireService.transitions(id)
  transitions.value = t.transitions
}

async function fetchContacts(): Promise<void> {
  const result  = await contactService.list({ partenaire_id: id, ...contactFilters })
  contacts.value = result.data
}

const debouncedFetchContacts = useDebounceFn(fetchContacts, 350)

async function changerStatut(statut: string): Promise<void> {
  await partenaireService.changerStatut(id, statut)
  await loadPartenaire()
}

function openContactModal(c: Contact | null = null): void {
  selectedContact.value  = c
  showContactModal.value = true
}

function onContactSaved(): void {
  showContactModal.value = false
  fetchContacts()
}

function confirmDeleteContact(c: Contact): void {
  toDeleteContact.value = c
  showConfirm.value     = true
}

async function doDeleteContact(): Promise<void> {
  if (!toDeleteContact.value) return
  await contactService.remove(toDeleteContact.value.id)
  showConfirm.value = false
  fetchContacts()
}
</script>

<style scoped>
.page-header   { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; }
.page-title    { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-top: .25rem; }
.back-link     { color: #64748b; font-size: .875rem; text-decoration: none; }
.info-grid     { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 2rem; }
.info-card     { background: #fff; border-radius: 14px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,.05); }
.info-card h3  { font-size: 1rem; font-weight: 600; color: #1e293b; margin-bottom: 1rem; }
.dl-row        { display: flex; justify-content: space-between; padding: .4rem 0; border-bottom: 1px solid #f1f5f9; font-size: .875rem; }
.dl-row dt     { color: #64748b; }
.dl-row dd     { color: #0f172a; font-weight: 500; }
.statut-actions { display: flex; flex-wrap: wrap; gap: .6rem; }
.btn-transition { padding: .5rem 1rem; border-radius: 8px; border: 2px solid; background: transparent; cursor: pointer; font-weight: 600; font-size: .875rem; transition: all .2s; }
.btn-transition:hover { opacity: .85; }
.no-transitions { color: #94a3b8; font-size: .875rem; }
.section       { margin-top: 1.5rem; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
.section-header h2 { font-size: 1.2rem; font-weight: 700; color: #1e293b; }
.filters       { display: flex; gap: .75rem; margin-bottom: 1rem; }
.input         { padding: .55rem .75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: .875rem; }
.btn-primary   { background: #0ea5e9; color: #fff; border: none; padding: .55rem 1.1rem; border-radius: 8px; font-weight: 600; cursor: pointer; }
.table-wrapper { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.05); }
.table         { width: 100%; border-collapse: collapse; font-size: .875rem; }
.table th      { padding: .9rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: .8rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
.table td      { padding: .85rem 1rem; border-bottom: 1px solid #f1f5f9; color: #334155; }
.td-bold       { font-weight: 600; }
.td-actions    { display: flex; gap: .5rem; }
.td-empty      { text-align: center; color: #94a3b8; padding: 2rem; }
.btn-icon      { background: none; border: none; cursor: pointer; font-size: 1rem; padding: .25rem; border-radius: 6px; }
.btn-icon.edit:hover   { background: #dbeafe; }
.btn-icon.danger:hover { background: #fee2e2; }
.loading       { text-align: center; padding: 3rem; color: #64748b; }
</style>
