<script setup>
import DashboardLayout from '../../../Layouts/Dashboard.vue';
import Layout from '../../../Layouts/Layout.vue';

defineProps({
    jobs: Object,
});

// Layout
defineOptions({
  layout: [Layout, DashboardLayout]
});

</script>

<template>
    <div class="user_edit-header">
        <h3>Jobs</h3>
        <Link class="button" :href="route('dashboard.jobs.create')">Create Job</Link>
    </div>

    <div class="user_list">
        <article class="user_list-item" v-for="job in jobs.data" :key="job.id">
        <Link :href="route('dashboard.jobs.edit', job.id)">
            <div class="user_list-item__info">
            <p>{{ job.translations[0]?.title }}</p>

            <p v-if="job.active" class="job-published">
                <i class="fa-regular fa-circle-check"></i>Published
            </p>
            <p v-else="job.active" class="job-unpublished">
                <i class="fa-regular fa-circle-xmark"></i> Unpublished
            </p>
            </div>
            <button class="user_list-item__action"><i class="fas fa-pen"></i></button>
        </Link>
        </article>
    </div>


</template>
