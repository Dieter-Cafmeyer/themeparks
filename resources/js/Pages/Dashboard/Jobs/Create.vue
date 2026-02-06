<script setup>
import DashboardLayout from '../../../Layouts/Dashboard.vue';
import Layout from '../../../Layouts/Layout.vue';
import Form from "./Form.vue";

defineProps({
    locales: Array,
    tags: Array,
});

// Layout
defineOptions({
  layout: [Layout, DashboardLayout]
});

</script>

<template>
    <Link class="user_edit-back" :href="route('dashboard.jobs')"><i class="fas fa-angle-left"></i> Back</Link>

    <div class="user_edit-header">
        <h3>Create new job</h3>
    </div>

    <Form :locales="locales" :tags="tags" />
</template>
