import api from './api'
import type { ApiResponse, SimulatorHealth } from '@/types'

export const simulatorService = {
  health: () => api.get<ApiResponse<SimulatorHealth>>('/simulator/health'),

  generateServices: (count: number) =>
    api.post('/simulator/services/generate', { count }),

  start: () => api.post('/simulator/simulation/start', {}),

  stop: () => api.post('/simulator/simulation/stop', {}),
}