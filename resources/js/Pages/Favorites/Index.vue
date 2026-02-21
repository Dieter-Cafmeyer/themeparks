<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ParkItem from '../Components/Parks/ParkItem.vue';
import ParksMap from '../Components/Parks/ParksMap.vue'

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;
const view = ref('list')

const props = defineProps({
    parks: Array,
    isGuest: Boolean,
    title: String,
});

const search = ref('');

const filteredParks = computed(() => {
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

    <Head :title="` | ${t('favorites')}`" />

    <div class="container">
        <div class="favorites-page">
            <div class="park_header space-top-md space-bottom-md">
                <h1>{{ t('favorites') }}</h1>

                <div class="view-toggle">
                    <button :class="{ active: view === 'list' }" @click="view = 'list'">
                        <i class="fas fa-list"></i>
                    </button>

                    <button :class="{ active: view === 'map' }" @click="view = 'map'">
                        <i class="far fa-map"></i>
                    </button>
                </div>
            </div>

            <!-- Guest view -->
            <div class="favorites-page__empty-wrapper" v-if="isGuest">
                <div class="favorites-page__empty space-top-md">
                    <h2>{{ t('not_logged_in') }}</h2>
                    <p>
                        <Link :href="route('login')">{{ t('log_in') }}</Link>
                        {{ t('or') }}
                        <Link :href="route('register')">{{ t('create_account') }}</Link>
                        {{ t('to_add_favorites') }}
                    </p>
                </div>
            </div>

            <!-- Logged in but no favorites -->
            <div class="favorites-page__empty-wrapper" v-else-if="parks.length === 0">
                <div class="favorites-page__empty space-top-md">
                    <h2 class="space-bottom-md">{{ t('no_favorites') }}</h2>
                    <Link class="button" :href="route('parks')">{{ t('browse_parks') }}</Link>
                </div>
            </div>

            <!-- Logged in with favorites, but filtered without results -->
            <div v-else-if="parks.length > 0 && filteredParks.length === 0">
                <p>Geen resultaten gevonden, voeg
                    <Link :href="route('parks')">{{ t('parks') }}</Link> toe of pas je zoekopdracht aan.
                </p>
            </div>

            <!-- Favorites list -->
            <div v-else>
                <div v-if="view === 'list'" class="park_overview favorites-page_list">
                    <div v-if="!isGuest && parks.length > 0" class="park_search search form-item">
                        <input id="search" v-model="search" type="text" :placeholder="t('search_resort')" />
                        <i class="fas fa-magnifying-glass"></i>
                    </div>

                    <ParkItem v-for="park in filteredParks" :key="park.id" :park="park" />
                </div>
                <div v-else-if="view === 'map'" class="favorites-page_map">
                    <ParksMap :parks="props.parks" />
                </div>
            </div>
        </div>
    </div>
</template>
