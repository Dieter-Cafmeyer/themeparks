<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ParkItem from '../Components/Parks/ParkItem.vue';

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

const props = defineProps({
    parks: Array,
    isGuest: Boolean,
    title: String,
});

const search = ref('');

const filteredParks = computed(() => {
    console.log(search.value);
    
    if (!search.value.trim()) {
        return props.parks
    }

    const term = search.value.toLowerCase()

    return props.parks.filter(park =>
        park.name.toLowerCase().includes(term)
    )
})
</script>

<template>
    <Head :title="` | ${props.title}`" />

    <div class="container">
        <div class="favorites-page">
            <h1 class="space-top-md space-bottom-sm">{{ props.title }}</h1>

            <div class="search form-item">
                <input id="search" v-model="search" type="text" :placeholder="t('search_resort')" />
                <i class="fas fa-magnifying-glass"></i>
            </div>


            <!-- Guest view -->
            <div v-if="isGuest" class="bg-gray-100 p-6 rounded">
            <p class="mb-4">
                Je bent niet ingelogd.
            </p>

            <Link 
                :href="route('register')" 
                class="text-blue-600 underline"
            >
                Maak een account om parken toe te voegen aan je favorieten.
            </Link>
            </div>

            <!-- Logged in but no favorites -->
            <div v-else-if="parks.length === 0">
            <p>Je hebt nog geen favoriete parken toegevoegd.</p>
            </div>

            <!-- Favorites list -->
            <div v-else class="park_overview space-bottom-md">
                <ParkItem v-for="park in filteredParks" :key="park.id" :park="park" />
            </div>

        </div>
    </div>
</template>

