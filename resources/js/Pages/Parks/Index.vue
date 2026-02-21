<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

import ParkItem from '../Components/Parks/ParkItem.vue'
import ParksMap from '../Components/Parks/ParksMap.vue'

const { props: pageProps } = usePage()
const t = (key) => pageProps.translations?.[key] ?? key

const props = defineProps({
    destinations: Array,
    title: String,
    moreInfo: String,
})

const search = ref('')
const view = ref('list')

const filteredDestinations = computed(() => {
    const term = search.value.trim().toLowerCase()

    if (!term) return props.destinations

    return props.destinations
        .map(destination => {
            const destinationMatches =
                destination.name.toLowerCase().includes(term)

            if (destinationMatches) return destination

            const filteredParks = destination.parks.filter(park =>
                park.name.toLowerCase().includes(term)
            )

            return {
                ...destination,
                parks: filteredParks,
            }
        })
        .filter(destination =>
            destination.name.toLowerCase().includes(term) ||
            destination.parks.length > 0
        )
})

const allParks = computed(() => {
    return filteredDestinations.value.flatMap(d =>
        d.parks.map(p => ({
            ...p,
            destination_name: d.name
        }))
    )
})
</script>

<template>

    <Head :title="` | ${t('destinations')}`" />

    <div class="container">

        <div class="park_header space-top-md space-bottom-md">
            <h1>{{ t('destinations') }}</h1>

            <div class="view-toggle">
                <button :class="{ active: view === 'list' }" @click="view = 'list'">
                    <i class="fas fa-list"></i>
                </button>

                <button :class="{ active: view === 'map' }" @click="view = 'map'">
                    <i class="far fa-map"></i>
                </button>
            </div>
        </div>

        <!-- LIST -->
        <div v-if="view === 'list'">
            <div class="park_search search form-item">
                <input id="search" v-model="search" type="text" :placeholder="t('search_resort')" />
                <i class="fas fa-magnifying-glass"></i>
            </div>

            <div v-for="destination in filteredDestinations" :key="destination.id"
                class="park_overview space-bottom-md">
                <h2 class="park_overview--title">
                    {{ destination.name }}
                </h2>

                <ParkItem v-for="park in destination.parks" :key="park.id" :park="park" />
            </div>
        </div>

        <!-- MAP -->
        <ParksMap v-if="view === 'map'" :parks="allParks" />

    </div>
</template>