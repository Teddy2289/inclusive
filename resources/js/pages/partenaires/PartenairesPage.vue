<template>
    <div class="partenaires-container">
        <!-- En-tête avec gradient -->
        <div class="page-header">
            <div class="header-left">
                <h1 class="page-title">Gestion des Partenaires</h1>
                <p class="page-description">Gérez l'ensemble de vos partenaires et leurs informations</p>
            </div>
            <button class="btn-primary" @click="openModal()">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau partenaire
            </button>
        </div>

        <!-- Section filtres avancée -->
        <div class="filters-section">
            <div class="filters-header">
                <h3 class="filters-title">Filtres de recherche</h3>
                <button class="reset-btn" @click="resetFilters">
                    <svg class="reset-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Réinitialiser
                </button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Recherche</label>
                    <div class="input-icon">
                        <svg class="input-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input v-model="filters.search" class="input" placeholder="Raison sociale, contact..." @input="debouncedFetch" />
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Ville</label>
                    <div class="input-icon">
                        <svg class="input-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <input v-model="filters.ville" class="input" placeholder="Toutes les villes" @input="debouncedFetch" />
                    </div>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Statut</label>
                    <div class="select-wrapper">
                        <select v-model="filters.statut" class="select" @change="fetchData">
                            <option value="">Tous les statuts</option>
                            <option v-for="s in statutOptions" :key="s.value" :value="s.value">
                                {{ s.label }}
                            </option>
                        </select>
                        <svg class="select-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-value">{{ partenaires.length }}</span>
                <span class="stat-label">Partenaires affichés</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-value">{{ totalPartenaires }}</span>
                <span class="stat-label">Total général</span>
            </div>
        </div>

        <!-- Tableau des partenaires -->
        <div class="table-container">
            <div v-if="loading" class="loading-state">
                <div class="spinner"></div>
                <p>Chargement des partenaires...</p>
            </div>

            <div v-else class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Raison sociale</th>
                            <th>Ville</th>
                            <th>Secteur d'activité</th>
                            <th>Téléphone</th>
                            <th>Statut</th>
                            <th>Contacts</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in partenaires" :key="p.id" class="table-row">
                            <td class="td-highlight">
                                <div class="company-info">
                                    <div class="company-avatar">
                                        {{ getInitials(p.raison_sociale) }}
                                    </div>
                                    <span class="company-name">{{ p.raison_sociale }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="cell-content">
                                    <svg class="cell-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    {{ p.ville ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge-sector">{{ p.secteur_activite ?? 'Non spécifié' }}</span>
                            </td>
                            <td>
                                <div class="cell-content">
                                    <svg class="cell-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ p.telephone_1 ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <StatutBadge :statut="p.statut" />
                            </td>
                            <td>
                                <RouterLink :to="`/partenaires/${p.id}`" class="link-contacts">
                                    <svg class="link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Voir contacts
                                </RouterLink>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn edit" @click="openModal(p)" title="Modifier">
                                        <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button class="action-btn delete" @click="confirmDelete(p)" title="Supprimer">
                                        <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="partenaires.length === 0">
                            <td colspan="7" class="empty-state">
                                <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="empty-title">Aucun partenaire trouvé</p>
                                <p class="empty-description">Ajustez vos filtres ou créez un nouveau partenaire</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination moderne -->
        <div class="pagination-container">
            <div class="pagination-info">
                Affichage de {{ (filters.page - 1) * filters.per_page + 1 }} à
                {{ Math.min(filters.page * filters.per_page, totalPartenaires) }} sur {{ totalPartenaires }} partenaires
            </div>
            <div class="pagination">
                <button v-for="(link, i) in pagination.links.filter(l => l.label !== null)" :key="i"
                    class="page-btn" :class="{ active: link.active, disabled: !link.url }"
                    :disabled="!link.url" v-html="link.label" @click="goToPage(link.url)" />
            </div>
        </div>

        <!-- Modals -->
        <PartenaireModal v-if="showModal" :partenaire="selected" :statut-options="statutOptions"
            @close="showModal = false" @saved="onSaved" />
        <ConfirmDialog v-if="showConfirm" message="Voulez-vous vraiment supprimer ce partenaire ?"
            @confirm="doDelete" @cancel="showConfirm = false" />
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import type { Partenaire, PaginationLink, EnumOption } from '@/types'
import { partenaireService } from '@/services/partenaireService'
import api from '@/services/api'
import StatutBadge from '@/components/StatutBadge.vue'
import PartenaireModal from '@/components/partenaires/PartenaireModal.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'

const partenaires = ref<Partenaire[]>([])
const loading = ref(false)
const showModal = ref(false)
const showConfirm = ref(false)
const selected = ref<Partenaire | null>(null)
const toDelete = ref<Partenaire | null>(null)
const statutOptions = ref<EnumOption[]>([])
const totalPartenaires = ref(0)

const filters = reactive({
    search: '',
    ville: '',
    statut: '',
    per_page: 15,
    page: 1,
})

const pagination = reactive<{ links: PaginationLink[] }>({ links: [] })

const getInitials = (name: string) => {
    return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
}

onMounted(async () => {
    const { data } = await api.get('/enums')
    statutOptions.value = (data.partenaire_statuts ?? []).filter(Boolean)
    await fetchData()
})

async function fetchData(): Promise<void> {
    loading.value = true
    try {
        const result = await partenaireService.list(filters)
        partenaires.value = result.data
        pagination.links = result.meta.links ?? []
        totalPartenaires.value = result.meta.total
    } finally {
        loading.value = false
    }
}

const debouncedFetch = useDebounceFn(fetchData, 350)

function resetFilters(): void {
    filters.search = ''
    filters.ville = ''
    filters.statut = ''
    filters.page = 1
    fetchData()
}

function goToPage(url: string | null): void {
    if (!url) return
    const page = new URL(url).searchParams.get('page')
    if (page) { filters.page = parseInt(page); fetchData() }
}

function openModal(p: Partenaire | null = null): void {
    selected.value = p
    showModal.value = true
}

function onSaved(): void {
    showModal.value = false
    fetchData()
}

function confirmDelete(p: Partenaire): void {
    toDelete.value = p
    showConfirm.value = true
}

async function doDelete(): Promise<void> {
    if (!toDelete.value) return
    await partenaireService.remove(toDelete.value.id)
    showConfirm.value = false
    fetchData()
}
</script>
<style scoped src="./styles/partenaire.css">
</style>
