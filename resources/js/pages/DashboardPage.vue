<template>
    <div class="dashboard">

        <!-- Header -->
        <div class="dash-header">
            <div>
                <h1 class="dash-title">Tableau de bord</h1>
                <p class="dash-sub">Bienvenue sur votre espace de gestion</p>
            </div>
            <div class="import-zone">
                <input type="file" ref="fileInput" @change="handleFileUpload" accept=".xlsx,.xls"
                    style="display:none" />
                <button @click="fileInput?.click()" class="btn-import" :disabled="importing">
                    <span class="btn-inner">
                        <svg v-if="importing" class="spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                                stroke-dasharray="31.4" stroke-dashoffset="10" />
                        </svg>
                        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ico">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        {{ importing ? 'Importation...' : 'Importer Excel' }}
                    </span>
                </button>
                <!-- Dans dash-header, après le bouton Import -->
                <button @click="handleClearCrm" class="btn-danger" :disabled="clearing">
                    <span class="btn-inner">
                        <svg v-if="clearing" class="spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                                stroke-dasharray="31.4" stroke-dashoffset="10" />
                        </svg>
                        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ico">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ clearing ? 'Vidage...' : 'Vider CRM' }}
                    </span>
                </button>

                <button @click="handleResyncAll" class="btn-sync" :disabled="syncing">
                    <span class="btn-inner">
                        <svg v-if="syncing" class="spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                                stroke-dasharray="31.4" stroke-dashoffset="10" />
                        </svg>
                        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ico">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ syncing ? 'Lancement...' : 'Resync CRM' }}
                    </span>
                </button>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid" v-if="stats">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
                <div>
                    <p class="kpi-label">Total partenaires</p>
                    <p class="kpi-value">{{ stats.partenaires.total }}</p>
                </div>
            </div>

            <div class="kpi-card kpi-green">
                <div class="kpi-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="kpi-label">Synchronisés CRM</p>
                    <p class="kpi-value">{{ stats.partenaires.synced }}</p>
                </div>
            </div>

            <div class="kpi-card kpi-orange">
                <div class="kpi-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="kpi-label">En attente sync</p>
                    <p class="kpi-value">{{ stats.partenaires.not_synced }}</p>
                </div>
            </div>

            <div class="kpi-card kpi-purple">
                <div class="kpi-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div>
                    <p class="kpi-label">Total contacts</p>
                    <p class="kpi-value">{{ stats.contacts.total }}</p>
                </div>
            </div>
        </div>

        <!-- Skeleton KPI -->
        <div class="kpi-grid" v-else>
            <div v-for="i in 4" :key="i" class="kpi-card skeleton-card">
                <div class="skeleton sk-ico"></div>
                <div>
                    <div class="skeleton sk-label"></div>
                    <div class="skeleton sk-value"></div>
                </div>
            </div>
        </div>

        <!-- Sync Progress Banner -->
        <div class="sync-banner" v-if="syncStatus && (syncStatus.pending > 0 || syncStatus.percent < 100)">
            <div class="sync-banner-header">
                <div class="sync-banner-left">
                    <div class="sync-pulse"></div>
                    <span class="sync-title">Synchronisation vTiger CRM en cours</span>
                    <span class="sync-badge">{{ syncStatus.pending }} job{{ syncStatus.pending > 1 ? 's' : '' }} en
                        attente</span>
                </div>
                <span class="sync-percent">{{ syncStatus.percent }}%</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" :style="{ width: syncStatus.percent + '%' }"></div>
            </div>
            <div class="sync-footer">
                <span>{{ syncStatus.synced }} / {{ syncStatus.total }} partenaires synchronisés</span>
                <span v-if="syncStatus.failed > 0" class="sync-failed">⚠ {{ syncStatus.failed }} échec(s)</span>
            </div>
        </div>

        <!-- Sync Complete Banner -->
        <div class="sync-complete" v-else-if="syncStatus && syncStatus.percent === 100 && syncStatus.total > 0">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="check-ico">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Tous les partenaires sont synchronisés avec vTiger CRM
        </div>

        <!-- Content grid -->
        <div class="content-grid">

            <!-- Imports récents -->
            <div class="card">
                <div class="card-head">
                    <h2 class="card-title">Importations récentes</h2>
                    <span class="tag">Excel</span>
                </div>
                <div class="card-body">
                    <div v-if="!recentImports.length" class="empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="empty-ico">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p>Aucune importation récente</p>
                        <small>Cliquez sur "Importer Excel" pour commencer</small>
                    </div>
                    <div v-else class="import-list">
                        <div v-for="item in recentImports" :key="item.id" class="import-row">
                            <div class="import-file-ico">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="import-info">
                                <p class="import-name">{{ item.original_filename }}</p>
                                <p class="import-meta" v-if="item.status === 'success'">
                                    {{ item.rows_imported }} lignes importées
                                    <span v-if="item.rows_skipped > 0"> · {{ item.rows_skipped }} ignorées</span>
                                </p>
                                <p class="import-date">
                                    {{ item.user?.name ?? 'Système' }} · {{ formatDate(item.created_at) }}
                                </p>
                                <p class="import-error" v-if="item.error_message">{{ item.error_message }}</p>
                            </div>
                            <span class="status-pill" :class="'s-' + item.status">
                                {{ statusLabel(item.status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activité récente -->
            <div class="card">
                <div class="card-head">
                    <h2 class="card-title">Activité récente</h2>
                    <select class="mini-select" v-model="activityFilter">
                        <option value="all">Tous</option>
                        <option value="import">Imports</option>
                        <option value="contact">Contacts</option>
                        <option value="sync">Sync CRM</option>
                    </select>
                </div>
                <div class="card-body">
                    <div v-if="!filteredActivities.length" class="empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="empty-ico">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                        </svg>
                        <p>Aucune activité</p>
                    </div>
                    <div v-else class="activity-list">
                        <div v-for="act in filteredActivities" :key="act.id" class="activity-row">
                            <span class="act-dot" :class="'dot-' + act.type"></span>
                            <div class="act-info">
                                <p class="act-msg">{{ act.message }}</p>
                                <p class="act-time">{{ formatRelativeTime(act.createdAt) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Failed jobs alert -->
        <div class="failed-alert" v-if="syncStatus && syncStatus.failed > 0">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="alert-ico">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <div>
                <strong>{{ syncStatus.failed }} job(s) ont échoué</strong>
                <p>Consultez les logs ou relancez : <code>php artisan queue:retry all</code></p>
            </div>
        </div>

        <!-- Feedback toast -->
        <transition name="toast">
            <div v-if="feedback" class="toast" :class="'toast-' + feedback.type">
                <svg v-if="feedback.type === 'success'" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" class="toast-ico">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="toast-ico">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ feedback.message }}
            </div>
        </transition>

    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { dashboardService } from '@/services/DashboardService'
import type { DashboardStats, ImportItem, SyncStatus } from '@/services/DashboardService'
import { importService } from '@/services/importService'



interface Activity {
    id: number
    type: string
    message: string
    createdAt: string
}

const importing = ref(false)
const feedback = ref<{ message: string; type: 'success' | 'error' } | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)
const stats = ref<DashboardStats | null>(null)
const syncStatus = ref<SyncStatus | null>(null)
const recentImports = ref<ImportItem[]>([])
const activities = ref<Activity[]>([])
const activityFilter = ref('all')
let pollInterval: ReturnType<typeof setInterval> | null = null

const filteredActivities = computed(() =>
    activityFilter.value === 'all'
        ? activities.value
        : activities.value.filter(a => a.type === activityFilter.value)
)

// ── Fetch ─────────────────────────────────────────
async function loadStats(): Promise<void> {
    try {
        stats.value = await dashboardService.stats()
    } catch { }
}

async function loadRecentImports(): Promise<void> {
    try {
        recentImports.value = await dashboardService.recentImports()
    } catch { }
}

async function loadSyncStatus(): Promise<void> {
    try {
        syncStatus.value = await dashboardService.syncStatus()

        // Ajouter activité sync si jobs terminés
        if (syncStatus.value.pending === 0 && syncStatus.value.synced > 0) {
            const last = activities.value[0]
            if (!last || last.type !== 'sync') {
                activities.value.unshift({
                    id: Date.now(),
                    type: 'sync',
                    message: `${syncStatus.value.synced} partenaires synchronisés avec vTiger`,
                    createdAt: new Date().toISOString(),
                })
            }
        }
    } catch { }
}

// ── Import ────────────────────────────────────────
async function handleFileUpload(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0]
    if (!file) return

    importing.value = true
    feedback.value = null

    try {
        const response = await importService.importPartenaires(file)
        feedback.value = { message: response.message, type: 'success' }

        await loadRecentImports()
        activities.value.unshift({
            id: Date.now(), type: 'import',
            message: `Import de ${file.name}`,
            createdAt: new Date().toISOString(),
        })
        if (recentImports.value.length > 5) recentImports.value.pop()
        if (activities.value.length > 20) activities.value.pop()

        // Recharger stats + lancer polling sync
        await loadStats()
        startPolling()
        autoHide('success', 3000)
    } catch (e: any) {
        feedback.value = {
            message: e.response?.data?.message || "Erreur lors de l'import",
            type: 'error',
        }
        recentImports.value.unshift({
            id: Date.now(), nomFichier: file.name,
            dateImport: new Date().toISOString(), statut: 'error',
        })
        autoHide('error', 5000)
    } finally {
        importing.value = false
        if (fileInput.value) fileInput.value.value = ''
    }
}

// ── Polling sync status ───────────────────────────
function startPolling() {
    if (pollInterval) return
    pollInterval = setInterval(async () => {
        await loadSyncStatus()
        await loadStats()
        if (syncStatus.value?.pending === 0) stopPolling()
    }, 3000)
}

function stopPolling() {
    if (pollInterval) { clearInterval(pollInterval); pollInterval = null }
}

function autoHide(type: string, delay: number) {
    setTimeout(() => {
        if (feedback.value?.type === type) feedback.value = null
    }, delay)
}

// ── Formatters ────────────────────────────────────
const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })

const formatRelativeTime = (d: string) => {
    const diff = Date.now() - new Date(d).getTime()
    const m = Math.floor(diff / 60000)
    const h = Math.floor(diff / 3600000)
    const j = Math.floor(diff / 86400000)
    if (m < 1) return "À l'instant"
    if (m < 60) return `Il y a ${m} min`
    if (h < 24) return `Il y a ${h}h`
    return `Il y a ${j}j`
}

const clearing = ref(false)
const syncing = ref(false)

async function handleClearCrm() {
    if (!confirm('Vider tous les Leads vTiger et remettre à zéro ?')) return
    clearing.value = true
    try {
        const res = await dashboardService.clearCrm()
        feedback.value = { message: res.message, type: 'success' }
        await loadStats()
        autoHide('success', 3000)
    } catch {
        feedback.value = { message: 'Erreur lors du vidage CRM', type: 'error' }
        autoHide('error', 5000)
    } finally {
        clearing.value = false
    }
}

async function handleResyncAll() {
    if (!confirm('Relancer la synchronisation pour tous les partenaires ?')) return
    syncing.value = true
    try {
        const res = await dashboardService.resyncAll()
        feedback.value = { message: res.message, type: 'success' }
        startPolling()
        autoHide('success', 3000)
    } catch {
        feedback.value = { message: 'Erreur lors de la resync', type: 'error' }
        autoHide('error', 5000)
    } finally {
        syncing.value = false
    }
}

const statusLabel = (s: string) =>
    ({ success: 'Succès', error: 'Erreur', pending: 'En cours' }[s] ?? s)

// ── Lifecycle ─────────────────────────────────────
onMounted(async () => {
    await Promise.all([loadStats(), loadSyncStatus(), loadRecentImports()])
    // Si des jobs sont en attente au chargement, on poll
    if (syncStatus.value && syncStatus.value.pending > 0) startPolling()
})

onUnmounted(() => stopPolling())
</script>
<style scoped src="./styles/style.css"></style>
