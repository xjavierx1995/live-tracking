<template>
  <div class="sim-controls">
    <div class="controls-header">
      <q-icon name="settings_input_antenna" size="18px" class="q-mr-xs" />
      <span class="controls-title">Simulador</span>
      <div class="sim-status" :class="isRunning ? 'sim-status--active' : 'sim-status--inactive'">
        <span class="sim-status-dot" />
        {{ isRunning ? 'Activo' : 'Inactivo' }}
      </div>
    </div>

    <div v-if="error" class="controls-error">
      <q-icon name="warning" size="14px" />
      {{ error }}
    </div>

    <div class="controls-body">
      <div class="controls-row">
        <q-input
          v-model.number="serviceCount"
          type="number"
          dense
          outlined
          hide-bottom-space
          class="count-input"
          :rules="[(val) => val > 0 || '']"
        />
        <q-btn
          no-caps
          dense
          unelevated
          class="generate-btn"
          :loading="loading"
          :disable="serviceCount < 1 || isRunning"
          @click="$emit('generateServices', serviceCount)"
        >
          <q-icon name="add" size="16px" class="q-mr-xs" />
          Generar
        </q-btn>
      </div>

      <div class="controls-row">
        <q-btn
          no-caps
          dense
          unelevated
          class="start-btn"
          :loading="loading"
          :disable="isRunning"
          @click="$emit('startSimulation')"
        >
          <q-icon name="play_arrow" size="16px" class="q-mr-xs" />
          Iniciar
        </q-btn>
        <q-btn
          no-caps
          dense
          unelevated
          class="stop-btn"
          :loading="loading"
          :disable="!isRunning"
          @click="$emit('stopSimulation')"
        >
          <q-icon name="stop" size="16px" class="q-mr-xs" />
          Detener
        </q-btn>
      </div>
    </div>

    <div class="controls-footer">
      <q-btn flat dense no-caps size="sm" class="refresh-health" @click="$emit('checkHealth')">
        <q-icon name="refresh" size="14px" class="q-mr-xs" />
        Verificar estado
      </q-btn>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
  isRunning: boolean
  loading: boolean
  error: string | null
}>()

defineEmits<{
  checkHealth: []
  startSimulation: []
  stopSimulation: []
  generateServices: [count: number]
}>()

const serviceCount = ref(5)
</script>

<style scoped>
.sim-controls {
  padding: 14px 16px;
}

.controls-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}

.controls-title {
  font-size: 13px;
  font-weight: 600;
  color: var(--color-text-heading);
}

.sim-status {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 500;
  padding: 3px 10px;
  border-radius: 10px;
}

.sim-status--active {
  background: var(--color-success-light);
  color: var(--color-success);
}

.sim-status--inactive {
  background: #F3F4F6;
  color: #9CA3AF;
}

.sim-status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
}

.controls-error {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  background: var(--color-danger-light);
  color: var(--color-danger);
  border-radius: var(--radius-sm);
  font-size: 12px;
  margin-bottom: 12px;
}

.controls-body {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.controls-row {
  display: flex;
  gap: 8px;
}

.count-input {
  width: 72px;
}

.count-input :deep(.q-field__control) {
  height: 36px !important;
  min-height: 36px;
  border-radius: 8px !important;
  border-color: #D1D5DB !important;
  background: white !important;
}

.count-input :deep(.q-field__native) {
  font-size: 13px;
  color: var(--color-text-heading);
}

.generate-btn {
  flex: 1;
  height: 36px !important;
  min-height: 36px;
  background: #4A6CF7 !important;
  color: white !important;
  border-radius: 8px !important;
  font-size: 12px;
  font-weight: 600 !important;
  letter-spacing: 0.2px;
}
.generate-btn:hover {
  background: #3B5DE7 !important;
}

.start-btn {
  flex: 1;
  height: 36px !important;
  min-height: 36px;
  background: #22C55E !important;
  color: white !important;
  border-radius: 8px !important;
  font-size: 12px;
  font-weight: 600 !important;
  letter-spacing: 0.2px;
}
.start-btn:hover {
  background: #16A34A !important;
}

.stop-btn {
  flex: 1;
  height: 36px !important;
  min-height: 36px;
  background: #EF4444 !important;
  color: white !important;
  border-radius: 8px !important;
  font-size: 12px;
  font-weight: 600 !important;
  letter-spacing: 0.2px;
}
.stop-btn:hover {
  background: #DC2626 !important;
}

.controls-footer {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid var(--color-border);
}

.refresh-health {
  color: var(--color-text-muted);
  font-size: 11px;
}
.refresh-health:hover {
  color: var(--color-primary);
}
</style>
