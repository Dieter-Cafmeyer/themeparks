<script setup>
import { usePage } from '@inertiajs/vue3'

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

const props = defineProps({
    shows: Array
});

function formatDate(date) {
    if (!date) return ''
    return date.substring(11, 16)
}

function isPast(showtime) {
    if (!showtime?.endTime) return false
    return new Date(showtime.endTime) < new Date()
}
</script>

<template>
    <h2 class="space-top-md space-bottom-md">{{ t('shows_today') }}</h2>

    <div class="shows">
        <article v-for="item in shows" :key="item.id" class="show_item">
            <h3>{{ item.name }}</h3>

            <div v-if="item.status === 'CLOSED'" class="show_item--times">
                <i class="fa-regular fa-circle-xmark"></i>
                <span class="no-show">{{ t('no_show') }}</span>
            </div>

            <div v-else class="show_item--times">
                <div v-for="showtime in item.showtimes" :key="showtime.id" :class="{ 'showtime-past': isPast(showtime) }">
                    <i class="fa-regular fa-clock"></i> <span>{{ formatDate(showtime.startTime) }}</span>
                </div>
            </div>
        </article>
    </div>
</template>
