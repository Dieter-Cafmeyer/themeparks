<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    attractions_title: String,
    shows_title: String,
    map_title: String,
    showsToggle: Boolean,
})

const active = ref('attractions')
const emit = defineEmits(['update:activeTab'])

watch(active, (newVal) => {
    emit('update:activeTab', newVal)
})

function setActive(tab) {
    active.value = tab
}
</script>

<template>
    <div class="toggle">
        <div :class="{ active: active === 'attractions' }" @click="setActive('attractions')">
            {{ props.attractions_title }}
        </div>

        <div v-if="props.shows_title && props.showsToggle" :class="{ active: active === 'shows' }" @click="setActive('shows')">
            {{ props.shows_title }}
        </div>

        <div v-if="props.map_title" :class="{ active: active === 'map' }" @click="setActive('map')">
            {{ props.map_title }}
        </div>
    </div>
</template>