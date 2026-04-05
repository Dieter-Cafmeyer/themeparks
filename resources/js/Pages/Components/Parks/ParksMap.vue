<script setup>
import { onMounted, nextTick, ref } from 'vue'
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

onMounted(async () => {
    await nextTick()
    
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

    map.on('load', () => {
        addMarkers()
    })
})

function addMarkers() {

    const bounds = new maplibregl.LngLatBounds()
    let hasMarkers = false

    props.parks.forEach(park => {
        if (!park.latitude || !park.longitude) return

        const lngLat = [park.longitude, park.latitude]

        // Create custom marker element with pin
        const markerEl = document.createElement('div')
        markerEl.className = 'custom-marker'
        markerEl.innerHTML = `
            <div class="marker-glass">
                <i class="fas fa-location-dot"></i>
            </div>
            <div class="marker-pin"></div>
        `

        const popup = new maplibregl.Popup({ 
            offset: 40,
            className: 'custom-popup'
        }).setHTML(`
<div class="popup-content">
    <h4>${park.name}</h4>
    <a class="button" href="/parks/${park.slug}">${t('visit_park')}</a>
</div>
`)

        new maplibregl.Marker({ 
            element: markerEl,
            anchor: 'bottom'
        })
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

<style scoped>
:deep(.custom-marker) {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
}

:deep(.marker-glass) {
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 
        0 8px 32px 0 rgba(0, 0, 53, 0.15),
        inset 0 1px 2px 0 rgba(255, 255, 255, 0.6),
        0 4px 16px 0 rgba(0, 0, 53, 0.1);
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    position: relative;
    overflow: hidden;
}

:deep(.marker-glass::before) {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.5) 0%,
        rgba(255, 255, 255, 0.1) 50%,
        transparent 100%
    );
    transform: rotate(45deg);
    pointer-events: none;
}

:deep(.marker-glass i) {
    color: #000035;
    font-size: 20px;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 53, 0.2));
    z-index: 1;
    position: relative;
}

:deep(.marker-pin) {
    width: 3px;
    height: 8px;
    background: linear-gradient(to bottom, 
        rgba(0, 0, 53, 0.6) 0%, 
        rgba(0, 0, 53, 0.3) 100%
    );
    border-radius: 1.5px;
    margin-top: -1px;
    box-shadow: 0 2px 4px rgba(0, 0, 53, 0.2);
}

:deep(.custom-marker:hover .marker-glass) {
    transform: scale(1.15);
    background: rgba(255, 255, 255, 0.4);
    box-shadow: 
        0 12px 48px 0 rgba(0, 0, 53, 0.2),
        inset 0 2px 3px 0 rgba(255, 255, 255, 0.7),
        0 6px 24px 0 rgba(0, 0, 53, 0.15);
    border-color: rgba(255, 255, 255, 0.6);
}

:deep(.custom-marker:hover .marker-pin) {
    background: linear-gradient(to bottom, 
        rgba(0, 0, 53, 0.8) 0%, 
        rgba(0, 0, 53, 0.4) 100%
    );
}

:deep(.custom-popup .maplibregl-popup-content) {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(30px) saturate(180%);
    -webkit-backdrop-filter: blur(30px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 24px;
    padding: 24px;
    box-shadow: 
        0 8px 32px 0 rgba(0, 0, 53, 0.12),
        inset 0 1px 1px 0 rgba(255, 255, 255, 0.8);
    font-family: 'effra', sans-serif;
}

:deep(.custom-popup .maplibregl-popup-tip) {
    border-top-color: rgba(255, 255, 255, 0.85);
}

:deep(.custom-popup .popup-content h4) {
    margin: 0 0 16px 0;
    color: #000035;
    font-size: 18px;
    font-weight: 600;
    letter-spacing: -0.3px;
}

:deep(.custom-popup .popup-content .button) {
    display: inline-block;
    padding: 12px 24px;
    background: rgba(0, 0, 53, 0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: white;
    border-radius: 20px;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
    font-size: 14px;
    font-weight: 500;
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 4px 16px rgba(0, 0, 53, 0.2);
}

:deep(.custom-popup .popup-content .button:hover) {
    background: rgba(0, 0, 85, 0.95);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 53, 0.25);
    border-color: rgba(255, 255, 255, 0.25);
}

:deep(.maplibregl-popup-close-button) {
    color: #000035;
    font-size: 24px;
    padding: 8px;
    right: 8px;
    top: 8px;
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

:deep(.maplibregl-popup-close-button:hover) {
    background: transparent;
    color: #000035;
    opacity: 1;
}
</style>