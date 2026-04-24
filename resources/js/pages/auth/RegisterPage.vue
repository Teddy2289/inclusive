<template>
  <div class="auth-wrapper">
    <div class="auth-card">
      <h1 class="auth-title">Créer un compte</h1>

      <div v-if="errorMsg" class="alert-error">{{ errorMsg }}</div>

      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label>Nom complet</label>
          <input v-model="form.name" type="text" placeholder="Jean Dupont" required />
          <span v-if="errors.name" class="field-error">{{ errors.name[0] }}</span>
        </div>

        <div class="form-group">
          <label>Email</label>
          <input v-model="form.email" type="email" placeholder="vous@exemple.com" required />
          <span v-if="errors.email" class="field-error">{{ errors.email[0] }}</span>
        </div>

        <div class="form-group">
          <label>Mot de passe</label>
          <input v-model="form.password" type="password" placeholder="••••••••" required />
          <span v-if="errors.password" class="field-error">{{ errors.password[0] }}</span>
        </div>

        <div class="form-group">
          <label>Confirmer le mot de passe</label>
          <input v-model="form.password_confirmation" type="password" placeholder="••••••••" required />
        </div>

        <button type="submit" class="btn-primary" :disabled="loading">
          {{ loading ? 'Création...' : 'Créer le compte' }}
        </button>
      </form>

      <p class="auth-link">
        Déjà un compte ? <RouterLink to="/login">Se connecter</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter }     from 'vue-router'
import { useAuthStore }  from '@/stores/auth'

const auth     = useAuthStore()
const router   = useRouter()
const loading  = ref(false)
const errorMsg = ref('')
const errors   = ref<Record<string, string[]>>({})

const form = reactive({
  name: '', email: '', password: '', password_confirmation: '',
})

async function handleRegister(): Promise<void> {
  loading.value  = true
  errorMsg.value = ''
  errors.value   = {}
  try {
    await auth.register(form.name, form.email, form.password, form.password_confirmation)
    router.push({ name: 'dashboard' })
  } catch (e: any) {
    if (e.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
    } else {
      errorMsg.value = e.response?.data?.message ?? 'Erreur lors de la création.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-wrapper  { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f1f5f9; }
.auth-card     { background: #fff; padding: 2.5rem; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.08); width: 100%; max-width: 420px; }
.auth-title    { font-size: 1.6rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem; text-align: center; }
.form-group    { display: flex; flex-direction: column; gap: .35rem; margin-bottom: 1rem; }
.form-group label { font-size: .875rem; font-weight: 500; color: #475569; }
.form-group input { padding: .6rem .75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: .9rem; outline: none; }
.form-group input:focus { border-color: #38bdf8; }
.field-error   { color: #ef4444; font-size: .8rem; }
.btn-primary   { width: 100%; padding: .7rem; background: #0ea5e9; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: .5rem; }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.alert-error   { background: #fee2e2; color: #b91c1c; padding: .6rem; border-radius: 8px; margin-bottom: 1rem; font-size: .875rem; }
.auth-link     { text-align: center; margin-top: 1rem; font-size: .875rem; color: #64748b; }
.auth-link a   { color: #0ea5e9; font-weight: 600; text-decoration: none; }
</style>
