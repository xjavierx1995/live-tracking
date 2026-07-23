import { ref } from 'vue'
import { simulatorService } from '@/services/simulatorService'

export function useSimulator() {
  const isRunning = ref(false)
  const health = ref<'up' | 'down' | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function checkHealth() {
    try {
      const { data } = await simulatorService.health()
      const status = data.data?.simulator
      health.value = status || 'down'
      isRunning.value = status === 'up'
    } catch {
      health.value = 'down'
      isRunning.value = false
    }
  }

  async function startSimulation() {
    loading.value = true
    error.value = null
    try {
      await simulatorService.start()
      isRunning.value = true
      health.value = 'up'
    } catch {
      error.value = 'Error al iniciar la simulación'
    } finally {
      loading.value = false
    }
  }

  async function stopSimulation() {
    loading.value = true
    error.value = null
    try {
      await simulatorService.stop()
      isRunning.value = false
    } catch {
      error.value = 'Error al detener la simulación'
    } finally {
      loading.value = false
    }
  }

  async function generateServices(count: number) {
    loading.value = true
    error.value = null
    try {
      await simulatorService.generateServices(count)
    } catch {
      error.value = 'Error al generar servicios'
    } finally {
      loading.value = false
    }
  }

  return {
    isRunning,
    health,
    loading,
    error,
    checkHealth,
    startSimulation,
    stopSimulation,
    generateServices,
  }
}