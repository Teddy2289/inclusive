<template>
    <div class="modal-overlay" @click.self="$emit('close')">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">
                    {{ role ? "Modifier le rôle" : "Nouveau rôle" }}
                </h2>
                <button class="modal-close" @click="$emit('close')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <!-- Nom du rôle -->
                <div class="form-group">
                    <label class="form-label">Nom du rôle <span class="required">*</span></label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="form-input"
                        :class="{ error: errors.name }"
                        placeholder="ex: manager, commercial..."
                    />
                    <span v-if="errors.name" class="form-error">{{ errors.name }}</span>
                </div>

                <!-- Permissions -->
                <div class="form-group">
                    <label class="form-label">Permissions associées</label>
                    <div class="permissions-search">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input
                            v-model="permSearch"
                            type="text"
                            placeholder="Filtrer les permissions..."
                            class="perm-search-input"
                        />
                    </div>

                    <div class="permissions-box">
                        <div v-if="filteredPermissions.length === 0" class="perm-empty">
                            Aucune permission trouvée
                        </div>

                        <!-- Sélectionner tout -->
                        <label v-if="filteredPermissions.length > 0" class="perm-item perm-select-all">
                            <input
                                type="checkbox"
                                :checked="allSelected"
                                :indeterminate="someSelected && !allSelected"
                                @change="toggleAll"
                                class="perm-checkbox"
                            />
                            <span class="perm-name perm-all-label">Tout sélectionner</span>
                        </label>

                        <div class="perm-divider" v-if="filteredPermissions.length > 0"></div>

                        <label
                            v-for="perm in filteredPermissions"
                            :key="perm.id"
                            class="perm-item"
                        >
                            <input
                                type="checkbox"
                                :value="perm.name"
                                v-model="form.permissions"
                                class="perm-checkbox"
                            />
                            <span class="perm-name">{{ formatPermission(perm.name) }}</span>
                            <span class="perm-raw">{{ perm.name }}</span>
                        </label>
                    </div>
                    <div class="perm-count">
                        {{ form.permissions.length }} permission(s) sélectionnée(s)
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-cancel" @click="$emit('close')">Annuler</button>
                <button class="btn-save" @click="submit" :disabled="saving">
                    <svg v-if="saving" class="spinner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ saving ? "Enregistrement..." : (role ? "Mettre à jour" : "Créer") }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from "vue";
import type { Role, Permission } from "@/types";

const props = defineProps<{
    role: Role | null;
    permissions: Permission[];
}>();

const emit = defineEmits<{
    close: [];
    save: [data: { name: string; permissions: string[] }];
}>();

const saving = ref(false);
const permSearch = ref("");

const form = reactive({
    name: "",
    permissions: [] as string[],
});

const errors = reactive({
    name: "",
});

const filteredPermissions = computed(() =>
    props.permissions.filter((p) =>
        p.name.toLowerCase().includes(permSearch.value.toLowerCase())
    )
);

const allSelected = computed(
    () =>
        filteredPermissions.value.length > 0 &&
        filteredPermissions.value.every((p) => form.permissions.includes(p.name))
);

const someSelected = computed(() =>
    filteredPermissions.value.some((p) => form.permissions.includes(p.name))
);

const toggleAll = () => {
    if (allSelected.value) {
        // Désélectionner ceux qui sont filtrés
        form.permissions = form.permissions.filter(
            (n) => !filteredPermissions.value.map((p) => p.name).includes(n)
        );
    } else {
        // Ajouter ceux qui sont filtrés sans doublon
        const names = filteredPermissions.value.map((p) => p.name);
        form.permissions = [...new Set([...form.permissions, ...names])];
    }
};

const formatPermission = (name: string) =>
    name
        .split(".")
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
        .join(" › ");

const validate = () => {
    errors.name = "";
    if (!form.name.trim()) {
        errors.name = "Le nom du rôle est obligatoire";
        return false;
    }
    return true;
};

const submit = async () => {
    if (!validate()) return;
    saving.value = true;
    try {
        emit("save", { name: form.name.trim(), permissions: form.permissions });
    } finally {
        saving.value = false;
    }
};

// Pré-remplir si édition
watch(
    () => props.role,
    (role) => {
        if (role) {
            form.name = role.name;
            form.permissions = role.permissions?.map((p) => p.name) ?? [];
        } else {
            form.name = "";
            form.permissions = [];
        }
    },
    { immediate: true }
);
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    padding: 1rem;
}

.modal {
    background: #fff;
    border-radius: 14px;
    width: 100%;
    max-width: 540px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.modal-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.modal-close {
    width: 1.75rem;
    height: 1.75rem;
    border: none;
    background: #f1f5f9;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    transition: all 0.15s;
}

.modal-close:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.modal-close svg {
    width: 0.9rem;
    height: 0.9rem;
}

.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.form-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.required {
    color: #ef4444;
}

.form-input {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.82rem;
    color: #0f172a;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.form-input.error {
    border-color: #ef4444;
}

.form-error {
    font-size: 0.72rem;
    color: #ef4444;
}

/* Permissions */
.permissions-search {
    position: relative;
    margin-bottom: 0.5rem;
}

.search-icon {
    position: absolute;
    left: 0.6rem;
    top: 50%;
    transform: translateY(-50%);
    width: 0.8rem;
    height: 0.8rem;
    color: #94a3b8;
}

.perm-search-input {
    width: 100%;
    padding: 0.45rem 0.65rem 0.45rem 2rem;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
    font-size: 0.78rem;
    color: #0f172a;
    background: #f8fafc;
    transition: all 0.2s;
}

.perm-search-input:focus {
    outline: none;
    border-color: #0ea5e9;
    background: #fff;
}

.permissions-box {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    max-height: 220px;
    overflow-y: auto;
    background: #fafafa;
}

.perm-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    transition: background 0.1s;
}

.perm-item:hover {
    background: #f0f9ff;
}

.perm-select-all {
    background: #f8fafc;
}

.perm-divider {
    height: 1px;
    background: #e2e8f0;
}

.perm-checkbox {
    width: 0.9rem;
    height: 0.9rem;
    accent-color: #0ea5e9;
    flex-shrink: 0;
    cursor: pointer;
}

.perm-name {
    font-size: 0.78rem;
    color: #334155;
    flex: 1;
}

.perm-all-label {
    font-weight: 600;
    color: #0f172a;
    font-size: 0.78rem;
}

.perm-raw {
    font-size: 0.68rem;
    color: #94a3b8;
    font-family: monospace;
    background: #f1f5f9;
    padding: 0.1rem 0.4rem;
    border-radius: 4px;
}

.perm-empty {
    padding: 1.5rem;
    text-align: center;
    color: #94a3b8;
    font-size: 0.8rem;
}

.perm-count {
    font-size: 0.72rem;
    color: #64748b;
    margin-top: 0.35rem;
}

/* Footer */
.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.btn-cancel {
    padding: 0.5rem 1.1rem;
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s;
}

.btn-cancel:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

.btn-save {
    padding: 0.5rem 1.25rem;
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(14, 165, 233, 0.3);
}

.btn-save:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
}

.btn-save:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.spinner-icon {
    width: 0.85rem;
    height: 0.85rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
