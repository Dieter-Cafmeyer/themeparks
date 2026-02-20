<script setup>
import { useForm, usePage, router } from '@inertiajs/vue3';

import DashboardLayout from '../../../Layouts/Dashboard.vue';
import Layout from '../../../Layouts/Layout.vue';
import TextInput from '../../Components/Form/TextInput.vue';
import SwitchToggle from '../../Components/Form/SwitchToggle.vue'
import ParkItem from '../../Components/Parks/ParkItem.vue';

// Layout
defineOptions({
  layout: [Layout, DashboardLayout]
});

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

// User update logics
const user = pageProps.user;

const form = useForm({
  profile_picture: null,
  name: user.name || '',
  email: user.email || '',
  language: user.language || '',
  is_admin: user.is_admin || false,
  preview: '/storage/' + user.profile_picture || '',
});

const updateUser = () => {
  if (!form.profile_picture) {
    delete form.profile_picture;
  }

  form.post(route("dashboard.users.update", user.id));
};

// User delete logics
const deleteUser = () => {
  if (confirm("Are you sure you want to delete this user?")) {
    router.delete(route("dashboard.users.delete", user.id));
  }
}

// Profile Picture
const changePicture = (e) => {
  form.profile_picture = e.target.files[0];
  form.preview = URL.createObjectURL(e.target.files[0]);
};

</script>

<template class="user_edit">

  <div class="dashboard-background">
    <div class="user_edit-header">
      <Link class="button" :href="route('dashboard.users')"><i class="fas fa-angle-left"></i> {{ t('back') }}</Link>
      <button class="button" @click="deleteUser"><i class="fas fa-trash"></i> {{ t('delete') }}</button>
    </div>
    <form class="user_edit-form" @submit.prevent="updateUser">
      <h2 class="space-top-md" style="text-align: center;width: 100%;">{{ user.name }}</h2>
      <div class="form-item upload-picture">

        <div class="preview" v-if="form.preview != '/storage/null'">
          <img :src="form.preview" alt="">
        </div>

        <label class="upload-file" for="profile_picture">
          <i class="fas fa-upload"></i> <span v-if="form.preview != '/storage/null'">{{ t('change_profile_picture') }}</span><span
            v-else>{{ t('upload_profile_picture') }}</span>
        </label>
        <input type="file" id="profile_picture" @change="changePicture" hidden>
        <small class="message-error" v-if="form.errors.profile_picture">{{ form.errors.profile_picture }}</small>
      </div>

      <TextInput :name="t('name')" v-model="form.name" :message="form.errors.name" />
      <TextInput :name="t('email')" type="email" v-model="form.email" :message="form.errors.email" />

      <label class="checkbox-wrapper">
        <SwitchToggle v-model="form.is_admin" />
        Admin
      </label>

      <div class="form-actions">
        <button class="button" :disabled="form.processing"><i class="fas fa-save"></i> {{ t('save_changes') }}</button>
      </div>
    </form>
  </div>

  <div class="park_overview space-bottom-md">
    <h3 v-if="user.favorite_parks.length > 0" class="space-top-md space-bottom-md">{{ t('favorites') }}</h3>
    <ParkItem v-for="park in user.favorite_parks" :key="park.id" :park="park" />
  </div>
</template>
