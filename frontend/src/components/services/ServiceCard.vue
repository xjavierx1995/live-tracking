<template>
  <q-item
    clickable
    :active="selected"
    :class="{ 'bg-blue-1': selected }"
    class="q-py-sm"
  >
    <q-item-section avatar>
      <q-avatar color="primary" text-color="white" size="36px">
        {{ service.name.charAt(0).toUpperCase() }}
      </q-avatar>
    </q-item-section>

    <q-item-section>
      <q-item-label class="text-weight-medium">{{ service.name }}</q-item-label>
      <q-item-label caption>
        {{ (service.trackings || []).length }} puntos de tracking
      </q-item-label>
    </q-item-section>

    <q-item-section side>
      <q-badge
        :color="(service.trackings || []).length > 0 ? 'positive' : 'grey'"
        :label="(service.trackings || []).length > 0 ? 'Activo' : 'Sin datos'"
      />
    </q-item-section>
  </q-item>
</template>

<script setup lang="ts">
import type { ServiceWithTrackings } from '@/types'

defineProps<{
  service: ServiceWithTrackings
  selected: boolean
}>()
</script>