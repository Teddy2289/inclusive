import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  const user  = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value)

  function hasPermission(permission: string): boolean {
    return user.value?.permissions.includes(permission) ?? false
  }

  function hasRole(role: string): boolean {
    return user.value?.roles.includes(role) ?? false
  }

  async function login(email: string, password: string): Promise<void> {
    const { data } = await api.post('/login', { email, password })
    token.value = data.token
    user.value  = data.user
    localStorage.setItem('token', data.token)
  }

  async function register(name: string, email: string, password: string, password_confirmation: string): Promise<void> {
    const { data } = await api.post('/register', { name, email, password, password_confirmation })
    token.value = data.token
    user.value  = data.user
    localStorage.setItem('token', data.token)
  }

  async function logout(): Promise<void> {
    await api.post('/logout')
    token.value = null
    user.value  = null
    localStorage.removeItem('token')
  }

  async function fetchMe(): Promise<void> {
    if (!token.value) return
    const { data } = await api.get('/me')
    user.value = data
  }

  return { user, token, isAuthenticated, hasPermission, hasRole, login, register, logout, fetchMe }
})
