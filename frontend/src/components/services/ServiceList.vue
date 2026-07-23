<template>
  <q-card>
    <q-card-section class="q-pa-sm">
      <div class="row items-center q-mb-sm">
        <div class="text-h6">Servicios</div>
        <q-space />
        <q-btn flat round dense icon="refresh" :loading="loading" @click="$emit('refresh')">
          <q-tooltip>Actualizar</q-tooltip>
        </q-btn>
      </div>

      <q-scroll-area style="height: calc(100vh - 350px)">
        <q-list separator>
          <ServiceCard
            v-for="service in services"
            :key="service.id"
            :service="service"
            :selected="service.id === selectedId"
            @click="$emit('select', service)"
          />
          <q-item v-if="services.length === 0 && !loading">
            <q-item-section class="text-grey"> No hay servicios disponibles </q-item-section>
          </q-item>
        </q-list>
      </q-scroll-area>
    </q-card-section>
  </q-card>
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