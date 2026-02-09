<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

import DashboardLayout from '../../../Layouts/Dashboard.vue'
import Layout from '../../../Layouts/Layout.vue'
import SwitchToggle from '../../Components/Form/SwitchToggle.vue'

defineOptions({
    layout: [Layout, DashboardLayout]
})

const props = defineProps({
    destinations: Array,
    title: String,
    search_title: String,
})

const search = ref('')
const missingImages = ref({})

function imageError(parkId) {
    missingImages.value[parkId] = true
}

function toggleDestination(destination) {
    router.put(route('admin.destinations.update', { destination: destination.id }), {
        is_active: destination.is_active
    }, {
        preserveScroll: true,
    })
}

function togglePark(park) {
    router.put(route('admin.parks.update', { park: park.id }), {
        is_active: park.is_active
    }, {
        preserveScroll: true,
    })
}

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
    <h2>{{ title }}</h2>

    <div class="search form-item">
        <input id="search" v-model="search" type="text" :placeholder="search_title" />
        <i class="fas fa-magnifying-glass"></i>
    </div>

    <article v-for="destination in filteredDestinations" :key="destination.id" class="dashboard_park--item">
        <label class="dashboard_park--item__title">
            <h3>{{ destination.name }}</h3>
            <SwitchToggle v-model="destination.is_active" @update:modelValue="toggleDestination(destination)" />
        </label>

        <div v-for="park in destination.parks" :key="park.id" :class="[
            'dashboard_park--item__park',
            { 'dashboard_park--item__park--missing': missingImages[park.id] }
        ]">
            <div>
                <img :src="`/storage/parks/${park.api_id}.jpg`" :alt="park.name" @error="imageError(park.id)"
                    onerror="this.onerror=null;this.src='/storage/parks/placeholder_square.png';" />
            </div>

            <label>
                <p>
                    {{ park.name }} <br>
                    <span>{{ park.api_id }}</span>
                </p>
                <SwitchToggle v-model="park.is_active" @update:modelValue="togglePark(park)" />
            </label>
        </div>
    </article>
</template>