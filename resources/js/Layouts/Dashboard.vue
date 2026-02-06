<script setup>
import SideBar from '../Pages/Components/SideBar.vue';
import { usePage } from "@inertiajs/vue3";

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
            

            <h1 class="space-top-md space-bottom-md">Hi, {{ $page.props.auth.user.firstname }}</h1>

            <div class="dashboard-wrapper">
                <SideBar />

                <section>
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>