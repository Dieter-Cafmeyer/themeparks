<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';


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
  firstname: user.firstname || '',
  email: user.email || '',
  language: user.language || '',
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
          <img :src="form.preview" alt="">
        </div>

        <label class="upload-file" for="profile_picture">
          <i class="fas fa-upload"></i> <span v-if="form.preview != '/storage/null'">{{ t('change_profile_picture') }}</span>
          <span v-else>{{ t('upload_profile_picture') }}</span>
        </label>
        <input type="file" id="profile_picture" @change="changePicture" hidden>
        <small class="message-error" v-if="form.errors.profile_picture">{{ form.errors.profile_picture }}</small>
      </div>

      <TextInput :name="t('name')" v-model="form.name" :message="form.errors.name" />
      <TextInput :name="t('firstname')" v-model="form.firstname" :message="form.errors.firstname" />
      <TextInput :name="t('email')" type="email" v-model="form.email" :message="form.errors.email" />

      <div class="form-item">
        <label for="language">{{ t('language') }}</label>
        <LanguageSwitcher />
      </div>

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

      <div class="form-actions">
        <button class="button" :disabled="form.processing"><i class="fas fa-save"></i> {{ t('save_my_account') }}</button>
      </div>
    </form>
  </div>
</template>