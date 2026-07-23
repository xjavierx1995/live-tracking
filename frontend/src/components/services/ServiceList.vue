<template>
  <div class="service-list">
    <div class="list-header">
      <div class="list-title">
        <q-icon name="directions_bus" size="20px" class="q-mr-xs" />
        Servicios
      </div>
      <div class="list-count" v-if="services.length > 0">
        {{ services.length }}
      </div>
      <q-btn flat round dense icon="refresh" size="sm" class="refresh-btn" :loading="loading" @click="$emit('refresh')">
        <q-tooltip>Actualizar</q-tooltip>
      </q-btn>
    </div>

    <div class="list-body">
      <div v-if="loading && services.length === 0" class="loading-state">
        <q-spinner-dots color="primary" size="32px" />
        <span>Cargando servicios...</span>
      </div>

      <div v-else-if="services.length === 0" class="empty-state">
        <q-icon name="inbox" size="48px" color="grey-4" />
        <span class="empty-title">Sin servicios</span>
        <span class="empty-subtitle">Genera servicios desde el panel de control</span>
      </div>

      <div v-else class="service-items">
        <ServiceCard
          v-for="service in services"
          :key="service.id"
          :service="service"
          :selected="service.id === selectedId"
          @click="$emit('select', service)"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ServiceWithTrackings } from '@/types'
import ServiceCard from './ServiceCard.vue'

defineProps<{
  services: ServiceWithTrackings[]
  loading: boolean
  selectedId: number | undefined
}>()

defineEmits<{
  select: [service: ServiceWithTrackings]
  refresh: []
}>()
</script>

<style scoped>
.service-list {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.list-header {
  display: flex;
  align-items: center;
  padding: 16px 18px;
  border-bottom: 1px solid var(--color-border);
  flex-shrink: 0;
}

.list-title {
  display: flex;
  align-items: center;
  font-size: 15px;
  font-weight: 600;
  color: var(--color-text-heading);
}

.list-count {
  margin-left: 8px;
  padding: 2px 8px;
  background: var(--color-primary-light);
  color: var(--color-primary);
  border-radius: 10px;
  font-size: 11px;
  font-weight: 600;
}

.refresh-btn {
  margin-left: auto;
  color: var(--color-text-muted);
}
.refresh-btn:hover {
  color: var(--color-primary);
}

.list-body {
  flex: 1;
  overflow-y: auto;
  padding: 8px;
}

.service-items {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 48px 24px;
  color: var(--color-text-muted);
  font-size: 13px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 48px 24px;
  text-align: center;
}

.empty-title {
  font-size: 14px;
  font-weight: 500;
  color: var(--color-text);
}

.empty-subtitle {
  font-size: 12px;
  color: var(--color-text-muted);
}
</style>
