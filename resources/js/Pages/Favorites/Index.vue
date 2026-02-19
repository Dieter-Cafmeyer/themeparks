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
    <Head :title="` | ${t('favorites')}`" />

    <div class="container">
        <div class="favorites-page">
            <h1 class="space-top-md space-bottom-md">{{ t('favorites') }}</h1>

            <div  v-if="!isGuest && parks.length > 0" class="search form-item">
                <input id="search" v-model="search" type="text" :placeholder="t('search_resort')" />
                <i class="fas fa-magnifying-glass"></i>
            </div>

            <!-- Guest view -->
            <div class="favorites-page__empty-wrapper" v-if="isGuest">
                <div class="favorites-page__empty space-top-md">
                    <h2>{{ t('not_logged_in') }}</h2>
                    <p><Link :href="route('login')">{{ t('log_in') }}</Link> 
                    {{ t('or') }} 
                    <Link :href="route('register')">{{ t('create_account') }}</Link> 
                    {{ t('to_add_favorites') }}</p>
                </div>
            </div>

            <!-- Logged in but no favorites -->
            <div class="favorites-page__empty-wrapper" v-else-if="parks.length === 0">
                <div class="favorites-page__empty space-top-md">
                    <h2 class="space-bottom-md">{{t('no_favorites')}}</h2>
                    <Link class="button" :href="route('parks')">{{ t('browse_parks') }}</Link>
                </div>
            </div>

            <!-- Favorites list -->
            <div v-else class="park_overview space-bottom-md">
                <ParkItem v-for="park in filteredParks" :key="park.id" :park="park" />
            </div>

        </div>
    </div>
</template>

