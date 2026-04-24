<template>
    <div class="users-container">
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des utilisateurs</h1>
                <p class="page-description">
                    Gérez les utilisateurs, leurs rôles et permissions
                </p>
            </div>
            <button @click="openCreateModal" class="btn-primary">
                <svg
                    class="btn-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                Nouvel utilisateur
            </button>
        </div>

        <!-- Search -->
        <div class="search-section">
            <div class="input-icon">
                <svg
                    class="input-icon-svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Rechercher un utilisateur..."
                    @input="debouncedSearch"
                    class="finput"
                />
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <div class="table-responsive">
                <table class="table">
                    <colgroup>
                        <col style="width: 18%" />
                        <col style="width: 22%" />
                        <col style="width: 20%" />
                        <col style="width: 20%" />
                        <col style="width: 12%" />
                        <col style="width: 8%" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Rôles</th>
                            <th>Permissions spécifiques</th>
                            <th>Date de création</th>
                            <th class="th-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in users"
                            :key="user.id"
                            class="table-row"
                        >
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        {{ getInitials(user.name) }}
                                    </div>
                                    <span class="user-name">{{
                                        user.name
                                    }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="user-email">{{ user.email }}</span>
                            </td>
                            <td>
                                <div class="badges-container">
                                    <span
                                        v-for="role in user.roles"
                                        :key="role.id"
                                        class="badge badge-primary"
                                    >
                                        {{ role.name }}
                                    </span>
                                    <span
                                        v-if="!user.roles.length"
                                        class="badge badge-secondary"
                                        >Aucun rôle</span
                                    >
                                </div>
                            </td>
                            <td>
                                <div class="badges-container">
                                    <span
                                        v-for="perm in user.permissions"
                                        :key="perm.id"
                                        class="badge badge-info"
                                    >
                                        {{ formatPermission(perm.name) }}
                                    </span>
                                    <span
                                        v-if="!user.permissions.length"
                                        class="badge badge-secondary"
                                        >Aucune</span
                                    >
                                </div>
                            </td>
                            <td>
                                <div class="date-cell">
                                    <svg
                                        class="cell-icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    {{ formatDate(user.created_at as string) }}
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button
                                        @click="openEditModal(user)"
                                        class="action-btn edit"
                                        title="Modifier"
                                    >
                                        <svg
                                            class="action-icon"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="confirmDelete(user)"
                                        class="action-btn delete"
                                        title="Supprimer"
                                    >
                                        <svg
                                            class="action-icon"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!users.length && !loading">
                            <td colspan="6">
                                <div class="empty-state">
                                    <svg
                                        class="empty-icon"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                        />
                                    </svg>
                                    <p class="empty-title">Aucun utilisateur</p>
                                    <p class="empty-description">
                                        Cliquez sur "Nouvel utilisateur" pour en
                                        créer un
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container" v-if="meta && meta.last_page > 1">
                <div class="pagination">
                    <button
                        @click="changePage(1)"
                        :disabled="meta.current_page === 1"
                        class="page-btn"
                    >
                        «
                    </button>
                    <button
                        @click="changePage(meta.current_page - 1)"
                        :disabled="meta.current_page === 1"
                        class="page-btn"
                    >
                        ‹
                    </button>

                    <button
                        v-for="page in displayedPages"
                        :key="page"
                        @click="changePage(page)"
                        class="page-btn"
                        :class="{ active: page === meta.current_page }"
                    >
                        {{ page }}
                    </button>

                    <button
                        @click="changePage(meta.current_page + 1)"
                        :disabled="meta.current_page === meta.last_page"
                        class="page-btn"
                    >
                        ›
                    </button>
                    <button
                        @click="changePage(meta.last_page)"
                        :disabled="meta.current_page === meta.last_page"
                        class="page-btn"
                    >
                        »
                    </button>
                </div>
                <div class="pagination-info">
                    Page {{ meta.current_page }} sur {{ meta.last_page }} •
                    {{ meta.total }} utilisateur(s)
                </div>
            </div>
        </div>

        <!-- User Modal -->
        <UserModal
            v-if="showModal"
            :user="selectedUser"
            :roles="allRoles"
            :permissions="allPermissions"
            @close="closeModal"
            @save="handleSave"
        />

        <!-- Delete Confirmation Modal -->
        <ConfirmModal
            v-if="showDeleteConfirm"
            title="Supprimer l'utilisateur"
            :message="`Êtes-vous sûr de vouloir supprimer ${userToDelete?.name} ?`"
            :details="`Email: ${userToDelete?.email}`"
            confirm-text="Supprimer"
            cancel-text="Annuler"
            type="danger"
            @confirm="deleteUser"
            @cancel="cancelDelete"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { userService } from "@/services/userService";
import UserModal from "@/components/users/UserModal.vue";
import ConfirmModal from "@/components/ConfirmModal.vue";
import type { User, Role, Permission, Paginated } from "@/types";
import { an } from "vue-router/dist/router-CWoNjPRp.mjs";

const users = ref<User[]>([]);
const allRoles = ref<Role[]>([]);
const allPermissions = ref<Permission[]>([]);
const search = ref("");
const loading = ref(false);
const meta = ref<Paginated<User>["meta"] | null>(null);
const showModal = ref(false);
const showDeleteConfirm = ref(false);
const selectedUser = ref<User | null>(null);
const userToDelete = ref<User | null>(null);
let debounceTimeout: any;

const displayedPages = computed(() => {
    if (!meta.value) return [];
    const current = meta.value.current_page;
    const last = meta.value.last_page;
    const delta = 2;
    const range = [];
    const rangeWithDots: any = [];
    let l;

    for (let i = 1; i <= last; i++) {
        if (
            i === 1 ||
            i === last ||
            (i >= current - delta && i <= current + delta)
        ) {
            range.push(i);
        }
    }

    range.forEach((i) => {
        let l: any;
        if (l) {
            if (i - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (i - l !== 1) {
                rangeWithDots.push("...");
            }
        }
        rangeWithDots.push(i);
        l = i;
    });

    return rangeWithDots;
});

const getInitials = (name: string) => {
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString("fr-FR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
    });
};

const formatPermission = (permission: string) => {
    return permission
        .split(".")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" - ");
};

const loadUsers = async () => {
    loading.value = true;
    try {
        const response = await userService.getUsers({
            page: meta.value?.current_page || 1,
            search: search.value,
        });
        users.value = response.data;
        meta.value = response.meta;
    } catch (error) {
        console.error("Erreur lors du chargement des utilisateurs:", error);
    } finally {
        loading.value = false;
    }
};

const loadRolesAndPermissions = async () => {
    try {
        const [roles, permissions] = await Promise.all([
            userService.getRoles(),
            userService.getPermissions(),
        ]);
        allRoles.value = roles;
        allPermissions.value = permissions;
    } catch (error) {
        console.error("Erreur chargement rôles/permissions:", error);
    }
};

const debouncedSearch = () => {
    if (debounceTimeout) clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        if (meta.value) meta.value.current_page = 1;
        loadUsers();
    }, 300);
};

const changePage = (page: number) => {
    if (meta.value && page >= 1 && page <= meta.value.last_page) {
        loadUsers();
    }
};

const openCreateModal = () => {
    selectedUser.value = null;
    showModal.value = true;
};

const openEditModal = (user: User) => {
    selectedUser.value = user;
    showModal.value = true;
};

const handleSave = async (userData: any) => {
    try {
        if (selectedUser.value) {
            await userService.updateUser(selectedUser.value.id, userData);
        } else {
            await userService.createUser(userData);
        }
        await loadUsers();
        closeModal();
    } catch (error) {
        console.error("Erreur lors de la sauvegarde:", error);
    }
};

const confirmDelete = (user: User) => {
    userToDelete.value = user;
    showDeleteConfirm.value = true;
};

const deleteUser = async () => {
    if (userToDelete.value) {
        try {
            await userService.deleteUser(userToDelete.value.id);
            await loadUsers();
        } catch (error) {
            console.error("Erreur lors de la suppression:", error);
        } finally {
            cancelDelete();
        }
    }
};

const cancelDelete = () => {
    showDeleteConfirm.value = false;
    userToDelete.value = null;
};

const closeModal = () => {
    showModal.value = false;
    selectedUser.value = null;
};

onMounted(() => {
    loadUsers();
    loadRolesAndPermissions();
});
</script>

<style scoped>
/* ── Container ── */
.users-container {
    padding: 1.5rem 2rem;
    background: #f8fafc;
    min-height: 100vh;
    font-family: "Segoe UI", system-ui, sans-serif;
}

/* ── Header ── */
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

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
}

.btn-icon {
    width: 0.9rem;
    height: 0.9rem;
}

/* ── Search ── */
.search-section {
    margin-bottom: 1rem;
}

.input-icon {
    position: relative;
}

.input-icon-svg {
    position: absolute;
    left: 0.6rem;
    top: 50%;
    transform: translateY(-50%);
    width: 0.8rem;
    height: 0.8rem;
    color: #94a3b8;
}

.finput {
    width: 100%;
    padding: 0.5rem 0.65rem 0.5rem 2rem;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    font-size: 0.8rem;
    color: #0f172a;
    background: #fff;
    transition: all 0.2s;
}

.finput:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

/* ── Table container ── */
.table-container {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 0.5px solid #e2e8f0;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.table th {
    padding: 0.7rem 1rem;
    text-align: left;
    font-size: 0.68rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}

.table th.th-center {
    text-align: center;
}

.table td {
    padding: 0.75rem 1rem;
    border-bottom: 0.5px solid #f1f5f9;
    font-size: 0.8rem;
    color: #334155;
    vertical-align: middle;
}

.table-row {
    transition: background 0.15s;
}

.table-row:hover td {
    background: #f8fafc;
}

.table tbody tr:last-child td {
    border-bottom: none;
}

/* ── User cell ── */
.user-cell {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.user-avatar {
    width: 1.9rem;
    height: 1.9rem;
    border-radius: 6px;
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: 700;
    flex-shrink: 0;
}

.user-name {
    font-weight: 600;
    color: #0f172a;
    font-size: 0.82rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-email {
    font-size: 0.78rem;
    color: #475569;
}

/* ── Badges ── */
.badges-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
}

.badge {
    padding: 0.2rem 0.6rem;
    border-radius: 5px;
    font-size: 0.7rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    max-width: 100%;
}

.badge-primary {
    background: #dbeafe;
    color: #1e40af;
}

.badge-info {
    background: #e0f2fe;
    color: #0369a1;
}

.badge-secondary {
    background: #f1f5f9;
    color: #475569;
}

/* ── Date cell ── */
.date-cell {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    color: #475569;
    font-size: 0.78rem;
}

.cell-icon {
    width: 0.75rem;
    height: 0.75rem;
    color: #94a3b8;
    flex-shrink: 0;
}

/* ── Action buttons ── */
.action-buttons {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
}

.action-btn {
    width: 1.75rem;
    height: 1.75rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s;
}

.action-icon {
    width: 0.8rem;
    height: 0.8rem;
}

.action-btn.edit {
    background: #f0f9ff;
    color: #0284c7;
}

.action-btn.edit:hover {
    background: #e0f2fe;
}

.action-btn.delete {
    background: #fff5f5;
    color: #ef4444;
}

.action-btn.delete:hover {
    background: #fee2e2;
}

/* ── Empty state ── */
.empty-state {
    text-align: center;
    padding: 3rem;
}

.empty-icon {
    width: 3.5rem;
    height: 3.5rem;
    color: #cbd5e1;
    margin: 0 auto 0.85rem;
    display: block;
}

.empty-title {
    font-size: 1rem;
    font-weight: 600;
    color: #475569;
    margin: 0 0 0.35rem;
}

.empty-description {
    color: #94a3b8;
    font-size: 0.82rem;
    margin: 0;
}

/* ── Pagination ── */
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination {
    display: flex;
    gap: 0.35rem;
    align-items: center;
    flex-wrap: wrap;
}

.page-btn {
    padding: 0.35rem 0.65rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: 6px;
    font-size: 0.75rem;
    cursor: pointer;
    color: #334155;
    transition: all 0.15s;
    min-width: 2rem;
    text-align: center;
}

.page-btn:hover:not(:disabled):not(.active) {
    border-color: #0ea5e9;
    color: #0ea5e9;
}

.page-btn.active {
    background: #0ea5e9;
    color: #fff;
    border-color: #0ea5e9;
}

.page-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.pagination-info {
    font-size: 0.75rem;
    color: #64748b;
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .users-container {
        padding: 1rem;
    }

    .page-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .pagination-container {
        flex-direction: column;
        align-items: center;
    }

    .table th,
    .table td {
        padding: 0.5rem 0.75rem;
    }

    .badges-container {
        flex-direction: column;
        gap: 0.25rem;
    }

    .badge {
        white-space: normal;
    }
}

/* Loading state (optionnel) */
.loading-state {
    text-align: center;
    padding: 3rem;
    color: #64748b;
    font-size: 0.85rem;
}

.spinner {
    width: 2.5rem;
    height: 2.5rem;
    border: 3px solid #e2e8f0;
    border-top-color: #0ea5e9;
    border-radius: 50%;
    margin: 0 auto 1rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
