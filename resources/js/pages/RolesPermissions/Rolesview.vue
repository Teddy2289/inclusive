<template>
    <div class="roles-container">
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des rôles</h1>
                <p class="page-description">Définissez les rôles et leurs permissions associées</p>
            </div>
            <button @click="openCreateModal" class="btn-primary">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Nouveau rôle
            </button>
        </div>

        <!-- Search -->
        <div class="search-section">
            <div class="input-icon">
                <svg class="input-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Rechercher un rôle..."
                    @input="debouncedSearch"
                    class="finput"
                />
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table">
                    <colgroup>
                        <col style="width: 20%" />
                        <col style="width: 50%" />
                        <col style="width: 15%" />
                        <col style="width: 15%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Rôle</th>
                            <th>Permissions</th>
                            <th>Date de création</th>
                            <th class="th-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roles" :key="role.id" class="table-row">
                            <td>
                                <div class="role-cell">
                                    <div class="role-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <span class="role-name">{{ role.name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="badges-container">
                                    <template v-if="role.permissions && role.permissions.length">
                                        <span
                                            v-for="perm in role.permissions.slice(0, 4)"
                                            :key="perm.id"
                                            class="badge badge-perm"
                                        >
                                            {{ formatPermission(perm.name) }}
                                        </span>
                                        <span
                                            v-if="role.permissions.length > 4"
                                            class="badge badge-more"
                                        >
                                            +{{ role.permissions.length - 4 }} autres
                                        </span>
                                    </template>
                                    <span v-else class="badge badge-secondary">Aucune permission</span>
                                </div>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <svg class="cell-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ formatDate(role.created_at as string) }}
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button @click="openEditModal(role)" class="action-btn edit" title="Modifier">
                                        <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(role)" class="action-btn delete" title="Supprimer">
                                        <svg class="action-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!roles.length && !loading">
                            <td colspan="4">
                                <div class="empty-state">
                                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <p class="empty-title">Aucun rôle</p>
                                    <p class="empty-description">Cliquez sur "Nouveau rôle" pour en créer un</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container" v-if="meta && meta.last_page > 1">
                <div class="pagination">
                    <button @click="changePage(1)" :disabled="meta.current_page === 1" class="page-btn">«</button>
                    <button @click="changePage(meta.current_page - 1)" :disabled="meta.current_page === 1" class="page-btn">‹</button>
                    <button
                        v-for="page in displayedPages" :key="page"
                        @click="typeof page === 'number' && changePage(page)"
                        class="page-btn"
                        :class="{ active: page === meta.current_page, dots: page === '...' }"
                    >{{ page }}</button>
                    <button @click="changePage(meta.current_page + 1)" :disabled="meta.current_page === meta.last_page" class="page-btn">›</button>
                    <button @click="changePage(meta.last_page)" :disabled="meta.current_page === meta.last_page" class="page-btn">»</button>
                </div>
                <div class="pagination-info">
                    Page {{ meta.current_page }} sur {{ meta.last_page }} • {{ meta.total }} rôle(s)
                </div>
            </div>
        </div>

        <!-- Modals -->
        <RoleModal
            v-if="showModal"
            :role="selectedRole"
            :permissions="allPermissions"
            @close="closeModal"
            @save="handleSave"
        />

        <ConfirmModal
            v-if="showDeleteConfirm"
            title="Supprimer le rôle"
            :message="`Êtes-vous sûr de vouloir supprimer le rôle « ${roleToDelete?.name} » ?`"
            details="Les utilisateurs ayant ce rôle le perdront immédiatement."
            confirm-text="Supprimer"
            cancel-text="Annuler"
            type="danger"
            @confirm="deleteRole"
            @cancel="cancelDelete"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { roleService } from "@/services/Rolepermissionservice";
import { permissionService } from "@/services/Rolepermissionservice";
import RoleModal from "@/components/RolePermission/RoleModal.vue";
import ConfirmModal from "@/components/ConfirmModal.vue";
import type { Role, Permission, Paginated } from "@/types";

const roles = ref<Role[]>([]);
const allPermissions = ref<Permission[]>([]);
const search = ref("");
const loading = ref(false);
const meta = ref<Paginated<Role>["meta"] | null>(null);
const showModal = ref(false);
const showDeleteConfirm = ref(false);
const selectedRole = ref<Role | null>(null);
const roleToDelete = ref<Role | null>(null);
let debounceTimeout: any;

const displayedPages = computed(() => {
    if (!meta.value) return [];
    const current = meta.value.current_page;
    const last = meta.value.last_page;
    const range: number[] = [];
    for (let i = 1; i <= last; i++) {
        if (i === 1 || i === last || (i >= current - 2 && i <= current + 2)) {
            range.push(i);
        }
    }
    const result: (number | string)[] = [];
    let prev: number | undefined;
    for (const i of range) {
        if (prev && i - prev === 2) result.push(prev + 1);
        else if (prev && i - prev > 2) result.push("...");
        result.push(i);
        prev = i;
    }
    return result;
});

const formatDate = (date: string) =>
    date ? new Date(date).toLocaleDateString("fr-FR", { day: "2-digit", month: "2-digit", year: "numeric" }) : "—";

const formatPermission = (name: string) =>
    name.split(".").map((w) => w.charAt(0).toUpperCase() + w.slice(1)).join(" › ");

const loadRoles = async () => {
    loading.value = true;
    try {
        const response = await roleService.getRoles({
            page: meta.value?.current_page || 1,
            search: search.value,
        });
        roles.value = response.data;
        meta.value = response.meta;
    } catch (error) {
        console.error("Erreur chargement rôles:", error);
    } finally {
        loading.value = false;
    }
};

const loadPermissions = async () => {
    try {
        // Charger toutes les permissions sans pagination pour les checkboxes
        const response = await permissionService.getPermissions({ per_page: 999 } as any);
        allPermissions.value = response.data;
    } catch (error) {
        console.error("Erreur chargement permissions:", error);
    }
};

const debouncedSearch = () => {
    if (debounceTimeout) clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        if (meta.value) meta.value.current_page = 1;
        loadRoles();
    }, 300);
};

const changePage = (page: number) => {
    if (meta.value && page >= 1 && page <= meta.value.last_page) {
        meta.value.current_page = page;
        loadRoles();
    }
};

const openCreateModal = () => { selectedRole.value = null; showModal.value = true; };
const openEditModal = (role: Role) => { selectedRole.value = role; showModal.value = true; };
const closeModal = () => { showModal.value = false; selectedRole.value = null; };

const handleSave = async (data: { name: string; permissions: string[] }) => {
    try {
        if (selectedRole.value) {
            await roleService.updateRole(selectedRole.value.id, data);
        } else {
            await roleService.createRole(data);
        }
        await loadRoles();
        closeModal();
    } catch (error) {
        console.error("Erreur sauvegarde rôle:", error);
    }
};

const confirmDelete = (role: Role) => { roleToDelete.value = role; showDeleteConfirm.value = true; };
const cancelDelete = () => { showDeleteConfirm.value = false; roleToDelete.value = null; };

const deleteRole = async () => {
    if (!roleToDelete.value) return;
    try {
        await roleService.deleteRole(roleToDelete.value.id);
        await loadRoles();
    } catch (error) {
        console.error("Erreur suppression rôle:", error);
    } finally {
        cancelDelete();
    }
};

onMounted(() => {
    loadRoles();
    loadPermissions();
});
</script>

<style scoped>
.roles-container {
    padding: 1.5rem 2rem;
    background: #f8fafc;
    min-height: 100vh;
    font-family: "Segoe UI", system-ui, sans-serif;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.page-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.2rem;
}

.page-description {
    color: #64748b;
    margin: 0;
    font-size: 0.8rem;
}

.btn-primary {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #fff;
    border: none;
    padding: 0.55rem 1.1rem;
    border-radius: 9px;
    font-weight: 600;
    font-size: 0.8rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    box-shadow: 0 2px 6px rgba(14, 165, 233, 0.3);
    transition: all 0.2s;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4); }
.btn-icon { width: 0.9rem; height: 0.9rem; }

.search-section { margin-bottom: 1rem; }
.input-icon { position: relative; }
.input-icon-svg {
    position: absolute; left: 0.6rem; top: 50%; transform: translateY(-50%);
    width: 0.8rem; height: 0.8rem; color: #94a3b8;
}
.finput {
    width: 100%;
    padding: 0.5rem 0.65rem 0.5rem 2rem;
    border: 1px solid #e2e8f0; border-radius: 7px;
    font-size: 0.8rem; color: #0f172a; background: #fff; transition: all 0.2s;
}
.finput:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,.1); }

.table-container {
    background: #fff; border-radius: 12px;
    overflow: hidden; border: 0.5px solid #e2e8f0;
}
.table-responsive { overflow-x: auto; }
.table { width: 100%; border-collapse: collapse; }
.table thead { background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
.table th {
    padding: 0.7rem 1rem; text-align: left;
    font-size: 0.68rem; font-weight: 600; color: #475569;
    text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;
}
.table th.th-center { text-align: center; }
.table td {
    padding: 0.75rem 1rem; border-bottom: 0.5px solid #f1f5f9;
    font-size: 0.8rem; color: #334155; vertical-align: middle;
}
.table-row { transition: background 0.15s; }
.table-row:hover td { background: #f8fafc; }
.table tbody tr:last-child td { border-bottom: none; }

.role-cell { display: flex; align-items: center; gap: 0.6rem; }
.role-icon {
    width: 1.9rem; height: 1.9rem; border-radius: 6px;
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    color: #0284c7;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.role-icon svg { width: 1rem; height: 1rem; }
.role-name { font-weight: 600; color: #0f172a; font-size: 0.82rem; }

.badges-container { display: flex; flex-wrap: wrap; gap: 0.35rem; }

.badge {
    padding: 0.2rem 0.6rem; border-radius: 5px;
    font-size: 0.7rem; font-weight: 500; white-space: nowrap;
}
.badge-perm { background: #ede9fe; color: #6d28d9; }
.badge-more { background: #f1f5f9; color: #475569; font-style: italic; }
.badge-secondary { background: #f1f5f9; color: #475569; }

.date-cell { display: flex; align-items: center; gap: 0.35rem; color: #475569; font-size: 0.78rem; }
.cell-icon { width: 0.75rem; height: 0.75rem; color: #94a3b8; flex-shrink: 0; }

.action-buttons { display: flex; align-items: center; justify-content: center; gap: 0.35rem; }
.action-btn {
    width: 1.75rem; height: 1.75rem; border: none; border-radius: 6px;
    cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.15s;
}
.action-icon { width: 0.8rem; height: 0.8rem; }
.action-btn.edit { background: #f0f9ff; color: #0284c7; }
.action-btn.edit:hover { background: #e0f2fe; }
.action-btn.delete { background: #fff5f5; color: #ef4444; }
.action-btn.delete:hover { background: #fee2e2; }

.empty-state { text-align: center; padding: 3rem; }
.empty-icon { width: 3.5rem; height: 3.5rem; color: #cbd5e1; margin: 0 auto 0.85rem; display: block; }
.empty-title { font-size: 1rem; font-weight: 600; color: #475569; margin: 0 0 0.35rem; }
.empty-description { color: #94a3b8; font-size: 0.82rem; margin: 0; }

.pagination-container {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 1rem;
}
.pagination { display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap; }
.page-btn {
    padding: 0.35rem 0.65rem; border: 1px solid #e2e8f0; background: #fff;
    border-radius: 6px; font-size: 0.75rem; cursor: pointer; color: #334155;
    transition: all 0.15s; min-width: 2rem; text-align: center;
}
.page-btn:hover:not(:disabled):not(.active) { border-color: #0ea5e9; color: #0ea5e9; }
.page-btn.active { background: #0ea5e9; color: #fff; border-color: #0ea5e9; }
.page-btn:disabled { opacity: 0.45; cursor: not-allowed; }
.page-btn.dots { cursor: default; border-color: transparent; }
.pagination-info { font-size: 0.75rem; color: #64748b; }
</style>
