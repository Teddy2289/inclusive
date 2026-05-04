<template>
    <div class="contacts-container">

        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Contacts</h1>
                <p class="page-description">Gérez l'ensemble de vos contacts et leurs rendez-vous</p>
            </div>
            <button class="btn-primary" @click="openModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" class="btn-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau contact
            </button>
        </div>

        <!-- Filtres -->
        <div class="filters-section">
            <div class="filters-header">
                <span class="filters-title">Filtres de recherche</span>
                <button class="reset-btn" @click="resetFilters">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" class="reset-icon">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Réinitialiser
                </button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Recherche</label>
                    <div class="input-icon">
                        <svg class="input-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input v-model="filters.search" class="finput" placeholder="Conseiller, fonction..."
                            @input="debouncedFetch" />
                    </div>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Statut</label>
                    <select v-model="filters.statut" class="fselect" @change="fetchData">
                        <option value="">Tous les statuts</option>
                        <option v-for="s in statutOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <div v-if="loading" class="loading-state">
                <div class="spinner"></div>
                <p>Chargement des contacts...</p>
            </div>

            <div v-else class="table-responsive">
                <table class="table">
                    <colgroup>
                        <col />
                        <col />
                        <col />
                        <col />
                        <col />
                        <col />
                        <col />
                        <col />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Conseiller</th>
                            <th>Partenaire</th>
                            <th>Fonction</th>
                            <th>Statut</th>
                            <th>Date RDV</th>
                            <th>Heure RDV</th>
                            <th>Téléphone</th>
                            <th class="th-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="c in contacts" :key="c.id" class="table-row">

                            <!-- Conseiller -->
                            <td>
                                <div class="conseiller-cell">
                                    <div class="avatar">{{ getInitials(c.conseiller) }}</div>
                                    <span class="conseiller-name">{{ c.conseiller ?? '—' }}</span>
                                </div>
                            </td>

                            <!-- Partenaire -->
                            <td>
                                <button v-if="c.partenaires?.length" class="badge-partenaires"
                                    @click="openPartenairesModal(c)">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                                        class="badge-ico">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18" />
                                    </svg>
                                    {{ c.partenaires.length }}
                                    {{ c.partenaires.length > 1 ? 'partenaires' : 'partenaire' }}
                                </button>
                                <span v-else class="text-muted">—</span>
                            </td>

                            <!-- Fonction -->
                            <td>
                                <span v-if="c.fonction" class="badge-fonction">{{ c.fonction }}</span>
                                <span v-else class="text-muted">—</span>
                            </td>

                            <!-- Statut -->
                            <td>
                                <StatutBadge :statut="c.statut" />
                            </td>

                            <!-- Date RDV -->
                            <td>
                                <div v-if="c.date_rdv" class="date-cell">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                                        class="cell-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                    {{ c.date_rdv }}
                                </div>
                                <span v-else class="text-muted">—</span>
                            </td>

                            <!-- Heure RDV -->
                            <td>
                                <div v-if="c.heure_rdv" class="date-cell">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                                        class="cell-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ c.heure_rdv }}
                                </div>
                                <span v-else class="text-muted">—</span>
                            </td>

                            <!-- Téléphone -->
                            <td>
                                <div v-if="c.tel" class="date-cell">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                                        class="cell-icon">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                    {{ c.tel }}
                                </div>
                                <span v-else class="text-muted">—</span>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit" @click="openModal(c)" title="Modifier">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                                            class="action-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                        </svg>
                                    </button>
                                    <button class="action-btn delete" @click="confirmDelete(c)" title="Supprimer">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                                            class="action-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="contacts.length === 0">
                            <td colspan="8" class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="empty-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                                <p class="empty-title">Aucun contact trouvé</p>
                                <p class="empty-description">Ajustez vos filtres ou créez un nouveau contact</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-container">
            <div class="pagination">
                <button v-for="(link, i) in pagination.links.filter(l => l.label !== null)" :key="i" class="page-btn"
                    :class="{ active: link.active, disabled: !link.url }" :disabled="!link.url" v-html="link.label"
                    @click="goToPage(link.url)" />
            </div>
        </div>

        <!-- Modals -->
        <ContactModal v-if="showModal" :contact="selected" :partenaire-id="selected?.partenaire_id ?? null" "
      :statut-options="statutOptions" @close="showModal = false" @saved="onSaved" />
        <ConfirmDialog v-if="showConfirm" message="Voulez-vous vraiment supprimer ce contact ?" @confirm="doDelete"
            @cancel="showConfirm = false" />
    </div>

    <!-- À ajouter AVANT </div> final, après les modals existants -->

    <!-- Modal liste partenaires -->
    <Teleport to="body">
        <div v-if="showPartenairesModal" class="overlay" @click.self="showPartenairesModal = false">
            <div class="modal-part">

                <div class="modal-part-header">
                    <div class="modal-part-title-wrap">
                        <div class="modal-part-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="modal-part-title">Partenaires liés</h2>
                            <p class="modal-part-sub">
                                {{ selectedForPart?.conseiller ?? '—' }} ·
                                {{ selectedForPart?.partenaires?.length ?? 0 }} partenaire(s)
                            </p>
                        </div>
                    </div>
                    <button class="modal-close" @click="showPartenairesModal = false">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <ul class="part-list">
                    <li v-for="p in selectedForPart?.partenaires" :key="p.id" class="part-item">
                        <div class="part-item-left">
                            <div class="part-avatar">
                                {{ p.raison_sociale?.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <RouterLink :to="`/partenaires/${p.id}`" class="part-name"
                                    @click="showPartenairesModal = false">
                                    {{ p.raison_sociale }}
                                </RouterLink>
                                <p v-if="p.ville" class="part-ville">{{ p.ville }}</p>
                            </div>
                        </div>
                        <RouterLink :to="`/partenaires/${p.id}`" class="part-link-btn"
                            @click="showPartenairesModal = false">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </RouterLink>
                    </li>
                </ul>

            </div>
        </div>
    </Teleport>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import type { Contact, PaginationLink, EnumOption } from '@/types'
import { contactService } from '@/services/contactService'
import api from '@/services/api'
import StatutBadge from '@/components/StatutBadge.vue'
import ContactModal from '@/components/contacts/ContactModal.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'

// ─── State ──────────────────────────────────────
const contacts = ref<Contact[]>([])
const loading = ref(false)
const showModal = ref(false)
const showConfirm = ref(false)
const selected = ref<Contact | null>(null)
const toDelete = ref<Contact | null>(null)
const statutOptions = ref<EnumOption[]>([])

const showPartenairesModal = ref(false)
const selectedForPart = ref<Contact | null>(null)

const filters = reactive({
    search: '',
    statut: '',
    per_page: 15,
    page: 1,
})

const pagination = reactive<{ links: PaginationLink[] }>({ links: [] })

// ─── Init ────────────────────────────────────────
onMounted(async () => {
    const { data } = await api.get('/enums')
    statutOptions.value = data.contact_statuts
    await fetchData()
})

// ─── Fetch ───────────────────────────────────────
async function fetchData(): Promise<void> {
    loading.value = true
    try {
        const result = await contactService.list(filters)
        contacts.value = result.data
        pagination.links = result.meta.links ?? []
    } finally {
        loading.value = false
    }
}
const getInitials = (name: string | null) => {
    if (!name) return '?'
    return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
}
const debouncedFetch = useDebounceFn(fetchData, 350)

function resetFilters(): void {
    filters.search = ''
    filters.statut = ''
    filters.page = 1
    fetchData()
}

function goToPage(url: string | null): void {
    if (!url) return
    const page = new URL(url).searchParams.get('page')
    if (page) { filters.page = parseInt(page); fetchData() }
}

// ─── CRUD ────────────────────────────────────────
function openModal(c: Contact | null = null): void {
    selected.value = c
    showModal.value = true
}
// ✅ ouvrir le modal partenaires
function openPartenairesModal(c: Contact): void {
    selectedForPart.value = c
    showPartenairesModal.value = true
}
function onSaved(): void {
    showModal.value = false
    fetchData()
}

function confirmDelete(c: Contact): void {
    toDelete.value = c
    showConfirm.value = true
}

async function doDelete(): Promise<void> {
    if (!toDelete.value) return
    await contactService.remove(toDelete.value.id)
    showConfirm.value = false
    fetchData()
}
</script>

<style scoped src="./style.css"></style>
