<script setup>
import { onMounted, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

import maplibregl from 'maplibre-gl'
import 'maplibre-gl/dist/maplibre-gl.css'

const props = defineProps({
    parks: Array
})

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

const mapContainer = ref(null)
let map

onMounted(() => {
    map = new maplibregl.Map({
        container: mapContainer.value,
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
        },
        center: [10, 50],
        zoom: 4
    })

    map.addControl(new maplibregl.NavigationControl())

    addMarkers()
})

function addMarkers() {

    const bounds = new maplibregl.LngLatBounds()
    let hasMarkers = false

    props.parks.forEach(park => {
        if (!park.latitude || !park.longitude) return

        const lngLat = [park.longitude, park.latitude]

        const popup = new maplibregl.Popup({ offset: 25 }).setHTML(`
<h4>${park.name}</h4><br>
<a class="button" href="/parks/${park.api_id}">${t('visit_park')}</a>
`)

        new maplibregl.Marker()
            .setLngLat(lngLat)
            .setPopup(popup)
            .addTo(map)

        bounds.extend(lngLat)
        hasMarkers = true
    })

    if (hasMarkers && props.parks.length < 4) {
        map.fitBounds(bounds, {
            padding: 80,
            maxZoom: 10,
            duration: 0
        })
    }
}
</script>

<template>
    <div ref="mapContainer" class="parks-map"></div>
</template>