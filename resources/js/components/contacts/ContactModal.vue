<template>
    <div class="overlay" @click.self="$emit('close')">
        <div class="modal">

            <div class="modal-header">
                <div class="modal-header-left">
                    <div class="modal-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="modal-title">{{ isEdit ? 'Modifier le contact' : 'Nouveau contact' }}</h2>
                        <p class="modal-sub">{{ isEdit ? 'Mettez à jour les informations du contact' : 'Remplissez les informations du nouveau contact' }}</p>
                    </div>
                </div>
                <button class="modal-close" @click="$emit('close')">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="handleSubmit">

                <!-- Section : Identité -->
                <div class="form-section">
                    <div class="section-label">Identité du conseiller</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="field-label">Nom <span class="required">*</span></label>
                            <input v-model="form.conseiller_nom" class="input"
                                :class="{ 'input-error': errors.conseiller_nom }" placeholder="Dupont" />
                            <span v-if="errors.conseiller_nom" class="field-error">{{ errors.conseiller_nom[0] }}</span>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Prénom <span class="required">*</span></label>
                            <input v-model="form.conseiller_prenom" class="input"
                                :class="{ 'input-error': errors.conseiller_prenom }" placeholder="Jean" />
                            <span v-if="errors.conseiller_prenom" class="field-error">{{ errors.conseiller_prenom[0]
                            }}</span>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Fonction</label>
                            <input v-model="form.fonction" class="input" placeholder="Directeur commercial" />
                        </div>
                        <div class="form-group">
                            <label class="field-label">Poste</label>
                            <input v-model="form.poste" class="input" placeholder="Poste interne" />
                        </div>
                    </div>
                </div>

                <!-- Section : Coordonnées -->
                <div class="form-section">
                    <div class="section-label">Coordonnées</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="field-label">Téléphone</label>
                            <input v-model="form.tel" class="input" placeholder="+33 6 00 00 00 00" />
                        </div>
                        <div v-if="isEdit" class="form-group">
                            <label class="field-label">Statut</label>
                            <select v-model="form.statut" class="input">
                                <option v-for="s in statutOptions" :key="s.value" :value="s.value">{{ s.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section : Partenaire — affiché seulement si pas de partenaireId fixe -->
                <!-- Section Partenaires — remplace l'ancien select simple -->
                <div class="form-section">
                    <div class="section-label">Partenaires liés</div>
                    <div class="form-group">
                        <label class="field-label">
                            Partenaires <span class="required">*</span>
                        </label>

                        <div v-if="loadingPart" class="loading-inline">Chargement...</div>

                        <!-- Liste de cases à cocher -->
                        <div v-else class="partenaires-checklist">
                            <label v-for="p in partenaires" :key="p.id" class="check-item"
                                :class="{ selected: form.partenaire_ids.includes(p.id) }">
                                <input type="checkbox" :value="p.id" v-model="form.partenaire_ids"
                                    class="check-input" />
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                                    class="part-ico">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18" />
                                </svg>
                                {{ p.raison_sociale }}
                            </label>
                        </div>

                        <span v-if="errors.partenaire_ids" class="field-error">
                            {{ errors.partenaire_ids[0] }}
                        </span>
                    </div>
                </div>

                <!-- Section : Rendez-vous -->
                <div class="form-section">
                    <div class="section-label">Rendez-vous</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="field-label">Date de RDV</label>
                            <input v-model="form.date_rdv" type="date" class="input"
                                :class="{ 'input-error': errors.date_rdv }" :min="today" />
                            <span v-if="errors.date_rdv" class="field-error">{{ errors.date_rdv[0] }}</span>
                        </div>
                        <div class="form-group">
                            <label class="field-label">Heure de RDV</label>
                            <input v-model="form.heure_rdv" type="time" class="input" />
                        </div>
                        <div class="form-group">
                            <label class="field-label">Date premier contact</label>
                            <input v-model="form.date_premier_contact" type="date" class="input" />
                        </div>
                    </div>
                </div>

                <!-- Section : Commentaires -->
                <div class="form-section">
                    <div class="section-label">Commentaires</div>
                    <div class="form-group">
                        <textarea v-model="form.commentaires" class="input textarea" rows="3"
                            placeholder="Notes, observations..." />
                    </div>
                </div>

                <!-- Erreur globale -->
                <div v-if="errorMsg" class="alert-error">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" class="alert-ico">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    {{ errorMsg }}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" @click="$emit('close')">Annuler</button>
                    <button type="submit" class="btn-submit" :disabled="loading">
                        <svg v-if="loading" class="spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"
                                stroke-dasharray="31.4" stroke-dashoffset="10" />
                        </svg>
                        {{ loading ? 'Enregistrement...' : 'Enregistrer' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { ref, reactive, computed, onMounted } from 'vue'
import type { Contact, ContactForm, EnumOption } from '@/types'
import { contactService } from '@/services/contactService'
import { partenaireService } from '@/services/partenaireService'

const props = defineProps<{
    contact: Contact | null
    partenaireId: number | null
    statutOptions: EnumOption[]
}>()

const emit = defineEmits<{ close: []; saved: [] }>()
const isEdit = computed(() => !!props.contact)
const loading = ref(false)
const loadingPart = ref(false)
const errorMsg = ref('')
const errors = ref<Record<string, string[]>>({})
const today = new Date().toISOString().split('T')[0]
const partenaires = ref<{ id: number; raison_sociale: string }[]>([])

// Si partenaireId est fourni (ouverture depuis fiche partenaire),
// on n'a pas besoin de charger la liste
const needsPartenaireSelect = computed(() => !props.partenaireId && !props.contact?.partenaire_id)

const form = reactive<ContactForm>({
    conseiller_nom: props.contact?.conseiller_nom ?? '',
    conseiller_prenom: props.contact?.conseiller_prenom ?? '',
    fonction: props.contact?.fonction ?? '',
    tel: props.contact?.tel ?? '',
    poste: props.contact?.poste ?? '',
    date_rdv: props.contact?.date_rdv ?? '',
    heure_rdv: props.contact?.heure_rdv ?? '',
    date_premier_contact: props.contact?.date_premier_contact ?? '',
    commentaires: props.contact?.commentaires ?? '',
    statut: props.contact?.statut?.value ?? '',
     partenaire_ids: props.contact?.partenaires?.map(p => p.id)
    ?? (props.partenaireId ? [props.partenaireId] : []),
})

onMounted(async () => {
  loadingPart.value = true
  try {
    const result = await partenaireService.list({ per_page: 999 })
    partenaires.value = result.data.map(p => ({
      id: p.id,
      raison_sociale: p.raison_sociale,
    }))
  } finally {
    loadingPart.value = false
  }
})

async function handleSubmit(): Promise<void> {
    loading.value = true
    errorMsg.value = ''
    errors.value = {}
    try {
        if (isEdit.value && props.contact) {
            await contactService.update(props.contact.id, form)
        } else {
            await contactService.create(form)
        }

        emit('saved')
        router.reload()
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
    background: rgba(15, 23, 42, .45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
    padding: 1rem;
}

.modal {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 640px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
}

/* Header */
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.modal-header-left {
    display: flex;
    align-items: center;
    gap: .85rem;
}

.modal-icon {
    width: 2.5rem;
    height: 2.5rem;
    background: #e0f2fe;
    color: #0284c7;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.modal-icon svg {
    width: 1.2rem;
    height: 1.2rem;
}

.modal-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 .15rem;
}

.modal-sub {
    font-size: .75rem;
    color: #64748b;
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    padding: .25rem;
    border-radius: 6px;
    transition: background .15s, color .15s;
}

.modal-close:hover {
    background: #f1f5f9;
    color: #475569;
}

.modal-close svg {
    width: 1.1rem;
    height: 1.1rem;
    display: block;
}

/* Sections */
.form-section {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid #f8fafc;
}

.section-label {
    font-size: .68rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: .75rem;
}

/* Grid */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
}

/* Groups */
.form-group {
    display: flex;
    flex-direction: column;
    gap: .3rem;
}

.field-label {
    font-size: .78rem;
    font-weight: 600;
    color: #374151;
}

.required {
    color: #ef4444;
}

/* Inputs */
.input {
    padding: .52rem .75rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: .82rem;
    color: #0f172a;
    background: #fff;
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
}

.input:focus {
    outline: none;
    border-color: #0ea5e9;
    box-shadow: 0 0 0 3px rgba(14, 165, 233, .1);
}

.input-error {
    border-color: #fca5a5;
    background: #fff5f5;
}

.input-error:focus {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, .1);
}

.textarea {
    resize: vertical;
    min-height: 80px;
}

/* Field error */
.field-error {
    font-size: .72rem;
    color: #ef4444;
    margin-top: .1rem;
}

/* Alert globale */
.alert-error {
    margin: 0 1.5rem .25rem;
    background: #fef2f2;
    border: 1px solid #fca5a5;
    border-radius: 8px;
    padding: .65rem 1rem;
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    font-size: .8rem;
    color: #b91c1c;
}

.alert-ico {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
    margin-top: .05rem;
    color: #ef4444;
}

/* Footer */
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: .65rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #f1f5f9;
}

.btn-cancel {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
    padding: .52rem 1.1rem;
    border-radius: 8px;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
}

.btn-cancel:hover {
    background: #f1f5f9;
}

.btn-submit {
    background: linear-gradient(135deg, #0ea5e9, #0284c7);
    color: #fff;
    border: none;
    padding: .52rem 1.25rem;
    border-radius: 8px;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: .45rem;
    box-shadow: 0 2px 6px rgba(14, 165, 233, .3);
    transition: all .2s;
}

.btn-submit:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(14, 165, 233, .4);
}

.btn-submit:disabled {
    opacity: .55;
    cursor: not-allowed;
}

.spin {
    width: .9rem;
    height: .9rem;
    animation: rotate 1s linear infinite;
}

@keyframes rotate {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 520px) {
    .form-grid {
        grid-template-columns: 1fr;
    }

    .modal {
        max-height: 100vh;
        border-radius: 0;
    }
}
</style>
