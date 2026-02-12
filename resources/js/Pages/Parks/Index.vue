<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ParkItem from '../Components/Parks/ParkItem.vue';

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

const props = defineProps({
    destinations: Array,
    title: String,
    moreInfo: String,
});

const search = ref('');

const filteredDestinations = computed(() => {
    if (!search.value.trim()) {
        return props.destinations
    }

    const term = search.value.toLowerCase()

    return props.destinations.filter(destination =>
        destination.name.toLowerCase().includes(term)
    )
})
</script>


<template>
    <Head :title="` | ${props.title}`" />

    <div class="container">

        <h1 class="space-top-md space-bottom-sm">{{ props.title }}</h1>

        <div class="search form-item">
            <input id="search" v-model="search" type="text" :placeholder="t('search_resort')" />
            <i class="fas fa-magnifying-glass"></i>
        </div>

        <div v-for="destination in filteredDestinations" :key="destination.id" class="park_overview space-bottom-md">
            <h2 class="park_overview--title">{{ destination.name }}</h2>

            <ParkItem v-for="park in destination.parks" :key="park.id" :park="park" />
        </div>
    </div>
</template>