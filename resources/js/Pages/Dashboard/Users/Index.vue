<script setup>
import { usePage } from "@inertiajs/vue3";

import DashboardLayout from '../../../Layouts/Dashboard.vue';
import Layout from '../../../Layouts/Layout.vue';

defineOptions({
  layout: [Layout, DashboardLayout]
});

const { props } = usePage();
const users = JSON.parse(JSON.stringify(props.users));

</script>

<template>
  <div class="dashboard-background">
    <h3>Users</h3>

    <div class="user_list">
      <article class="user_list-item" v-for="user in users" :key="user.id">
        <Link :href="route('dashboard.users.edit', user.id)">
        <div class="user_list-item__info">
          <p>{{ user.firstname }} {{ user.name }}</p>
          <p>{{ user.email }}</p>
        </div>
        <button class="user_list-item__action"><i class="fas fa-pen"></i></button>
        </Link>
      </article>
    </div>
  </div>
</template>
