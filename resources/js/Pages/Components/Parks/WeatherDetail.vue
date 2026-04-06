<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

const props = defineProps({
    weather: Object,
    date: String
})

const emit = defineEmits(['close'])

// Weather code to icon and description mapping
// Based on WMO Weather interpretation codes
const weatherCodeMap = {
    0: { icon: 'fa-sun', description: 'weather_clear_sky', color: '#FFD700' },
    1: { icon: 'fa-sun', description: 'weather_mainly_clear', color: '#FFD700' },
    2: { icon: 'fa-cloud-sun', description: 'weather_partly_cloudy', color: '#87CEEB' },
    3: { icon: 'fa-cloud', description: 'weather_overcast', color: '#B0C4DE' },
    45: { icon: 'fa-smog', description: 'weather_foggy', color: '#DCDCDC' },
    48: { icon: 'fa-smog', description: 'weather_rime_fog', color: '#DCDCDC' },
    51: { icon: 'fa-cloud-rain', description: 'weather_light_drizzle', color: '#4682B4' },
    53: { icon: 'fa-cloud-rain', description: 'weather_moderate_drizzle', color: '#4682B4' },
    55: { icon: 'fa-cloud-rain', description: 'weather_dense_drizzle', color: '#4682B4' },
    61: { icon: 'fa-cloud-showers-heavy', description: 'weather_slight_rain', color: '#1E90FF' },
    63: { icon: 'fa-cloud-showers-heavy', description: 'weather_moderate_rain', color: '#1E90FF' },
    65: { icon: 'fa-cloud-showers-heavy', description: 'weather_heavy_rain', color: '#1E90FF' },
    71: { icon: 'fa-snowflake', description: 'weather_slight_snow', color: '#E0F8FF' },
    73: { icon: 'fa-snowflake', description: 'weather_moderate_snow', color: '#E0F8FF' },
    75: { icon: 'fa-snowflake', description: 'weather_heavy_snow', color: '#E0F8FF' },
    77: { icon: 'fa-snowflake', description: 'weather_snow_grains', color: '#E0F8FF' },
    80: { icon: 'fa-cloud-rain', description: 'weather_slight_rain_showers', color: '#4682B4' },
    81: { icon: 'fa-cloud-showers-heavy', description: 'weather_moderate_rain_showers', color: '#1E90FF' },
    82: { icon: 'fa-cloud-showers-heavy', description: 'weather_violent_rain_showers', color: '#00008B' },
    85: { icon: 'fa-snowflake', description: 'weather_slight_snow_showers', color: '#E0F8FF' },
    86: { icon: 'fa-snowflake', description: 'weather_heavy_snow_showers', color: '#E0F8FF' },
    95: { icon: 'fa-bolt', description: 'weather_thunderstorm', color: '#FFD700' },
    96: { icon: 'fa-bolt', description: 'weather_thunderstorm_slight_hail', color: '#FFD700' },
    99: { icon: 'fa-bolt', description: 'weather_thunderstorm_heavy_hail', color: '#FFD700' },
}

const weatherInfo = computed(() => {
    if (!props.weather) return null
    const code = props.weather.weathercode ?? 0
    return weatherCodeMap[code] || weatherCodeMap[0]
})

function formatDate(dateString) {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('nl-BE', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    }).format(date)
}
</script>

<template>
    <Transition name="modal-fade" appear>
        <div class="weather-detail" @click.self="$emit('close')">
            <Transition name="modal-content" appear>
                <div class="weather-detail_wrapper">
                    <div class="weather-detail_header">
                        <h3>{{ t('weather_forecast') ?? 'Weather Forecast' }}</h3>
                        <button class="weather-detail_close" @click="$emit('close')">
                            &times;
                        </button>
                    </div>

                    <div v-if="weather" class="weather-detail_content">
                        <div class="weather-detail_date">
                            {{ formatDate(date) }}
                        </div>

                        <div class="weather-detail_icon" :style="{ color: weatherInfo.color }">
                            <i class="fa-solid" :class="weatherInfo.icon"></i>
                        </div>

                        <div class="weather-detail_description">
                            {{ t(weatherInfo.description) }}
                        </div>

                        <div class="weather-detail_stats">
                            <div class="weather-stat">
                                <i class="fa-solid fa-temperature-high"></i>
                                <div class="weather-stat_label">{{ t('max_temp') ?? 'Max' }}</div>
                                <div class="weather-stat_value">{{ Math.round(weather.temperature_2m_max) }}°C</div>
                            </div>

                            <div class="weather-stat">
                                <i class="fa-solid fa-temperature-low"></i>
                                <div class="weather-stat_label">{{ t('min_temp') ?? 'Min' }}</div>
                                <div class="weather-stat_value">{{ Math.round(weather.temperature_2m_min) }}°C</div>
                            </div>

                            <div class="weather-stat">
                                <i class="fa-solid fa-droplet"></i>
                                <div class="weather-stat_label">{{ t('precipitation') ?? 'Precipitation' }}</div>
                                <div class="weather-stat_value">{{ weather.precipitation_sum }} mm</div>
                            </div>

                            <div class="weather-stat">
                                <i class="fa-solid fa-wind"></i>
                                <div class="weather-stat_label">{{ t('wind_speed') ?? 'Wind' }}</div>
                                <div class="weather-stat_value">{{ Math.round(weather.windspeed_10m_max) }} km/h</div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>
