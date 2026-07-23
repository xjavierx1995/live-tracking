<template>
  <q-page class="dashboard">
    <!-- Mobile tabs -->
    <div class="mobile-tabs">
      <button
        class="mobile-tab"
        :class="{ 'mobile-tab--active': mobileView === 'list' }"
        @click="mobileView = 'list'"
      >
        <q-icon name="list" size="18px" />
        Servicios
      </button>
      <button
        class="mobile-tab"
        :class="{ 'mobile-tab--active': mobileView === 'map' }"
        @click="mobileView = 'map'"
      >
        <q-icon name="map" size="18px" />
        Mapa
      </button>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" :class="{ 'sidebar--hidden-mobile': mobileView !== 'list' }">
      <ServiceList
        :services="services"
        :loading="loading"
        :selected-id="selectedService?.id"
        @select="onSelectService"
        @refresh="fetchServices"
      />
      <SimulatorControls
        class="simulator-section"
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
    <div class="map-area" :class="{ 'map-area--hidden-mobile': mobileView !== 'map' }">
      <MapView
        :services="services"
        :selected-service="selectedService"
        @select-service="onMapSelectService"
      />
      <div v-if="selectedService" class="selected-info">
        <div class="selected-info-inner">
          <div class="selected-name">{{ selectedService.name }}</div>
          <div class="selected-meta">
            {{ (selectedService.trackings || []).length }} puntos
          </div>
          <button class="deselect-btn" @click="selectedService = null">
            <q-icon name="close" size="14px" />
          </button>
        </div>
      </div>
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
const mobileView = ref<'list' | 'map'>('list')
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
    mobileView.value = 'map'
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

<style scoped>
.dashboard {
  display: flex;
  height: calc(100vh - 50px);
  background: var(--color-bg);
  overflow: hidden;
  position: relative;
}

.mobile-tabs {
  display: none;
}

.sidebar {
  width: 380px;
  min-width: 380px;
  display: flex;
  flex-direction: column;
  background: var(--color-surface);
  border-right: 1px solid var(--color-border);
  overflow: hidden;
}

.sidebar > *:first-child {
  flex: 1;
  overflow: hidden;
}

.simulator-section {
  border-top: 1px solid var(--color-border);
  flex-shrink: 0;
}

.map-area {
  flex: 1;
  position: relative;
  min-width: 0;
}

.selected-info {
  position: absolute;
  bottom: 16px;
  left: 16px;
  z-index: 1000;
  background: var(--color-surface);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-lg);
  padding: 10px 14px;
  border: 1px solid var(--color-border);
}

.selected-info-inner {
  display: flex;
  align-items: center;
  gap: 10px;
}

.selected-name {
  font-weight: 600;
  font-size: 13px;
  color: var(--color-text-heading);
}

.selected-meta {
  font-size: 11px;
  color: var(--color-text-muted);
}

.deselect-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-bg);
  color: var(--color-text-muted);
  cursor: pointer;
  transition: all 0.15s ease;
  padding: 0;
}
.deselect-btn:hover {
  background: var(--color-danger-light);
  border-color: var(--color-danger);
  color: var(--color-danger);
}

/* Mobile */
@media (max-width: 768px) {
  .dashboard {
    flex-direction: column;
  }

  .mobile-tabs {
    display: flex;
    background: var(--color-surface);
    border-bottom: 1px solid var(--color-border);
    flex-shrink: 0;
    z-index: 10;
  }

  .mobile-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 12px;
    border: none;
    background: none;
    color: var(--color-text-muted);
    font-size: 13px;
    font-weight: 500;
    font-family: var(--font-sans);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.15s ease;
  }

  .mobile-tab--active {
    color: var(--color-primary);
    border-bottom-color: var(--color-primary);
    background: var(--color-primary-light);
  }

  .sidebar {
    width: 100%;
    min-width: 100%;
    flex: 1;
    border-right: none;
    overflow: hidden;
  }

  .sidebar--hidden-mobile {
    display: none;
  }

  .map-area {
    flex: 1;
    position: relative;
  }

  .map-area--hidden-mobile {
    display: none;
  }

  .selected-info {
    bottom: 12px;
    left: 12px;
    right: 12px;
  }
}
</style>
