<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

import AttractionItem from './AttractionItem.vue'
import SwitchToggle from '../Form/SwitchToggle.vue'

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

const props = defineProps({
    attractions: {
        type: Object,
        default: () => ({})
    },
})

const search = ref('')
const showFavoritesOnly = ref(false)

const attractionsArray = computed(() => Object.values(props.attractions))

const filteredAttractions = computed(() => {
    let result = attractionsArray.value

    // Zoekfilter
    if (search.value) {
        const term = search.value.toLowerCase()
        result = result.filter(attraction =>
            attraction.name?.toLowerCase().includes(term)
        )
    }

    // Favorieten filter
    if (showFavoritesOnly.value) {
        result = result.filter(attraction => attraction.is_favorited)
    }

    return result
})

const groupedAttractions = computed(() => {
    const groups = {
        OPERATING: [],
        DOWN: [],
        CLOSED: []
    }

    filteredAttractions.value.forEach(attraction => {
        const status = attraction.status ?? 'OPERATING'
        if (groups[status]) {
            groups[status].push(attraction)
        } else {
            groups[status] = [attraction]
        }
    })

    Object.keys(groups).forEach(status => {
        if (status === 'OPERATING') {
            // Hoog → laag wachttijd
            groups[status].sort((a, b) => {
                const aTime = a.queue?.STANDBY?.waitTime ?? 0
                const bTime = b.queue?.STANDBY?.waitTime ?? 0
                return bTime - aTime
            })
        } else {
            // Alfabetisch
            groups[status].sort((a, b) => {
                const nameA = a.name ?? ''
                const nameB = b.name ?? ''
                return nameA.localeCompare(nameB)
            })
        }
    })

    return groups
})
</script>

<template>
    <div class="attractions">
        <div class="attractions_filters">
            <div class="search form-item attractions_search">
                <input id="search" v-model="search" type="text" :placeholder="t('search_title_attractions')" />
                <i class="fas fa-magnifying-glass"></i>
            </div>
        </div>

        <div class="attractions_favorites-toggle">
            <label>
                <SwitchToggle v-model="showFavoritesOnly" :star="true" />
            </label>
        </div>

        <div v-for="(group, status) in groupedAttractions" :key="status" class="attractions_group ">
            <template v-if="group.length > 0">
                <h2 class="space-bottom-md space-top-md">{{ status }} </h2>

                <AttractionItem v-for="attraction in group" :key="attraction.id" :attraction="attraction" />
            </template>
        </div>
    </div>
</template>