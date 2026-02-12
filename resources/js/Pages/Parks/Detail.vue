<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

import Toggle from '../Components/Parks/Toggle.vue';
import FavoriteButton from '../Components/Parks/FavoriteButton.vue';
import Attractions from '../Components/Parks/Attractions.vue';
import Shows from '../Components/Parks/Shows.vue';
import Map from '../Components/Parks/Map.vue';

const props = defineProps({
    park: Object,
    shows: Array,
    map: Object,
    attractions: Object,
    title: String,
    attractions_title: String,
    shows_title: String,
    map_title: String,
    last_updated: String,
    seconds_ago: String,
    minute: String,
    minutes: String,
    ago: String,
});

const activeTab = ref('attractions');
const lastUpdated = ref(new Date());
const relativeTime = ref('');
let showsToggle = false;

function goBack() {
    window.history.back()
}

const currentComponent = computed(() => {
    if (activeTab.value === 'shows') return Shows
    if (activeTab.value === 'map') return Map
    return Attractions
})

const currentProps = computed(() => {
    if (activeTab.value === 'shows') return { shows: props.shows }
    if (activeTab.value === 'map') return { map: props.map }
    return { attractions: props.attractions }
})

function reloadData() {
    router.get(route('parks.detail', props.park.id), {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: () => {
            lastUpdated.value = new Date()
            updateRelativeTime()
        }
    })
}

function updateRelativeTime() {
    const diff = Math.floor((new Date() - lastUpdated.value) / 1000)

    if (diff < 60) {
        relativeTime.value = `${diff} ${props.seconds_ago}`
    } else {
        const minutes = Math.floor(diff / 60)
        relativeTime.value = `${minutes} ${minutes === 1 ? props.minute : props.minutes} ${props.ago}`
    }
}

let intervalId = null

onMounted(() => {
    if(props.shows && props.shows.length > 0) {
        showsToggle = true;
    }
    updateRelativeTime()
    intervalId = setInterval(updateRelativeTime, 1000)
})

onUnmounted(() => {
    clearInterval(intervalId)
})
</script>

<template>
    <div class="park_detail">
        <button class="park_detail--back glass-button bottomLeft" @click="goBack">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <button class="glass-button bottomRight" @click="reloadData">
            <i class="fa-solid fa-arrow-rotate-right"></i>
        </button>

        <FavoriteButton :park-id="props.park.internal_id" :is-favorited="props.park.is_favorited" class="glass-button topRight" />

        <div class="park_detail--image">
            <img :src="`/storage/parks/${props.park.id}.jpg`" :alt="props.park.name"
                onerror="this.onerror=null;this.src='/storage/parks/placeholder_square.png';" />

            <h1 class="park_detail--parkname">{{ title }}</h1>
        </div>

        <div class="park_detail--reload" @click="reloadData">
            <span v-if="lastUpdated">
                {{ last_updated }}: {{ relativeTime }}
            </span>
        </div>

        <div class="container">
            <Toggle :attractions_title="props.attractions_title" :shows_title="props.shows_title"
                :map_title="props.map_title" v-model:activeTab="activeTab" :showsToggle="showsToggle" />

            <component :is="currentComponent" v-bind="currentProps" />
        </div>
    </div>
</template>