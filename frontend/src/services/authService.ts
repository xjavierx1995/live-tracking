import api from './api'
import type { ApiResponse, AuthResponse, User } from '@/types'

export const authService = {
  login: (email: string, password: string) =>
    api.post<ApiResponse<AuthResponse>>('/auth/login', { email, password }),

  register: (name: string, email: string, password: string, password_confirmation: string) =>
    api.post<ApiResponse<AuthResponse>>('/auth/register', { name, email, password, password_confirmation }),

  me: () => api.get<ApiResponse<User>>('/auth/me'),

  logout: () => api.post('/auth/logout'),
}