<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

const props = defineProps({
    attraction: Object
})

const showPopup = ref(false);
function tryOpenPopup() {
    // Alleen popup openen als de attractie OPERATING is
    if (props.attraction.status === 'OPERATING') {
        showPopup.value = true
    }
}

const queue = computed(() => props.attraction?.queue ?? {})
const standby = computed(() => queue.value?.STANDBY)
const singleRider = computed(() => queue.value?.SINGLE_RIDER)
const returnTime = computed(() => queue.value?.RETURN_TIME)
const paidReturnTime = computed(() => queue.value?.PAID_RETURN_TIME)
const boardingGroup = computed(() => queue.value?.BOARDING_GROUP)
const paidStandby = computed(() => queue.value?.PAID_STANDBY)

const statusClass = computed(() => {
    const el = props.attraction
    if (!el) return ''

    if (el.status === 'CLOSED') {
        return 'closed'
    }

    if (el.status === 'DOWN') {
        return 'down'
    }

    if (el.status === 'OPERATING') {
        const waitTime = el.queue?.STANDBY?.waitTime ?? 0

        if (waitTime <= 20) return 'open'
        if (waitTime <= 30) return 'openModerate'
        if (waitTime <= 40) return 'openBusy'
        if (waitTime <= 60) return 'openVeryBusy'
        return 'openSuperBusy'
    }

    return ''
});

const availableFeatures = computed(() => {
    const features = []

    if (queue.value.SINGLE_RIDER) {
        features.push({
            key: 'single_rider_available',
            class: 'badge_single',
            icon: 'fa-solid fa-person-walking-arrow-right'
        })
    }

    if (queue.value.RETURN_TIME?.state === 'AVAILABLE') {
        features.push({
            key: 'return_time_available',
            class: 'badge_return',
            icon: 'fa-solid fa-ticket'
        })
    }

    if (queue.value.PAID_RETURN_TIME?.state === 'AVAILABLE') {
        features.push({
            key: 'paid_return_available',
            class: 'badge_paid_return',
             icon: 'fa-solid fa-ticket'
        })
    }

    if (queue.value.BOARDING_GROUP?.allocationStatus === 'AVAILABLE') {
        features.push({
            key: 'boarding_group_available',
            class: 'badge_boarding',
            icon: 'fa-solid fa-users-line'
        })
    }

    if (queue.value.PAID_STANDBY) {
        features.push({
            key: 'paid_standby_available',
            class: 'badge_paid_standby',
            icon: 'fa-solid fa-coins'
        })
    }

    return features
})

function formatDate(date) {
    if (!date) return ''
    return new Date(date).toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    })
}
</script>

<template>
    <!-- Item -->
    <div class="attractions_item" @click="tryOpenPopup">
        <div :class="statusClass + ' attractions_item--content'">
            <h3>{{ attraction.name }}</h3>
            <div class="attractions_item--badges" v-if="availableFeatures.length > 0">
                <span v-for="feature in availableFeatures" :key="feature.key"
                    :class="'attraction_badge ' + feature.class">
                    <i :class="feature.icon"></i> {{ t(feature.key) }}
                </span>
            </div> 
        </div>

        <div :class="statusClass + ' attractions_item--time'">
            <div v-if="attraction.status === 'OPERATING'">
                <div v-if="attraction.queue?.STANDBY">
                    <p>{{ attraction.queue.STANDBY.waitTime }}</p>
                    <p>Min</p>
                </div>
                <div v-else-if="attraction.operatingHours">
                    {{ t('closes') }}
                    {{ formatDate(attraction.operatingHours[0].endTime) }}
                </div>
            </div>

            <div v-else-if="attraction.status === 'CLOSED'">
                <i class="fa-solid fa-lock"></i>
                <p class="specialstate">closed</p>
            </div>

            <div v-else>
                <i class="fa-solid fa-triangle-exclamation"></i>
                <p class="specialstate">down</p>
            </div>

        </div>
    </div>

    <!-- Popup -->
    <div v-if="showPopup" class="popup_overlay" @click.self="showPopup = false">
        <div class="popup">
            <button class="popup_close" @click="showPopup = false">×</button>

            <h2>{{ attraction.name }}</h2>

            <div class="popup_times"> 
                <div v-if="standby">
                    <h4> {{ t('standby') }} </h4>
                    <p>{{ standby.waitTime }}</p> 
                    <p>{{ t('minutes_short') }}</p>
                </div>

                <div v-if="singleRider">
                    <h4>{{ t('single_rider') }}</h4>
                    <p>{{ singleRider.waitTime }}</p> 
                    <p>{{ t('minutes_short') }}</p>
                </div>
            </div>

            <div v-if="returnTime" class="popup_return">
                <h4>{{ t('return_time_available') }}</h4>
                <p v-if="returnTime.state === 'AVAILABLE'">
                    <i class="fas fa-circle-check"></i>
                    {{ t('status') }}: {{ returnTime.state }}
                </p>
                <p v-if="returnTime.returnStart">
                    <i class="fa-regular fa-clock"></i>
                    {{ t('from') }} {{ formatDate(returnTime.returnStart) }}
                    {{ t('to') }} {{ formatDate(returnTime.returnEnd) }}
                </p>
            </div>

            <div v-if="paidReturnTime"  class="popup_return">
                <h4>{{ t('paid_return_available') }}</h4>
                <p>
                    <i class="fas fa-circle-check"></i>
                    {{ paidReturnTime.state }}
                </p>
                <p v-if="paidReturnTime.returnStart">
                    <i class="fa-regular fa-clock"></i>
                     {{ formatDate(paidReturnTime.returnStart) }}
                    {{ t('to') }} {{ formatDate(paidReturnTime.returnEnd) }}
                </p>
                <p v-if="paidReturnTime.price">
                    <i class="fa-solid fa-coins"></i>
                    {{ paidReturnTime.price.formatted }}
                </p>
            </div>

            <div v-if="boardingGroup">
                <h4>{{ t('boarding_group') }}</h4>
                <p>
                    {{ t('status') }}:
                    {{ boardingGroup.allocationStatus }}
                </p>
                <p>
                    {{ t('group') }}
                    {{ boardingGroup.currentGroupStart }}
                    –
                    {{ boardingGroup.currentGroupEnd }}
                </p>
                <p v-if="boardingGroup.nextAllocationTime">
                    {{ t('next_allocation') }}:
                    {{ formatDate(boardingGroup.nextAllocationTime) }}
                </p>
                <p v-if="boardingGroup.estimatedWait">
                    {{ t('estimated_wait') }}:
                    {{ boardingGroup.estimatedWait }}
                    {{ t('minutes') }}
                </p>
            </div>

            <div v-if="paidStandby">
                <h4>{{ t('paid_standby') }}</h4>
                <p>
                    {{ t('wait_time') }}:
                    {{ paidStandby.waitTime }}
                    {{ t('minutes') }}
                </p>
            </div>
        </div>
    </div>
</template>