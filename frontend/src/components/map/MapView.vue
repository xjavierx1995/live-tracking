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

      <!-- All services: show last location marker -->
      <l-marker
        v-for="svc in servicesWithLocation"
        :key="svc.id"
        :lat-lng="svc.lastLocation"
        @click="emit('selectService', svc.id)"
      >
        <l-popup>
          <div class="popup-service">
            <div class="popup-name">{{ svc.name }}</div>
            <div v-if="svc.lastTracking" class="popup-time">
              {{ formatTime(svc.lastTracking.created_at) }}
            </div>
          </div>
        </l-popup>
      </l-marker>

      <!-- Selected service: polyline -->
      <l-polyline
        v-if="selectedService?.polyline && polylinePoints.length > 0"
        :lat-lngs="polylinePoints"
        color="#4A6CF7"
        :weight="4"
        :opacity="0.85"
      />

      <!-- Selected service: all tracking points -->
      <l-marker
        v-for="(point, index) in selectedTrackings"
        :key="'track-' + index"
        :lat-lng="[point.latitude, point.longitude]"
      >
        <l-popup>
          <div class="popup-tracking">
            <div class="popup-tracking-label">Punto {{ index + 1 }}</div>
            <div class="popup-tracking-time">{{ formatTime(point.created_at) }}</div>
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
  services: ServiceWithTrackings[]
  selectedService: ServiceWithTrackings | null
}>()

const emit = defineEmits<{
  selectService: [serviceId: number]
}>()

const zoom = ref(13)
const center = ref<[number, number]>([40.4168, -3.7038])

// Services that have at least one tracking point
const servicesWithLocation = computed(() => {
  return props.services
    .filter((s) => s.trackings && s.trackings.length > 0)
    .map((s) => ({
      id: s.id,
      name: s.name,
      lastLocation: getLatestPoint(s.trackings!),
      lastTracking: getLatestPointObj(s.trackings!),
    }))
})

function getLatestPoint(trackings: TrackingPoint[]): [number, number] {
  const last = trackings[trackings.length - 1]
  return [last.latitude, last.longitude]
}

function getLatestPointObj(trackings: TrackingPoint[]): TrackingPoint {
  return trackings[trackings.length - 1]
}

// Trackings of the selected service (for full route display)
const selectedTrackings = computed<TrackingPoint[]>(() => {
  return props.selectedService?.trackings || []
})

function safeDecodePolyline(str: string): [number, number][] {
  if (!str) return []
  try {
    const decodeFn = polyline.decode || (polyline as any).default?.decode
    if (typeof decodeFn === 'function') {
      return decodeFn(str)
    }
  } catch (err) {
    console.error('Error decoding polyline:', err)
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
  if (!props.selectedService?.polyline) return []
  return safeDecodePolyline(props.selectedService.polyline)
})

// Center on selected service's last position
watch(
  () => props.selectedService,
  (service) => {
    if (service && service.trackings && service.trackings.length > 0) {
      const last = service.trackings[service.trackings.length - 1]
      center.value = [last.latitude, last.longitude]
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
  height: 100%;
  width: 100%;
  border-radius: 0;
}

.popup-service {
  padding: 2px 0;
}

.popup-name {
  font-weight: 600;
  font-size: 13px;
  color: #1F2937;
  margin-bottom: 2px;
}

.popup-time {
  font-size: 11px;
  color: #6B7280;
}

.popup-tracking {
  padding: 2px 0;
}

.popup-tracking-label {
  font-weight: 600;
  font-size: 12px;
  color: #4A6CF7;
  margin-bottom: 2px;
}

.popup-tracking-time {
  font-size: 11px;
  color: #6B7280;
}
</style>
