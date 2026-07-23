<template>
  <q-card>
    <q-card-section>
      <div class="text-h6 q-mb-md">Simulador</div>

      <q-banner v-if="error" class="bg-negative text-white q-mb-md">
        {{ error }}
      </q-banner>

      <div class="row items-center q-mb-md">
        <q-badge
          :color="isRunning ? 'positive' : 'grey'"
          class="q-mr-sm"
        >
          {{ isRunning ? 'Simulación activa' : 'Simulación inactiva' }}
        </q-badge>
        <q-btn flat round dense icon="refresh" size="sm" @click="$emit('checkHealth')">
          <q-tooltip>Verificar estado</q-tooltip>
        </q-btn>
      </div>

      <q-separator class="q-mb-md" />

      <!-- Generate Services -->
      <div class="row q-gutter-sm items-center q-mb-md">
        <q-input
          v-model.number="serviceCount"
          type="number"
          label="Cantidad"
          dense
          outlined
          style="max-width: 100px"
          :rules="[(val) => val > 0 || 'Mayor a 0']"
        />
        <q-btn
          label="Generar servicios"
          color="secondary"
          dense
          :loading="loading"
          :disable="serviceCount < 1 || isRunning"
          @click="$emit('generateServices', serviceCount)"
        />
      </div>

      <!-- Start/Stop Simulation -->
      <div class="row q-gutter-sm">
        <q-btn
          label="Iniciar simulación"
          color="positive"
          icon="play_arrow"
          dense
          :loading="loading"
          :disable="isRunning"
          @click="$emit('startSimulation')"
        />
        <q-btn
          label="Detener simulación"
          color="negative"
          icon="stop"
          dense
          :loading="loading"
          :disable="!isRunning"
          @click="$emit('stopSimulation')"
        />
      </div>
    </q-card-section>
  </q-card>
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
