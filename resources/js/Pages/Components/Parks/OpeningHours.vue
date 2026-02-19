<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

import axios from 'axios'

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

const props = defineProps({
    park: Object
})

// Emit an event to the parent when closing
const emit = defineEmits(['close'])

/* ===============================
   State
================================ */
const schedule = ref([])
const loading = ref(false)
const error = ref(null)
const selectedMonth = ref(new Date().toISOString().slice(0, 7)) // "YYYY-MM"

/* ===============================
   Helpers
================================ */
function getYearMonth() {
    const [year, month] = selectedMonth.value.split('-')
    return { year, month }
}

// Geeft YYYY-MM-DD string van vandaag volgens API offsets
function getTodayInTimezone(timezone) {
    const formatter = new Intl.DateTimeFormat('en-CA', {
        timeZone: timezone,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    })
    return formatter.format(new Date())
}

const todayString = computed(() => getTodayInTimezone(props.park.timezone))

function formatTime(isoString) {
    const date = new Date(isoString)
    return date.toTimeString().slice(0, 5) // HH:MM
}

function formatDayMonth(dateString) {
    const date = new Date(dateString)
    const day = date.getDate().toString().padStart(2, '0')
    const month = new Intl.DateTimeFormat('en-GB', { month: 'short' }).format(date)
    return { day, month }
}

/* ===============================
   API Call
================================ */
async function loadSchedule() {
    loading.value = true
    error.value = null
    const { year, month } = getYearMonth()

    try {
        const response = await axios.get(`/parks/${props.park.id}/schedule/${year}/${month}`)
        schedule.value = response.data.schedule ?? []
    } catch (e) {
        error.value = 'Failed to load schedule'
        schedule.value = []
    } finally {
        loading.value = false
    }
}

/* ===============================
   Month dropdown
================================ */
function generateNextMonths(count = 6) {
    const baseDate = new Date()
    const months = []

    for (let i = 0; i < count; i++) {
        const date = new Date(baseDate.getFullYear(), baseDate.getMonth() + i, 1)
        const year = date.getFullYear()
        const month = String(date.getMonth() + 1).padStart(2, '0')
        months.push({
            label: new Intl.DateTimeFormat('en-GB', { month: 'long', year: 'numeric' }).format(date),
            value: `${year}-${month}`
        })
    }
    return months
}
const availableMonths = computed(() => generateNextMonths(6))

/* ===============================
   Group schedule per day
================================ */
const groupedSchedule = computed(() => {
    const grouped = {}

    const today = new Date(todayString.value)

    for (const entry of schedule.value) {
        const entryDate = new Date(entry.date)
        // Skip past dates
        if (entryDate < today) continue

        if (!grouped[entry.date]) {
            grouped[entry.date] = { date: entry.date, entries: [] }
        }
        grouped[entry.date].entries.push(entry)
    }

    Object.values(grouped).forEach(day => {
        day.entries.sort((a, b) => new Date(a.openingTime) - new Date(b.openingTime))
    })

    return Object.values(grouped).sort((a, b) => a.date.localeCompare(b.date))
})

/* ===============================
   Open Now check
================================ */
const isOpenNow = computed(() => {
    const today = todayString.value
    const todayEntries = schedule.value.filter(e => e.date === today)
    if (!todayEntries.length) return false
    const now = new Date()
    return todayEntries.some(entry => now >= new Date(entry.openingTime) && now <= new Date(entry.closingTime))
})

/* ===============================
   Lifecycle
================================ */
onMounted(loadSchedule)
watch(selectedMonth, loadSchedule)
</script>

<template>
    <div class="openinghours">
        <div class="openinghours_wrapper">
            <div class="openinghours_header">
                <h3>{{ t('opening_hours') }}</h3>
                <!-- Close button -->
                <button class="openinghours_close" @click="$emit('close')">
                    &times;
                </button>
            </div>

            <!-- Month dropdown -->
            <div class="openinghours_control form-item">
                <div class="select-wrapper">
                    <select v-model="selectedMonth" class="month-picker">
                        <option v-for="month in availableMonths" :key="month.value" :value="month.value">
                            {{ month.label }}
                        </option>
                    </select>
                </div>
            </div>

            <div v-if="loading" class="loading"><i class="fa-solid fa-spinner"></i></div>
            <div v-else-if="error">{{ error }}</div>

            <div v-else-if="groupedSchedule.length" class="openinghours_list">
                <div v-for="day in groupedSchedule" :key="day.date" class="openinghours_day" :class="{
                    'is-today': day.date === todayString,
                    'is-open-now': day.date === todayString && isOpenNow
                }">

                    <div class="openinghours_day--header">
                        <span class="day">{{ formatDayMonth(day.date).day }}</span>
                        <span class="month">{{ formatDayMonth(day.date).month }}</span>
                        <span v-if="day.date === todayString && isOpenNow" class="openinghours_day--status">
                            {{ t('open_now') }}
                        </span>
                    </div>

                    <div class="openinghours_day--entry">
                        <div v-for="entry in day.entries" :key="entry.type + entry.openingTime"
                            class="openinghours_day--entry-item">
                            <p v-if="entry.type === 'OPERATING'">
                                <span>{{ t('opening_hours') }}</span>
                                <span class="dots"></span>
                                <span>{{ formatTime(entry.openingTime) }} – {{ formatTime(entry.closingTime) }}</span>
                            </p>
                            <p v-else>
                                <span>{{ entry.description }}:</span>
                                <span class="dots"></span>
                                <span>{{ formatTime(entry.openingTime) }} – {{ formatTime(entry.closingTime) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else>{{ t('no_schedule_available') }}</div>
        </div>
    </div>
</template>