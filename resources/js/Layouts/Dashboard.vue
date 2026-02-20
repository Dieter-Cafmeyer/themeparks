<script setup>
import SideBar from '../Pages/Components/Dashboard/SideBar.vue';
import { usePage } from "@inertiajs/vue3";
import LanguageSwitcher from '../Pages/Components/Form/LanguageSwitcher.vue';

const page = usePage();
const isActive = (path) => page.url.startsWith(path);
</script>

<template>

    <Head title=" | Dashboard" />
    <div class="dashboard space-bottom-lg">
        <div class="container">
            <div class="message--success" v-if="$page.props.flash.success">
                {{ page.props.flash.success }}
            </div>

            <div class="dashboard-header">
                <h1 class="space-top-md space-bottom-md">Hi, {{ $page.props.auth.user.firstname }}</h1>
                <div>
                    <LanguageSwitcher />
                    <Link class="dashboard-header--logout glass-button" :href="route('logout')" method="post" as="button"><i class="fas fa-power-off"></i></Link>
                </div>
            </div>


            <div class="dashboard-wrapper">
                <SideBar />

                <section class="">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>