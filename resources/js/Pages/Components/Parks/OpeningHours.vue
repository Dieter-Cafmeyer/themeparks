<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

import axios from 'axios'
import WeatherDetail from './WeatherDetail.vue'

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
const weather = ref({})
const loading = ref(false)
const error = ref(null)
const selectedMonth = ref(new Date().toISOString().slice(0, 7)) // "YYYY-MM"
const selectedWeather = ref(null)
const selectedWeatherDate = ref(null)

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
        const [scheduleResponse, weatherResponse] = await Promise.all([
            axios.get(`/parks/${props.park.internal_id}/schedule/${year}/${month}`),
            axios.get(`/parks/${props.park.internal_id}/weather/${year}/${month}`)
        ]);
        
        schedule.value = scheduleResponse.data.schedule ?? []
        
        // Map weather data by date
        if (weatherResponse.data?.daily?.time) {
            const weatherMap = {}
            weatherResponse.data.daily.time.forEach((date, index) => {
                weatherMap[date] = {
                    temperature_2m_max: weatherResponse.data.daily.temperature_2m_max[index],
                    temperature_2m_min: weatherResponse.data.daily.temperature_2m_min[index],
                    weathercode: weatherResponse.data.daily.weathercode[index],
                    precipitation_sum: weatherResponse.data.daily.precipitation_sum[index],
                    windspeed_10m_max: weatherResponse.data.daily.windspeed_10m_max[index],
                }
            })
            weather.value = weatherMap
        }
    } catch (e) {
        error.value = 'Failed to load schedule'
        schedule.value = []
        weather.value = {}
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
   Weather helpers
================================ */
const weatherCodeMap = {
    0: { icon: 'fa-sun', color: '#FFD700' },
    1: { icon: 'fa-sun', color: '#FFD700' },
    2: { icon: 'fa-cloud-sun', color: '#87CEEB' },
    3: { icon: 'fa-cloud', color: '#B0C4DE' },
    45: { icon: 'fa-smog', color: '#DCDCDC' },
    48: { icon: 'fa-smog', color: '#DCDCDC' },
    51: { icon: 'fa-cloud-rain', color: '#4682B4' },
    53: { icon: 'fa-cloud-rain', color: '#4682B4' },
    55: { icon: 'fa-cloud-rain', color: '#4682B4' },
    61: { icon: 'fa-cloud-showers-heavy', color: '#1E90FF' },
    63: { icon: 'fa-cloud-showers-heavy', color: '#1E90FF' },
    65: { icon: 'fa-cloud-showers-heavy', color: '#1E90FF' },
    71: { icon: 'fa-snowflake', color: '#E0F8FF' },
    73: { icon: 'fa-snowflake', color: '#E0F8FF' },
    75: { icon: 'fa-snowflake', color: '#E0F8FF' },
    77: { icon: 'fa-snowflake', color: '#E0F8FF' },
    80: { icon: 'fa-cloud-rain', color: '#4682B4' },
    81: { icon: 'fa-cloud-showers-heavy', color: '#1E90FF' },
    82: { icon: 'fa-cloud-showers-heavy', color: '#00008B' },
    85: { icon: 'fa-snowflake', color: '#E0F8FF' },
    86: { icon: 'fa-snowflake', color: '#E0F8FF' },
    95: { icon: 'fa-bolt', color: '#FFD700' },
    96: { icon: 'fa-bolt', color: '#FFD700' },
    99: { icon: 'fa-bolt', color: '#FFD700' },
}

function getWeatherIcon(code) {
    return weatherCodeMap[code] || weatherCodeMap[0]
}

function showWeatherDetail(date) {
    if (weather.value[date]) {
        selectedWeather.value = weather.value[date]
        selectedWeatherDate.value = date
    }
}

function closeWeatherDetail() {
    selectedWeather.value = null
    selectedWeatherDate.value = null
}

/* ===============================
   Lifecycle
================================ */
onMounted(loadSchedule)
watch(selectedMonth, loadSchedule)
</script>

<template>
    <Transition name="modal-fade" appear>
        <div class="openinghours">
            <Transition name="modal-content" appear>
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

                    <TransitionGroup v-else-if="groupedSchedule.length" name="schedule-list" tag="div" class="openinghours_list">
                        <div v-for="(day, index) in groupedSchedule" :key="day.date" 
                            class="openinghours_day" 
                            :class="{
                                'is-today': day.date === todayString,
                                'is-open-now': day.date === todayString && isOpenNow
                            }"
                            :style="{ '--schedule-delay': index }">

                            <div class="openinghours_day--header">
                                <div class="openinghours_day--date">
                                    <span class="day">{{ formatDayMonth(day.date).day }}</span>
                                    <span class="month">{{ formatDayMonth(day.date).month }}</span>
                                </div>

                                <div>
                                    <span v-if="day.date === todayString && isOpenNow" class="openinghours_day--status">
                                        {{ t('open_now') }}
                                    </span>
                                </div>

                                <div class="openinghours_day--mini" v-if="weather[day.date]">
                                <div v-if="weather[day.date]" 
                                    class="openinghours_day--weather"
                                    @click="showWeatherDetail(day.date)"
                                    :title="t('view_weather_details') ?? 'View weather details'">
                                    <i class="fa-solid" 
                                        :class="getWeatherIcon(weather[day.date].weathercode).icon"
                                        :style="{ color: getWeatherIcon(weather[day.date].weathercode).color }"></i>
                                    <span class="temperature">{{ Math.round(weather[day.date].temperature_2m_max) }}°</span>
                                </div>
                            </div>
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

                            <div class="openinghours_day--meta" v-if="weather[day.date]">
                                <div v-if="weather[day.date]" 
                                    class="openinghours_day--weather"
                                    @click="showWeatherDetail(day.date)"
                                    :title="t('view_weather_details') ?? 'View weather details'">
                                    <i class="fa-solid" 
                                        :class="getWeatherIcon(weather[day.date].weathercode).icon"
                                        :style="{ color: getWeatherIcon(weather[day.date].weathercode).color }"></i>
                                    <span class="temperature">{{ Math.round(weather[day.date].temperature_2m_max) }}°</span>
                                </div>
                            </div>
                        </div>
                    </TransitionGroup>

                    <div v-else>{{ t('no_schedule_available') }}</div>
                </div>
            </Transition>
            
            <WeatherDetail 
                v-if="selectedWeather" 
                :weather="selectedWeather"
                :date="selectedWeatherDate"
                @close="closeWeatherDetail" />
        </div>
    </Transition>
</template>

<style scoped>
/* Modal animations */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-content-enter-active {
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-content-leave-active {
    transition: all 0.3s ease;
}

.modal-content-enter-from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
}

.modal-content-leave-to {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
}

/* Schedule list animations */
.schedule-list-enter-active {
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    transition-delay: calc(var(--schedule-delay, 0) * 0.05s);
}

.schedule-list-leave-active {
    transition: all 0.3s ease;
}

.schedule-list-enter-from {
    opacity: 0;
    transform: translateX(-20px) scale(0.95);
}

.schedule-list-leave-to {
    opacity: 0;
    transform: translateX(20px) scale(0.95);
}

.schedule-list-move {
    transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
</style>