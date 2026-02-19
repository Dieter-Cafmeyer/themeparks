<script setup>
import { ref, nextTick, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const t = (key) => page.props.translations?.[key] ?? key

const navRef = ref(null)

const indicatorStyle = ref({
    left: '0px',
    width: '0px'
})

function isActive(key) {
    const url = page.url

    switch (key) {
        case 'parks':
            return url === '/' || url.startsWith('/parks')

        case 'favorites':
            return url.startsWith('/favorites')

        case 'dashboard':
            return url.startsWith('/dashboard')

        case 'login':
            return url.startsWith('/login')

        default:
            return false
    }
}

function updateIndicator() {
    const nav = navRef.value
    if (!nav) return

    const activeEl = nav.querySelector('li.active')
    if (!activeEl) return

    indicatorStyle.value = {
        left: activeEl.offsetLeft + 'px',
        width: activeEl.offsetWidth + 'px'
    }
}

onMounted(() => {
    nextTick(updateIndicator)
    window.addEventListener('resize', updateIndicator)
})

watch(() => page.url, () => {
    nextTick(updateIndicator)
})
</script>

<template>
    <header class="header">
        <div class="container">

            <nav class="main_navigation" ref="navRef">
                <ul>
                    <div class="nav_indicator" :style="indicatorStyle"></div>

                    <li :class="{ active: isActive('parks') }">
                        <Link :href="route('parks')">
                        <img src="../../assets/coaster.svg" alt="">
                        <label>{{ t('parks') }}</label>
                        </Link>
                    </li>

                    <li :class="{ active: isActive('favorites') }">
                        <Link :href="route('favorites')">
                        <i class="far fa-star"></i>
                        <label>{{ t('favorites') }}</label>
                        </Link>
                    </li>

                    <li v-if="!$page.props.auth.user" :class="{ active: isActive('login') }">
                        <Link :href="route('login')">
                        <i class="far fa-user"></i>
                        <label>Account</label>
                        </Link>
                    </li>

                    <li v-else :class="{ active: isActive('dashboard') }">
                        <Link :href="route('dashboard')">
                        <i class="far fa-user"></i>
                        <label>Account</label>
                        </Link>
                    </li>

                </ul>
            </nav>
        </div>
    </header>

    <main>
        <slot />
    </main>
</template>