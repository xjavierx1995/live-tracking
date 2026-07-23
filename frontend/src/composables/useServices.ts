import { ref } from 'vue'
import { serviceService } from '@/services/serviceService'
import type { ServiceWithTrackings } from '@/types'

export function useServices() {
  const services = ref<ServiceWithTrackings[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchServicesWithTracking() {
    loading.value = true
    error.value = null
    try {
      const { data } = await serviceService.listWithTracking()
      services.value = data.data
    } catch {
      error.value = 'Error cargando servicios'
    } finally {
      loading.value = false
    }
  }

  return { services, loading, error, fetchServicesWithTracking }
}