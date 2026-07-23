// User types
export interface User {
  id: number
  name: string
  email: string
}

// Service types
export interface Service {
  id: number
  name: string
  start_time: string
  end_time: string
  polyline: string
}

// Tracking types
export interface TrackingPoint {
  latitude: number
  longitude: number
  created_at: string
}

export interface ServiceWithTrackings extends Service {
  trackings: TrackingPoint[]
}

// Auth types
export interface AuthResponse {
  user: User
  token: string
}

// API types
export interface ApiResponse<T> {
  data: T
  message: string
  status: boolean
}

// Simulator types
export interface SimulatorHealth {
  simulator: 'up' | 'down'
}

export interface GenerateServicesInput {
  count: number
}