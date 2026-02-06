<script setup>
import { useForm, usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import DashboardLayout from '../../../Layouts/Dashboard.vue';
import Layout from '../../../Layouts/Layout.vue';

import TextInput from '../../Components/TextInput.vue';

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

<template>
    <Link class="user_edit-back" :href="route('dashboard.users')"><i class="fas fa-angle-left"></i> Back</Link>

    <div class="user_edit-header">
        <h3>Edit user</h3>
        <button class="button" @click="deleteUser"><i class="fas fa-trash"></i> Delete</button>
    </div>
    <form class="user_edit-form" @submit.prevent="updateUser">
        <div class="form-item upload-picture"> 
          
          <div class="preview" v-if="form.preview != '/storage/null'" >
            <img :src="form.preview" alt="">
          </div>

          <label class="upload-file" for="profile_picture">
            <i class="fas fa-upload"></i> <span v-if="form.preview != '/storage/null'">Change profile picture</span><span v-else>Upload a profile picture</span>
          </label>
          <input type="file" id="profile_picture" @change="changePicture" hidden>
          <small class="message-error" v-if="form.errors.profile_picture">{{ form.errors.profile_picture }}</small>
        </div>

        <TextInput name="Name" v-model="form.name" :message="form.errors.name"  />
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
          <select name="language" id="language" v-model="form.language">
            <option value="nl">Dutch</option>
            <option value="en">English</option>
            <option value="fr">French</option>
          </select>
        </div>

        <label class="checkbox-wrapper">
            <input type="checkbox"  v-model="form.is_admin" :checked="form.is_admin">
            Admin
        </label>
        
        <div class="form-actions">
            <button class="button" :disabled="form.processing"><i class="fas fa-save"></i> Save changes</button>
        </div>
    </form>
</template>
 
