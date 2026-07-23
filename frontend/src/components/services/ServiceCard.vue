<template>
  <div class="service-card" :class="{ 'service-card--selected': selected }">
    <div class="card-avatar" :class="{ 'card-avatar--selected': selected }">
      {{ service.name.charAt(0).toUpperCase() }}
    </div>
    <div class="card-body">
      <div class="card-name">{{ service.name }}</div>
      <div class="card-meta">
        <q-icon name="place" size="12px" />
        {{ (service.trackings || []).length }} puntos
      </div>
    </div>
    <div class="card-status">
      <span class="status-dot" :class="(service.trackings || []).length > 0 ? 'status-dot--active' : 'status-dot--inactive'" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ServiceWithTrackings } from '@/types'

defineProps<{
  service: ServiceWithTrackings
  selected: boolean
}>()
</script>

<style scoped>
.service-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all 0.15s ease;
  border: 1px solid transparent;
}

.service-card:hover {
  background: var(--color-bg);
}

.service-card--selected {
  background: var(--color-primary-light);
  border-color: var(--color-primary);
}

.card-avatar {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
  background: var(--color-bg);
  color: var(--color-text);
  flex-shrink: 0;
  transition: all 0.15s ease;
}

.card-avatar--selected {
  background: var(--color-primary);
  color: white;
}

.card-body {
  flex: 1;
  min-width: 0;
}

.card-name {
  font-size: 13px;
  font-weight: 500;
  color: var(--color-text-heading);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.card-meta {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  color: var(--color-text-muted);
  margin-top: 2px;
}

.card-status {
  flex-shrink: 0;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: block;
}

.status-dot--active {
  background: var(--color-success);
  box-shadow: 0 0 0 3px var(--color-success-light);
}

.status-dot--inactive {
  background: #D1D5DB;
}
</style>
