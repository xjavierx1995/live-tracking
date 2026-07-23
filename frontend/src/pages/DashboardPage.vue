<template>
  <q-page class="row">
    <!-- Sidebar: Services List -->
    <div class="col-12 col-md-4 q-pa-md" style="max-width: 400px">
      <ServiceList
        :services="services"
        :loading="loading"
        :selected-id="selectedService?.id"
        @select="onSelectService"
        @refresh="fetchServices"
      />
      <SimulatorControls
        class="q-mt-md"
        :is-running="isRunning"
        :loading="simLoading"
        :error="simError"
        @check-health="checkHealth"
        @start-simulation="handleStart"
        @stop-simulation="handleStop"
        @generate-services="handleGenerate"
      />
    </div>

    <!-- Map -->
    <div class="col">
      <MapView
        :services="services"
        :selected-service="selectedService"
        @select-service="onMapSelectService"
      />
    </div>
  </q-page>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue'
import type { ServiceWithTrackings } from '@/types'
import { serviceService } from '@/services/serviceService'
import { useSimulator } from '@/composables/useSimulator'
import MapView from '@/components/map/MapView.vue'
import ServiceList from '@/components/services/ServiceList.vue'
import SimulatorControls from '@/components/simulator/SimulatorControls.vue'

const { isRunning, loading: simLoading, error: simError, checkHealth, startSimulation, stopSimulation, generateServices } = useSimulator()

const services = ref<ServiceWithTrackings[]>([])
const loading = ref(false)
const selectedService = ref<ServiceWithTrackings | null>(null)
let pollingInterval: ReturnType<typeof setInterval> | null = null

async function fetchServices() {
  loading.value = true
  try {
    const { data } = await serviceService.listWithTracking()
    const serviceList = data.data || []
    services.value = serviceList
    if (serviceList.length > 0) {
      if (!selectedService.value || !serviceList.some((s: ServiceWithTrackings) => s.id === selectedService.value?.id)) {
        selectedService.value = serviceList[0]
      } else {
        const updated = serviceList.find((s: ServiceWithTrackings) => s.id === selectedService.value?.id)
        if (updated) selectedService.value = updated
      }
    } else {
      selectedService.value = null
    }
  } catch (error) {
    console.error('Error loading services:', error)
  } finally {
    loading.value = false
  }
}

function onSelectService(service: ServiceWithTrackings) {
  if (selectedService.value?.id === service.id) {
    selectedService.value = null
  } else {
    selectedService.value = service
  }
}

function onMapSelectService(serviceId: number) {
  const found = services.value.find((s) => s.id === serviceId)
  if (found) onSelectService(found)
}

function startPolling() {
  if (pollingInterval) return
  fetchServices()
  pollingInterval = setInterval(fetchServices, 30000)
}

function stopPolling() {
  if (pollingInterval) {
    clearInterval(pollingInterval)
    pollingInterval = null
  }
}

async function handleStart() {
  await startSimulation()
  checkHealth()
}

async function handleStop() {
  await stopSimulation()
  checkHealth()
}

async function handleGenerate(count: number) {
  await generateServices(count)
}

watch(isRunning, (running) => {
  if (running) {
    startPolling()
  } else {
    stopPolling()
  }
})

onMounted(() => {
  checkHealth()
})

onUnmounted(() => {
  stopPolling()
})
</script>
