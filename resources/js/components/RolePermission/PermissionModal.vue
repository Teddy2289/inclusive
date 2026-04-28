<template>
    <div class="modal-overlay" @click.self="$emit('close')">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">
                    {{ permission ? "Modifier la permission" : "Nouvelle permission" }}
                </h2>
                <button class="modal-close" @click="$emit('close')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">
                        Nom de la permission <span class="required">*</span>
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="form-input"
                        :class="{ error: errors.name }"
                        placeholder="ex: partenaires.create, contacts.edit..."
                    />
                    <span v-if="errors.name" class="form-error">{{ errors.name }}</span>

                </div>

                <!-- Aperçu du formatage -->
                <div class="preview" v-if="form.name">
                    <span class="preview-label">Aperçu affiché :</span>
                    <span class="preview-value">{{ formatPermission(form.name) }}</span>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-cancel" @click="$emit('close')">Annuler</button>
                <button class="btn-save" @click="submit" :disabled="saving">
                    <svg v-if="saving" class="spinner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ saving ? "Enregistrement..." : (permission ? "Mettre à jour" : "Créer") }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from "vue";
import type { Permission } from "@/types";

const props = defineProps<{
    permission: Permission | null;
}>();

const emit = defineEmits<{
    close: [];
    save: [data: { name: string }];
}>();

const saving = ref(false);

const form = reactive({ name: "" });
const errors = reactive({ name: "" });

const formatPermission = (name: string) =>
    name
        .split(".")
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
        .join(" › ");

const validate = () => {
    errors.name = "";
    if (!form.name.trim()) {
        errors.name = "Le nom de la permission est obligatoire";
        return false;
    }
    return true;
};

const submit = async () => {
    if (!validate()) return;
    saving.value = true;
    try {
        emit("save", { name: form.name.trim() });
    } finally {
        saving.value = false;
    }
};

watch(
    () => props.permission,
    (p) => {
        form.name = p?.name ?? "";
        errors.name = "";
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
    max-width: 460px;
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

.modal-close:hover { background: #e2e8f0; color: #0f172a; }
.modal-close svg { width: 0.9rem; height: 0.9rem; }

.modal-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
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

.required { color: #ef4444; }

.form-input {
    padding: 0.5rem 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.82rem;
    color: #0f172a;
    transition: all 0.2s;
    font-family: monospace;
}

.form-input:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.form-input.error { border-color: #ef4444; }
.form-error { font-size: 0.72rem; color: #ef4444; }

.form-hint {
    font-size: 0.72rem;
    color: #94a3b8;
    margin: 0;
}

.form-hint code {
    background: #f1f5f9;
    padding: 0.1rem 0.3rem;
    border-radius: 3px;
    font-size: 0.7rem;
    color: #0284c7;
}

.preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 0.85rem;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 8px;
}

.preview-label {
    font-size: 0.72rem;
    color: #0369a1;
    font-weight: 500;
}

.preview-value {
    font-size: 0.78rem;
    color: #0284c7;
    font-weight: 600;
}

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

.btn-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }

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

.btn-save:disabled { opacity: 0.7; cursor: not-allowed; }

.spinner-icon {
    width: 0.85rem;
    height: 0.85rem;
    animation: spin 1s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }
</style>
