<script setup>
import { onMounted, ref, watch, nextTick, computed } from 'vue'

import maplibregl from 'maplibre-gl'
import 'maplibre-gl/dist/maplibre-gl.css'
import { usePage } from '@inertiajs/vue3'

import SwitchToggle from '../Form/SwitchToggle.vue'


const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

const props = defineProps({
  map: {
    type: Object,
  },
  defaultCenter: {
    type: Array,
    default: () => [50.85, 4.35] // fallback coords [lat, lng]
  },
  defaultZoom: {
    type: Number,
    default: 15
  }
})

const mapContainer = ref(null)

// checkbox states
const showAttractions = ref(true)
const showShows = ref(true)
const showRestaurants = ref(true)

let map = null
let markers = []

// Computed: gefilterde children
const filteredChildren = computed(() => {
  if (!props.map?.children) return []

  return props.map.children.filter(c => {
    if (c.entityType === 'ATTRACTION' && showAttractions.value) return true
    if (c.entityType === 'SHOW' && showShows.value) return true
    if (c.entityType === 'RESTAURANT' && showRestaurants.value) return true
    return false
  })
})

function getCenter() {
  const children = filteredChildren.value
  if (!children.length) return props.defaultCenter

  const lats = children.map(c => c.location.latitude).filter(Boolean)
  const lngs = children.map(c => c.location.longitude).filter(Boolean)
  if (!lats.length || !lngs.length) return props.defaultCenter

  const lat = (Math.min(...lats) + Math.max(...lats)) / 2
  const lng = (Math.min(...lngs) + Math.max(...lngs)) / 2
  return [lat, lng]
}

onMounted(async () => {
  await nextTick()

  const center = getCenter()
  map = new maplibregl.Map({
    container: mapContainer.value,
    center: [center[1], center[0]], // lng, lat
    zoom: props.defaultZoom,
    style: {
      version: 8,
      sources: {
        osm: {
          type: 'raster',
          tiles: ['https://tile.openstreetmap.org/{z}/{x}/{y}.png'],
          tileSize: 256
        }
      },
      layers: [{
        id: 'osm',
        type: 'raster',
        source: 'osm'
      }]
    }
  })

  map.addControl(new maplibregl.NavigationControl())
  addMarkers()
})

// Watch filteredChildren
watch(filteredChildren, () => {
  clearMarkers()
  addMarkers()

  const center = getCenter()
  map.setCenter([center[1], center[0]])
}, { deep: true })

function addMarkers() {
  filteredChildren.value.forEach(a => {
    if (a.location.latitude && a.location.longitude) {
      const marker = new maplibregl.Marker()
        .setLngLat([a.location.longitude, a.location.latitude])
        .setPopup(
          new maplibregl.Popup({ offset: 25 }).setHTML(`
            <strong>${a.name}</strong><br>
            Status: ${a.status ?? 'Unknown'}
          `)
        )
        .addTo(map)
      markers.push(marker)
    }
  })
}

function clearMarkers() {
  markers.forEach(m => m.remove())
  markers = []
}
</script>

<template>
  <div class="map-filters space-top-md space-bottom-md">
    <label>
      <SwitchToggle v-model="showAttractions" />
      <span>{{ t('attractions_title') }}</span>
    </label>
    <label>
      <SwitchToggle v-model="showShows" />
      <span>{{ t('shows_title') }}</span>
    </label>
    <label>
      <SwitchToggle v-model="showRestaurants" />
      <span>Restaurants</span>
    </label>
  </div>

  <div ref="mapContainer" id="map"></div>
</template>