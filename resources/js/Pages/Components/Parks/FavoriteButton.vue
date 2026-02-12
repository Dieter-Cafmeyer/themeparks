<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    parkId: {
        type: Number,
        required: true,
    },
    isFavorited: {
        type: Boolean,
        required: true,
    }
})

const favorited = ref(props.isFavorited)

watch(() => props.isFavorited, (val) => {
    favorited.value = val
})

const toggle = () => {
    favorited.value = !favorited.value

    router.post(route('parks.favorite', props.parkId), {}, {
        preserveScroll: true,
        onError: () => {
            favorited.value = !favorited.value
        }
    })
}
</script>

<template>
    <button type="button" @click.stop="toggle" :class="['park_item--favorite', favorited ? 'active' : '']">
        <i :class="favorited ? 'fas fa-star' : 'far fa-star'"></i>
    </button>
</template>