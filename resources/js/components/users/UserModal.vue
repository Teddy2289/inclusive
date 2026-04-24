<template>
    <div class="modal-overlay" @click.self="close">
        <div class="modal">
            <div class="modal-header">
                <h2>
                    {{
                        isEdit ? "Modifier l'utilisateur" : "Nouvel utilisateur"
                    }}
                </h2>
                <button @click="close" class="close-btn">&times;</button>
            </div>

            <form @submit.prevent="save">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nom complet *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="form-input"
                            placeholder="Jean Dupont"
                        />
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            class="form-input"
                            placeholder="jean@example.com"
                        />
                    </div>

                    <div class="form-group">
                        <label>{{
                            isEdit ? "Nouveau mot de passe" : "Mot de passe *"
                        }}</label>
                        <div class="password-input-wrapper">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                :required="!isEdit"
                                class="form-input"
                                placeholder="********"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="password-toggle"
                            >
                                <svg
                                    v-if="!showPassword"
                                    class="toggle-icon"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                    />
                                </svg>
                                <svg
                                    v-else
                                    class="toggle-icon"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group" v-if="form.password">
                        <label>Confirmer le mot de passe</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="form-input"
                            placeholder="********"
                        />
                    </div>

                    <div class="form-group">
                        <label>Rôles</label>
                        <div class="checkbox-group">
                            <label
                                v-for="role in roles"
                                :key="role.id"
                                class="checkbox-label"
                            >
                                <input
                                    type="checkbox"
                                    :value="role.name"
                                    v-model="form.roles"
                                    class="checkbox-input"
                                />
                                <div class="checkbox-content">
                                    <span class="checkbox-text">{{
                                        role.name
                                    }}</span>
                                    <span
                                        class="role-perms"
                                        v-if="
                                            role.permissions &&
                                            role.permissions.length
                                        "
                                    >
                                        {{
                                            role.permissions
                                                .map((p) => p.name)
                                                .join(", ")
                                        }}
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Permissions supplémentaires</label>
                        <div class="checkbox-grid">
                            <label
                                v-for="perm in permissions"
                                :key="perm.id"
                                class="checkbox-label"
                            >
                                <input
                                    type="checkbox"
                                    :value="perm.name"
                                    v-model="form.permissions"
                                    class="checkbox-input"
                                />
                                <span class="checkbox-text">{{
                                    formatPermission(perm.name)
                                }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="close" class="btn-secondary">
                        Annuler
                    </button>
                    <button
                        type="submit"
                        class="btn-primary"
                        :disabled="loading"
                    >
                        <svg
                            v-if="loading"
                            class="spinner"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="3"
                                stroke-dasharray="31.4"
                                stroke-dashoffset="10"
                            />
                        </svg>
                        <span v-else>{{
                            loading ? "Enregistrement..." : "Enregistrer"
                        }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from "vue";
import type { User, Role, Permission } from "@/types";

interface UserFormData {
    name: string;
    email: string;
    password?: string;
    password_confirmation?: string;
    roles: string[];
    permissions: string[];
}

const props = defineProps<{
    user: User | null;
    roles: Role[];
    permissions: Permission[];
}>();

const emit = defineEmits<{
    close: [];
    save: [data: UserFormData];
}>();

const loading = ref(false);
const showPassword = ref(false);

const form = reactive({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    roles: [] as string[],
    permissions: [] as string[],
});

const isEdit = computed(() => !!props.user);

const formatPermission = (permission: string) => {
    return permission
        .split(".")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" - ");
};

watch(
    () => props.user,
    (newUser) => {
        if (newUser) {
            form.name = newUser.name;
            form.email = newUser.email;
            form.roles = newUser.roles.map((r) => r.name);
            form.permissions = newUser.permissions.map((p) => p.name);
            form.password = "";
            form.password_confirmation = "";
        } else {
            form.name = "";
            form.email = "";
            form.roles = [];
            form.permissions = [];
            form.password = "";
            form.password_confirmation = "";
        }
    },
    { immediate: true },
);

const save = async () => {
    if (!isEdit.value && form.password !== form.password_confirmation) {
        alert("Les mots de passe ne correspondent pas");
        return;
    }

    loading.value = true;
    try {
        const dataToSend: UserFormData = {
            name: form.name,
            email: form.email,
            roles: form.roles,
            permissions: form.permissions,
        };

        if (form.password) {
            dataToSend.password = form.password;
            dataToSend.password_confirmation = form.password_confirmation;
        }

        emit("save", dataToSend);
        close();
    } finally {
        loading.value = false;
    }
};

const close = () => {
    emit("close");
};
</script>

<style scoped>
/* ── Modal Overlay ── */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    backdrop-filter: blur(2px);
    animation: fadeIn 0.2s ease-out;
}

/* ── Modal Container ── */
.modal {
    background: #fff;
    border-radius: 16px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow: hidden;
    animation: slideUp 0.3s ease-out;
    box-shadow:
        0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* ── Modal Header ── */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.modal-header h2 {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #94a3b8;
    transition: color 0.2s;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}

.close-btn:hover {
    color: #ef4444;
    background: #fef2f2;
}

/* ── Modal Body ── */
.modal-body {
    padding: 1.5rem;
    max-height: calc(90vh - 120px);
    overflow-y: auto;
}

/* ── Form Group ── */
.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.75rem;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-input {
    width: 100%;
    padding: 0.6rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #0f172a;
    background: #fff;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.form-input::placeholder {
    color: #cbd5e1;
}

/* ── Password Input ── */
.password-input-wrapper {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    transition: color 0.2s;
}

.password-toggle:hover {
    color: #0ea5e9;
}

.toggle-icon {
    width: 1.1rem;
    height: 1.1rem;
}

/* ── Checkbox Groups ── */
.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    max-height: 200px;
    overflow-y: auto;
    padding: 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fafbfc;
}

.checkbox-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 0.75rem;
    max-height: 200px;
    overflow-y: auto;
    padding: 0.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fafbfc;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    cursor: pointer;
    font-size: 0.85rem;
    padding: 0.4rem;
    border-radius: 6px;
    transition: background 0.15s;
}

.checkbox-label:hover {
    background: #f1f5f9;
}

.checkbox-input {
    width: 1rem;
    height: 1rem;
    margin-top: 0.15rem;
    cursor: pointer;
    accent-color: #0ea5e9;
}

.checkbox-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.checkbox-text {
    color: #334155;
    font-weight: 500;
    font-size: 0.8rem;
}

.role-perms {
    font-size: 0.65rem;
    color: #64748b;
    line-height: 1.3;
    word-break: break-word;
}

/* ── Modal Footer ── */
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
}

.btn-secondary,
.btn-primary {
    padding: 0.55rem 1.2rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #475569;
}

.btn-secondary:hover:not(:disabled) {
    background: #f8fafc;
    border-color: #cbd5e0;
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #fff;
    box-shadow: 0 2px 6px rgba(14, 165, 233, 0.3);
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.4);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* ── Spinner ── */
.spinner {
    width: 1rem;
    height: 1rem;
    animation: spin 1s linear infinite;
}

/* ── Scrollbar Styling ── */
.checkbox-group::-webkit-scrollbar,
.checkbox-grid::-webkit-scrollbar,
.modal-body::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.checkbox-group::-webkit-scrollbar-track,
.checkbox-grid::-webkit-scrollbar-track,
.modal-body::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.checkbox-group::-webkit-scrollbar-thumb,
.checkbox-grid::-webkit-scrollbar-thumb,
.modal-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.checkbox-group::-webkit-scrollbar-thumb:hover,
.checkbox-grid::-webkit-scrollbar-thumb:hover,
.modal-body::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* ── Animations ── */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* ── Responsive ── */
@media (max-width: 640px) {
    .modal {
        width: 95%;
        max-height: 95vh;
    }

    .modal-header {
        padding: 1rem;
    }

    .modal-body {
        padding: 1rem;
    }

    .modal-footer {
        padding: 0.75rem 1rem;
        flex-direction: column-reverse;
    }

    .btn-secondary,
    .btn-primary {
        width: 100%;
        justify-content: center;
    }

    .checkbox-grid {
        grid-template-columns: 1fr;
    }
}
</style>
