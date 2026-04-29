<template>
  <div class="auth-wrapper">
    <div class="auth-container">
      <!-- Section gauche - Branding -->
      <div class="brand-section">
        <div class="brand-content">
          <div class="logo">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect width="48" height="48" rx="12" fill="url(#logoGradient)"/>
              <path d="M24 14L27.5 21H20.5L24 14Z" fill="white" fill-opacity="0.9"/>
              <path d="M24 34L20.5 27H27.5L24 34Z" fill="white" fill-opacity="0.6"/>
              <path d="M15 21L22 24.5L15 28V21Z" fill="white" fill-opacity="0.8"/>
              <path d="M33 21L26 24.5L33 28V21Z" fill="white" fill-opacity="0.8"/>
              <defs>
                <linearGradient id="logoGradient" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#0a1628"/>
                  <stop offset="1" stop-color="#0a1628"/>
                </linearGradient>
              </defs>
            </svg>
            <span class="logo-text">Inclusive Support</span>
          </div>
          <h2 class="brand-title">Bienvenue</h2>
          <p class="brand-description">
            Connectez-vous pour accéder à votre espace de travail et gérer vos activités efficacement.
          </p>
          <div class="features-list">
            <div class="feature">
              <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Gestion simplifiée</span>
            </div>
            <div class="feature">
              <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Sécurité renforcée</span>
            </div>
            <div class="feature">
              <svg class="feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Support 24/7</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Section droite - Formulaire -->
      <div class="form-section">
        <div class="form-container">
          <div class="form-header">
            <h1 class="form-title">Connexion</h1>
            <p class="form-subtitle">Entrez vos identifiants pour continuer</p>
          </div>

          <div v-if="errorMsg" class="alert-error">
            <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ errorMsg }}
          </div>

          <form @submit.prevent="handleLogin">
            <div class="form-group">
              <label class="form-label">
                <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email
              </label>
              <input
                v-model="form.email"
                type="email"
                placeholder="vous@exemple.com"
                required
                class="form-input"
                :class="{ 'input-error': errorMsg }"
              />
            </div>

            <div class="form-group">
              <label class="form-label">
                <svg class="label-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Mot de passe
              </label>
              <div class="password-input-wrapper">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="••••••••"
                  required
                  class="form-input"
                  :class="{ 'input-error': errorMsg }"
                />
                <button
                  type="button"
                  class="password-toggle"
                  @click="showPassword = !showPassword"
                >
                  <svg v-if="!showPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg v-else fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="form-options">
              <label class="checkbox-label">
                <input type="checkbox" v-model="rememberMe" class="checkbox-input" />
                <span class="checkbox-text">Se souvenir de moi</span>
              </label>
              <a href="#" class="forgot-link">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn-primary" :disabled="loading">
              <span v-if="loading" class="loading-spinner"></span>
              <span>{{ loading ? 'Connexion en cours...' : 'Se connecter' }}</span>
            </button>
          </form>

          <!-- <div class="divider">
            <span class="divider-text">ou</span>
          </div> -->

          <!-- <p class="auth-link">
            Pas encore de compte ?
            <RouterLink to="/register">Créer un compte</RouterLink>
          </p> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const loading = ref(false)
const errorMsg = ref('')
const showPassword = ref(false)
const rememberMe = ref(false)

const form = reactive({ email: '', password: '' })

async function handleLogin(): Promise<void> {
  loading.value = true
  errorMsg.value = ''
  try {
    await auth.login(form.email, form.password)
    if (rememberMe.value) {
      localStorage.setItem('rememberEmail', form.email)
    }
    router.push({ name: 'dashboard' })
  } catch (e: any) {
    errorMsg.value = e.response?.data?.message ?? 'Erreur de connexion. Vérifiez vos identifiants.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.auth-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #1c1a1f;
  padding: 1rem;
}

.auth-container {
  display: flex;
  max-width: 1200px;
  width: 100%;
  background: white;
  border-radius: 32px;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Section Branding */
.brand-section {
  flex: 1;
  background:#ff6b35;
  padding: 3rem;
  display: flex;
  align-items: center;
  position: relative;
  overflow: hidden;
}

.brand-section::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
  animation: rotate 20s linear infinite;
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.brand-content {
  position: relative;
  z-index: 1;
  color: white;
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 3rem;
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: -0.5px;
}

.brand-title {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.brand-description {
  font-size: 1rem;
  line-height: 1.6;
  opacity: 0.9;
  margin-bottom: 2rem;
}

.features-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.feature {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.9rem;
}

.feature-icon {
  width: 20px;
  height: 20px;
  stroke-width: 2;
}

/* Section Formulaire */
.form-section {
  flex: 1;
  padding: 3rem;
  background: white;
}

.form-container {
  max-width: 400px;
  margin: 0 auto;
}

.form-header {
  margin-bottom: 2rem;
  text-align: center;
}

.form-title {
  font-size: 1.875rem;
  font-weight: 800;
  color: #1f2937;
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
}

.form-subtitle {
  color: #6b7280;
  font-size: 0.875rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.label-icon {
  width: 16px;
  height: 16px;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.875rem;
  transition: all 0.2s;
  outline: none;
  background: #f9fafb;
}

.form-input:focus {
  border-color: #0a1628;
  background: white;
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.input-error {
  border-color: #ef4444;
}

.input-error:focus {
  border-color: #ef4444;
  box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.password-input-wrapper {
  position: relative;
}

.password-toggle {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.password-toggle svg {
  width: 20px;
  height: 20px;
  color: #9ca3af;
  transition: color 0.2s;
}

.password-toggle:hover svg {
  color: #0a1628;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-input {
  width: 16px;
  height: 16px;
  cursor: pointer;
  accent-color: #0a1628;
}

.checkbox-text {
  font-size: 0.875rem;
  color: #4b5563;
}

.forgot-link {
  font-size: 0.875rem;
  color: #0a1628;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}

.forgot-link:hover {
  color: #4f46e5;
  text-decoration: underline;
}

.btn-primary {
  width: 100%;
  padding: 0.875rem;
  background: #0a1628;
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  position: relative;
  overflow: hidden;
}

.btn-primary::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.5s;
}

.btn-primary:hover::before {
  left: 100%;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid white;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.alert-error {
  background: #fef2f2;
  border-left: 4px solid #ef4444;
  padding: 0.75rem 1rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #b91c1c;
}

.alert-icon {
  width: 18px;
  height: 18px;
  stroke-width: 2;
}

.divider {
  margin: 1.5rem 0;
  position: relative;
  text-align: center;
}

.divider::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  width: 100%;
  height: 1px;
  background: #e5e7eb;
}

.divider-text {
  position: relative;
  background: white;
  padding: 0 1rem;
  font-size: 0.75rem;
  color: #9ca3af;
  text-transform: uppercase;
}

.btn-google {
  width: 100%;
  padding: 0.75rem;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  transition: all 0.2s;
  margin-bottom: 1.5rem;
}

.btn-google:hover {
  background: #f9fafb;
  border-color: #d1d5db;
  transform: translateY(-1px);
}

.auth-link {
  text-align: center;
  font-size: 0.875rem;
  color: #6b7280;
}

.auth-link a {
  color: #0a1628;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s;
}

.auth-link a:hover {
  color: #4f46e5;
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
  .auth-container {
    flex-direction: column;
    max-width: 450px;
  }

  .brand-section {
    padding: 2rem;
  }

  .form-section {
    padding: 2rem;
  }

  .brand-title {
    font-size: 1.8rem;
  }
}
</style>
