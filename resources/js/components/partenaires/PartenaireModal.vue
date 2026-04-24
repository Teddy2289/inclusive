<template>
    <div class="overlay" @click.self="$emit('close')">
        <div class="modal">
            <h2 class="modal-title">{{ isEdit ? 'Modifier partenaire' : 'Nouveau partenaire' }}</h2>

            <form @submit.prevent="handleSubmit">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Raison sociale *</label>
                        <input v-model="form.raison_sociale" class="input" required />
                        <span v-if="errors.raison_sociale" class="field-error">{{ errors.raison_sociale[0] }}</span>
                    </div>
                    <div class="form-group">
                        <label>SIRET</label>
                        <input v-model="form.siret" class="input" />
                    </div>
                    <div class="form-group">
                        <label>Adresse</label>
                        <input v-model="form.adresse" class="input" />
                    </div>
                    <div class="form-group">
                        <label>CP</label>
                        <input v-model="form.cp" class="input" />
                    </div>
                    <div class="form-group">
                        <label>Ville</label>
                        <input v-model="form.ville" class="input" />
                    </div>
                    <div class="form-group">
                        <label>Secteur d'activité</label>
                        <input v-model="form.secteur_activite" class="input" />
                    </div>
                    <div class="form-group">
                        <label>Téléphone 1</label>
                        <input v-model="form.telephone_1" class="input" />
                    </div>
                    <div class="form-group">
                        <label>Téléphone 2</label>
                        <input v-model="form.telephone_2" class="input" />
                    </div>
                    <div class="form-group">
                        <label>Nb salariés</label>
                        <input v-model.number="form.nbrs_salaries" type="number" class="input" />
                    </div>
                    <div class="form-group">
                        <label>CA (€)</label>
                        <input v-model.number="form.ca" type="number" class="input" />
                    </div>
                    <div class="form-group" v-if="isEdit">
                        <label>Statut</label>
                        <select v-model="form.statut" class="input">
                            <option v-for="s in statutOptions" :key="s.value" :value="s.value">{{ s.label }}</option>
                        </select>
                    </div>
                </div>

                <div v-if="errorMsg" class="alert-error">{{ errorMsg }}</div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" @click="$emit('close')">Annuler</button>
                    <button type="submit" class="btn-primary" :disabled="loading">
                        {{ loading ? 'Enregistrement...' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import type { Partenaire, EnumOption, PartenaireForm } from '@/types'
import { partenaireService } from '@/services/partenaireService'

const props = defineProps<{
    partenaire: Partenaire | null
    statutOptions: EnumOption[]
}>()

const emit = defineEmits<{ close: []; saved: [] }>()
const isEdit = computed(() => !!props.partenaire)
const loading = ref(false)
const errorMsg = ref('')
const errors = ref<Record<string, string[]>>({})

const form = reactive<PartenaireForm>({
    raison_sociale: props.partenaire?.raison_sociale ?? '',
    adresse: props.partenaire?.adresse ?? '',
    cp: props.partenaire?.cp ?? '',
    ville: props.partenaire?.ville ?? '',
    secteur_activite: props.partenaire?.secteur_activite ?? '',
    telephone_1: props.partenaire?.telephone_1 ?? '',
    telephone_2: props.partenaire?.telephone_2 ?? '',
    nbrs_salaries: props.partenaire?.nbrs_salaries ?? null,
    ca: props.partenaire?.ca ?? null,
    siret: props.partenaire?.siret ?? '',
    statut: props.partenaire?.statut?.value ?? '',
})

async function handleSubmit(): Promise<void> {
    loading.value = true
    errorMsg.value = ''
    errors.value = {}
    try {
        if (isEdit.value && props.partenaire) {
            await partenaireService.update(props.partenaire.id, form)
        } else {
            await partenaireService.create(form)
        }
        emit('saved')
    } catch (e: any) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
        } else {
            errorMsg.value = e.response?.data?.message ?? 'Une erreur est survenue.'
        }
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
}

.modal {
    background: #fff;
    border-radius: 16px;
    padding: 2rem;
    width: 90%;
    max-width: 720px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 8px 32px rgba(0, 0, 0, .15);
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: .35rem;
}

.form-group label {
    font-size: .8rem;
    font-weight: 500;
    color: #475569;
}

.input {
    padding: .55rem .75rem;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: .875rem;
    outline: none;
}

.input:focus {
    border-color: #38bdf8;
}

.field-error {
    color: #ef4444;
    font-size: .75rem;
}

.alert-error {
    background: #fee2e2;
    color: #b91c1c;
    padding: .6rem;
    border-radius: 8px;
    margin-top: 1rem;
    font-size: .875rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
    margin-top: 1.5rem;
}

.btn-cancel {
    padding: .55rem 1.25rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
}

.btn-primary {
    background: #0ea5e9;
    color: #fff;
    border: none;
    padding: .55rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.btn-primary:disabled {
    opacity: .6;
    cursor: not-allowed;
}
</style>
