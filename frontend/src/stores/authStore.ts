import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import type { User } from '@/types'
import { authService } from '@/services/authService'

function getStoredUser(): User | null {
  const savedUser = localStorage.getItem('user')
  if (!savedUser) return null
  try {
    return JSON.parse(savedUser) as User
  } catch {
    return null
  }
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(getStoredUser())
  const token = ref<string | null>(localStorage.getItem('token'))
  const loading = ref(false)
  const error = ref<string | null>(null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials: { email: string; password: string }) {
    loading.value = true
    error.value = null
    try {
      const { data } = await authService.login(credentials.email, credentials.password)
      user.value = data.data.user
      token.value = data.data.token
      localStorage.setItem('token', data.data.token)
      localStorage.setItem('user', JSON.stringify(data.data.user))
      return true
    } catch (e: unknown) {
      if (axios.isAxiosError(e) && e.response?.data?.message) {
        error.value = e.response.data.message
      } else {
        error.value = 'Error al iniciar sesión'
      }
      return false
    } finally {
      loading.value = false
    }
  }

  async function register(payload: { name: string; email: string; password: string; password_confirmation: string }) {
    loading.value = true
    error.value = null
    try {
      const { data } = await authService.register(payload.name, payload.email, payload.password, payload.password_confirmation)
      user.value = data.data.user
      token.value = data.data.token
      localStorage.setItem('token', data.data.token)
      localStorage.setItem('user', JSON.stringify(data.data.user))
      return true
    } catch (e: unknown) {
      if (axios.isAxiosError(e) && e.response?.data?.message) {
        error.value = e.response.data.message
      } else {
        error.value = 'Error al registrarse'
      }
      return false
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await authService.logout()
    } catch {
      // Ignore logout errors
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  }

  async function fetchUser() {
    if (!token.value) return
    loading.value = true
    try {
      const { data } = await authService.me()
      user.value = data.data
      localStorage.setItem('user', JSON.stringify(data.data))
    } catch {
      user.value = null
      token.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    } finally {
      loading.value = false
    }
  }

  return { user, token, loading, error, isAuthenticated, login, register, logout, fetchUser }
})