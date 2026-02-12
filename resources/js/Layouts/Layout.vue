<script setup>
import { ref, nextTick, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const { props: pageProps } = usePage()
const t = (key) => pageProps.translations?.[key] ?? key

const navRef = ref(null)
const activeKey = ref('parks')

const indicatorStyle = ref({
    left: '0px',
    width: '0px'
})

function isActive(key) {
    return activeKey.value === key
}

function setActive(key) {
    activeKey.value = key
    nextTick(updateIndicator)
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
})
</script>

<template>
    <header class="header">
        <div class="container">

            <nav class="main_navigation" ref="navRef">
                <ul>
                    <div class="nav_indicator" :style="indicatorStyle"></div>

                    <li :class="{ active: isActive('parks') }" @click="setActive('parks')">
                        <Link :href="route('parks')">

                        <img src="../../assets/coaster.svg" alt="">
                        <label>Parks</label>
                        </Link>
                    </li>

                    <li :class="{ active: isActive('favorites') }" @click="setActive('favorites')">
                        <Link :href="route('favorites')">
                        <i class="far fa-star"></i>
                        <label>Favorites</label>
                        </Link>
                    </li>

                    <li v-if="!$page.props.auth.user" :class="{ active: isActive('login') }"
                        @click="setActive('login')">
                        <Link :href="route('login')">
                        <i class="far fa-user"></i>
                        <label>Account</label>
                        </Link>
                    </li>

                    <li v-else :class="{ active: isActive('dashboard') }" @click="setActive('dashboard')">
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