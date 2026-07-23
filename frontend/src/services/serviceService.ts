import api from './api'
import type { ApiResponse, Service, ServiceWithTrackings, TrackingPoint } from '@/types'

export const serviceService = {
  list: () => api.get<ApiResponse<Service[]>>('/services'),

  listWithTracking: () => api.get<ApiResponse<ServiceWithTrackings[]>>('/services/tracking'),

  getById: (id: number) => api.get<ApiResponse<Service>>(`/services/${id}`),

  getTracking: (id: number) => api.get<ApiResponse<TrackingPoint[]>>(`/services/${id}/tracking`),
}