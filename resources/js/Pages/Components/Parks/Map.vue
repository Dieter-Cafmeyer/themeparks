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
    default: () => [50.85, 4.35]
  },
  defaultZoom: {
    type: Number,
    default: 15
  },
  attractions: {
    type: Object,
  },
  park: {
    type: Object,
  },
  shows: {
    type: Object,
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
  if (!children.length) return [props.park.location.latitude, props.park.location.longitude]

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

  map.addControl(new maplibregl.FullscreenControl());
  map.addControl(new maplibregl.NavigationControl());

  const geolocate = new maplibregl.GeolocateControl({
    positionOptions: { enableHighAccuracy: true },
    trackUserLocation: true,
    showUserHeading: true,
    showAccuracyCircle: true
  })
  map.addControl(geolocate)


  addMarkers()
})

// Watch filteredChildren
watch(filteredChildren, () => {
  clearMarkers()
  addMarkers()
}, { deep: true })

function createMarkerElement(entityType, attraction = null) {
  const el = document.createElement('div')
  el.classList.add('custom-marker')

  switch (entityType) {
    case 'ATTRACTION':
      if (attraction) {
        let status = statusClass(attraction);

        if (!status) status = 'closed'
        el.classList.add('marker-attraction')
        el.classList.add(status)

        if (status === 'closed') {
          el.innerHTML = `<i class="fas fa-lock"></i>`
        } else if (status === 'down') {
          el.innerHTML = `<i class="fas fa-triangle-exclamation"></i>`
        } else if (status === 'refurbishment') {
          el.innerHTML = `<i class="fas fa-person-digging"></i>`
        } else {
          el.innerHTML = `${attraction ? `<span class="wait-time">${attraction.queue?.STANDBY?.waitTime ?? 0}</span>min` : ''}`
        }
      }
      break
    case 'SHOW':
      el.classList.add('marker-show')
      el.innerHTML = `<i class="fas fa-theater-masks"></i>`
      break
    case 'RESTAURANT':
      el.classList.add('marker-restaurant')
      el.innerHTML = `<i class="fas fa-utensils"></i>`
      break
    default:
      el.classList.add('marker-default')
  }

  return el
}

function statusClass(attraction) {

  if (!attraction) return ''

  if (attraction.status === 'CLOSED') {
    return 'closed'
  }

  if (attraction.status === 'REFURBISHMENT') {
    return 'refurbishment'
  }

  if (attraction.status === 'DOWN') {
    return 'down'
  }

  if (attraction.status === 'OPERATING') {
    const waitTime = attraction.queue?.STANDBY?.waitTime ?? 0

    if (waitTime <= 20) return 'open'
    if (waitTime <= 30) return 'openModerate'
    if (waitTime <= 40) return 'openBusy'
    if (waitTime <= 60) return 'openVeryBusy'
    return 'openSuperBusy'
  }

  return ''
};

const attractionMap = computed(() => {
  if (!props.attractions) return {}

  return Object.fromEntries(
    Object.values(props.attractions).map(a => [a.id, a])
  )
})

const showMap = computed(() => {
  if (!props.shows) return {}

  return Object.fromEntries(
    Object.values(props.shows).map(a => [a.id, a])
  )
})

function addMarkers() {
  filteredChildren.value.forEach(a => {
    if (a.location.latitude && a.location.longitude) {
      const attraction = attractionMap.value[a.id];
      let html = `<h4>${a.name}</h4>`;

      if (a.entityType === "ATTRACTION") {
        if (attraction?.status && attraction.status !== 'OPERATING') {
          html += `<strong>${t('status')}</strong><p>${t(attraction.status.toLowerCase())}</p>`;
        }
        if (attraction?.queue?.STANDBY?.waitTime) {
          html += `<strong>${t('standby')}</strong><p>${attraction.queue?.STANDBY?.waitTime ?? 0} min</p>`;
        }
        if (attraction?.queue?.SINGLE_RIDER) {
          html += `<strong>${t('single_rider')}</strong><p>${attraction.queue?.SINGLE_RIDER?.waitTime ?? '0'}</p>`;
        }
      } else if (a.entityType === "SHOW") {
        const show = showMap.value[a.id];
        if (show?.showtimes?.length) {
          const now = new Date()
          const nextShow = show.showtimes
            .map(s => new Date(s.startTime))
            .filter(date => date > now)
            .sort((a, b) => a - b)[0]

          if (nextShow) {
            html += `<br><strong>${t('next_show')}</strong>
            <p><i class="far fa-clock"></i>${nextShow.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false })}</p>`
          }
          if (!nextShow) {
            html += `<strong>${t('next_show')}</strong><p>${t('no_more_shows_today')}</p>`
          }
        }

      } else {

      }

      const el = createMarkerElement(a.entityType, attraction)
      const marker = new maplibregl.Marker({ element: el }).setLngLat([a.location.longitude, a.location.latitude])

      marker.setPopup(new maplibregl.Popup({ offset: 25 }).setHTML(`${html}`))
      marker.addTo(map)
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