<template>
  <div class="map-container">
    <l-map
      v-model:zoom="zoom"
      v-model:center="center"
      :use-global-leaflet="false"
      style="height: 100%; width: 100%"
    >
      <l-tile-layer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        layer-type="base"
        name="OpenStreetMap"
        attribution="&copy; OpenStreetMap contributors"
      />
      <l-polyline v-if="polylinePoints.length > 0" :lat-lngs="polylinePoints" color="blue" :weight="4" />
      <l-marker
        v-for="(point, index) in trackings"
        :key="index"
        :lat-lng="[point.latitude, point.longitude]"
      >
        <l-popup>
          <div>
            <strong>Punto {{ index + 1 }}</strong><br />
            {{ formatTime(point.created_at) }}
          </div>
        </l-popup>
      </l-marker>
    </l-map>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { LMap, LTileLayer, LPolyline, LMarker, LPopup } from '@vue-leaflet/vue-leaflet'
import * as polyline from '@mapbox/polyline'
import type { ServiceWithTrackings, TrackingPoint } from '@/types'

const props = defineProps<{
  service: ServiceWithTrackings | null
  trackings: TrackingPoint[]
}>()

const zoom = ref(13)
const center = ref<[number, number]>([40.4168, -3.7038]) // Madrid default

function safeDecodePolyline(str: string): [number, number][] {
  if (!str) return []
  try {
    const decodeFn = polyline.decode || (polyline as any).default?.decode
    if (typeof decodeFn === 'function') {
      return decodeFn(str)
    }
  } catch (err) {
    console.error('Error decoding polyline with @mapbox/polyline:', err)
  }
  return fallbackDecodePolyline(str)
}

function fallbackDecodePolyline(encoded: string): [number, number][] {
  const points: [number, number][] = []
  let index = 0, lat = 0, lng = 0
  while (index < encoded.length) {
    let shift = 0, result = 0, byte = 0
    do {
      byte = encoded.charCodeAt(index++) - 63
      result |= (byte & 0x1f) << shift
      shift += 5
    } while (byte >= 0x20)
    lat += result & 1 ? ~(result >> 1) : result >> 1

    shift = 0; result = 0
    do {
      byte = encoded.charCodeAt(index++) - 63
      result |= (byte & 0x1f) << shift
      shift += 5
    } while (byte >= 0x20)
    lng += result & 1 ? ~(result >> 1) : result >> 1

    points.push([lat / 1e5, lng / 1e5])
  }
  return points
}

const polylinePoints = computed<[number, number][]>(() => {
  if (!props.service?.polyline) return []
  return safeDecodePolyline(props.service.polyline)
})

// Update center when service changes
watch(
  () => props.service,
  (service) => {
    if (service && service.trackings && service.trackings.length > 0) {
      const firstTracking = service.trackings[0]
      center.value = [firstTracking.latitude, firstTracking.longitude]
    } else if (polylinePoints.value.length > 0) {
      center.value = polylinePoints.value[0]
    }
  },
  { immediate: true }
)

function formatTime(dateString: string): string {
  return new Date(dateString).toLocaleString('es-ES')
}
</script>

<style scoped>
.map-container {
  height: calc(100vh - 50px);
  width: 100%;
}
</style>