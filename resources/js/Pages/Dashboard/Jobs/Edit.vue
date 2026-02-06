<script setup>
import { usePage, router } from '@inertiajs/vue3';

import DashboardLayout from '../../../Layouts/Dashboard.vue';
import Layout from '../../../Layouts/Layout.vue';
import Form from "./Form.vue";

defineProps({
    job: Object,
    locales: Array,
    tags: Array,
});

// Layout
defineOptions({
  layout: [Layout, DashboardLayout]
});

const job = usePage().props.job;

// Job delete logics
const deleteJob = () => {
  if (confirm("Are you sure you want to delete this job?")) {
    router.delete(route("dashboard.jobs.delete", job.id));
  }
}


</script>

<template>
    <Link class="user_edit-back" :href="route('dashboard.jobs')"><i class="fas fa-angle-left"></i> Back</Link>

    <div class="user_edit-header">
        <h3>Edit job</h3>
        <button class="button" @click="deleteJob"><i class="fas fa-trash"></i> Delete</button>
    </div>

    <Form :job="job" :locales="locales" :tags="tags" />
</template>
