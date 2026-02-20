<script setup>
import { useForm, usePage } from '@inertiajs/vue3';

import DashboardLayout from '../../Layouts/Dashboard.vue'
import Layout from '../../Layouts/Layout.vue'
import LanguageSwitcher from '../Components/Form/LanguageSwitcher.vue';

import TextInput from '../Components/Form/TextInput.vue';

defineOptions({
  layout: [Layout, DashboardLayout]
});

const { props } = usePage();
const user = JSON.parse(JSON.stringify(props.user));
const t = (key) => props.translations?.[key] ?? key;

const form = useForm({
  profile_picture: null,
  name: user.name || '',
  email: user.email || '',
  password: '',
  password_confirmation: '',
  preview: '/storage/' + user.profile_picture || '',
});

// Profile Picture
const changePicture = (e) => {
  form.profile_picture = e.target.files[0];
  form.preview = URL.createObjectURL(e.target.files[0]);
};


// Update
const updateAccount = () => {
  if (!form.profile_picture) {
    delete form.profile_picture;
  }

  form.post(route("account.edit"));
};
</script>

<template>
  <div class="dashboard-background">
    <h3>{{ t('my_account') }}</h3>

    <form @submit.prevent="updateAccount">
      <div class="form-item upload-picture">
        <div class="preview" v-if="form.preview != '/storage/null'">
          <img :src="form.preview" onerror="this.onerror=null;this.src='/storage/users/placeholder.png';" alt="">
        </div>

        <label class="upload-file" for="profile_picture">
          <i class="fas fa-upload"></i> <span v-if="form.preview != '/storage/null'">{{ t('change_profile_picture')
            }}</span>
          <span v-else>{{ t('upload_profile_picture') }}</span>
        </label>
        <input type="file" id="profile_picture" @change="changePicture" hidden>
        <small class="message-error" v-if="form.errors.profile_picture">{{ form.errors.profile_picture }}</small>
      </div>

      <TextInput :name="t('name')" v-model="form.name" :message="form.errors.name" />
      <TextInput :name="t('email')" type="email" v-model="form.email" :message="form.errors.email" />
      <TextInput :name="t('new_password')" type="password" v-model="form.password" :message="form.errors.password" />
      <TextInput :name="t('confirm_password')" type="password" v-model="form.password_confirmation" />

      <div class="form-actions">
        <button class="button" :disabled="form.processing"><i class="fas fa-save"></i> {{ t('save_my_account')
          }}</button>
      </div>
    </form>
  </div>
</template>