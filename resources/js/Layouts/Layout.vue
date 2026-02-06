<script setup>
import LanguageSwitcher from '../Pages/Components/LanguageSwitcher.vue';
</script>

<template>
    <div>
        <header class="header">
            <div class="container">
                <Link :href="route('jobs')">
                    <img src="../../assets/logo.png" class="logo" alt="">
                </Link>

                <nav class="main_navigation">
                    <ul>
                        <li>
                            <Link :href="route('jobs')">Jobs</Link>
                        </li>
                        <li>
                            <Link :href="route('users')">Users</Link>
                        </li>

                        <li>
                            <LanguageSwitcher />
                        </li>

                        <li v-if="!$page.props.auth.user">
                            <Link :href="route('login')">Login</Link>
                        </li>
                        <li v-else>
                            <Link :href="route('dashboard')">
                                <div class="profile_picture" v-if="$page.props.auth.user.profile_picture"><img :src="'/storage/' + $page.props.auth.user.profile_picture"></div>
                                {{ $page.props.auth.user.firstname }}
                            </Link>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>