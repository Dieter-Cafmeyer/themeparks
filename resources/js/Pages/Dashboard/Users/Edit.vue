<script setup>
import { useForm, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import DashboardLayout from '../../../Layouts/Dashboard.vue';
import Layout from '../../../Layouts/Layout.vue';

import TextInput from '../../Components/Form/TextInput.vue';

// Layout
defineOptions({
  layout: [Layout, DashboardLayout]
});

// User update logics
const user = usePage().props.user;

const form = useForm({
  profile_picture: null,
  name: user.name || '',
  firstname: user.firstname || '',
  email: user.email || '',
  language: user.language || '',
  is_admin: null,
  preview: '/storage/' + user.profile_picture || '',
  tags: user.tags || [],
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

// Tags
const newTag = ref("");
const addTag = (e) => {
  if (!newTag.value.trim()) return;

  const tag = newTag.value.trim();

  if (!form.tags.includes(tag)) {
    form.tags.push(tag);
  }

  newTag.value = "";
};

const removeTag = (tag) => {
  form.tags = form.tags.filter(t => t !== tag);
};

</script>

<template class="user_edit">
  <div class="dashboard-background">
    <Link class="user_edit-back" :href="route('dashboard.users')"><i class="fas fa-angle-left"></i> {{ t('back') }}</Link>

    <div class="user_edit-header">
      <h3>{{ t('edit_user') }}</h3>
      <button class="button" @click="deleteUser"><i class="fas fa-trash"></i> {{ t('delete') }}</button>
    </div>
    <form class="user_edit-form" @submit.prevent="updateUser">
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

      <TextInput name="Name" v-model="form.name" :message="form.errors.name" />
      <TextInput name="Firstname" v-model="form.firstname" :message="form.errors.firstname" />
      <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" />

      <div class="form-item">
        <label for="language">{{ t('language') }}</label>
        <div class="select-wrapper">
          <select name="language" id="language" v-model="form.language">
            <option value="nl">{{ t('dutch') }}</option>
            <option value="en">{{ t('english') }}</option>
            <option value="fr">{{ t('french') }}</option>
          </select>
        </div>
      </div>

      <label class="checkbox-wrapper">
        <input type="checkbox" v-model="form.is_admin" :checked="form.is_admin">
        Admin
      </label>

      <div class="form-actions">
        <button class="button" :disabled="form.processing"><i class="fas fa-save"></i> {{ t('save_changes') }}</button>
      </div>
    </form>
  </div>
</template>
