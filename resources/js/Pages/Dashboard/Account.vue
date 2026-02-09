<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import DashboardLayout from '../../Layouts/Dashboard.vue'
import Layout from '../../Layouts/Layout.vue'

import TextInput from '../Components/Form/TextInput.vue';

defineOptions({
  layout: [Layout, DashboardLayout]
});

const { props } = usePage();
const user = JSON.parse(JSON.stringify(props.user));

const form = useForm({
  profile_picture: null,
  name: user.name || '',
  firstname: user.firstname || '',
  email: user.email || '',
  language: user.language || '',
  preview: '/storage/' + user.profile_picture || '',
  tags: user.tags || [],
});

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
    <h3>My Account</h3>

    <form @submit.prevent="updateAccount">
      <div class="form-item upload-picture">
        <div class="preview" v-if="form.preview != '/storage/null'">
          <img :src="form.preview" alt="">
        </div>

        <label class="upload-file" for="profile_picture">
          <i class="fas fa-upload"></i> <span v-if="form.preview != '/storage/null'">Change your profile
            picture</span><span v-else>Upload a profile picture</span>
        </label>
        <input type="file" id="profile_picture" @change="changePicture" hidden>
        <small class="message-error" v-if="form.errors.profile_picture">{{ form.errors.profile_picture }}</small>
      </div>

      <TextInput name="Name" v-model="form.name" :message="form.errors.name" />
      <TextInput name="Firstname" v-model="form.firstname" :message="form.errors.firstname" />
      <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" />

      <div class="form-item">
        <label for="tags">Tags</label>

        <div class="tags-wrapper">
          <span class="tag-pill" v-for="tag in form.tags" :key="tag">
            {{ tag }}
            <button type="button" @click="removeTag(tag)" class="remove"><i class="fas fa-xmark"></i></button>
          </span>
        </div>

        <div class="tags-add">
          <input type="text" v-model="newTag" placeholder="Add a tag" class="tag-input" />
          <button type="button" class="button" @click="addTag"><i class="fas fa-plus"></i></button>
        </div>

        <small class="message-error" v-if="form.errors.tags">
          {{ form.errors.tags }}
        </small>
      </div>

      <div class="form-item">
        <label for="language">Language</label>
        <div class="select-wrapper">
          <select name="language" id="language" v-model="form.language">
            <option value="nl">Dutch</option>
            <option value="en">English</option>
            <option value="fr">French</option>
          </select>
        </div>
      </div>

      <div class="form-actions">
        <button class="button" :disabled="form.processing"><i class="fas fa-save"></i> Save my account</button>
      </div>
    </form>
  </div>
</template>