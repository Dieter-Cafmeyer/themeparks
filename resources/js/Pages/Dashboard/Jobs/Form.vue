<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref } from 'vue';

import { Ckeditor } from '@ckeditor/ckeditor5-vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

const Editor = ClassicEditor;


const props = defineProps({
  job: Object,
  locales: Array,
  tags: Array,
  media: Array,
});

const form = useForm({
  active: props.job ? props.job.active == 1 : true,
  translations: props.job?.translations ?? 
    props.locales.map(locale => ({
        locale,
        title: '',
        description: '',
    })),
  tags: props.job?.tags?.map(t => t.name) ?? [],
  media: props.job?.media?.map(m => m.path) ?? [],
  previews: props.job?.media?.map(m => `/storage/${m.path}`) ?? [],
});

// Tags
const newTag = ref("");

const addTag = () => {
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


// Media
const addMedia = (e) => {
  const files = e.target.files;
  for (let i = 0; i < files.length; i++) {
    form.media.push(files[i]);
    form.previews.push(URL.createObjectURL(files[i]));
  }
};

const removeImage = (index) => {
  form.media.splice(index, 1);
  form.previews.splice(index, 1);
};


const submit = () => {
    if (props.job) {
        form.post(route("dashboard.jobs.update", props.job.id));
    } else {
        form.post(route("dashboard.jobs.store"));
    }
};

// Ckeditor
const editorConfig = {
  toolbar: [

    'heading',
    '|',
    'bold', 'italic',
    '|',
    'link',
    '|',
    'bulletedList', 'numberedList',
  ],
  placeholder: 'Write the job description here...',
};
</script>

<template>
    <form class="user_edit-form" @submit.prevent="submit">
      <div class="job-lang-item" v-for="(lang, index) in form.translations" :key="index">
          <h4>Language: {{ lang.locale.toUpperCase() }}</h4>
          <div class="form-item">
              <label>Title</label>
              <input v-model="lang.title" placeholder="Job title" />
              <small class="message-error" v-if="form.errors[`translations.${index}.title`]">{{ form.errors[`translations.${index}.title`] }}</small>
          </div>

          <div class="form-item">
              <label>Description</label>
              <Ckeditor :editor="Editor" :config="editorConfig" v-model="lang.description" />
          </div>
      </div>


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
      </div>

     

      <div class="form-item upload-picture">          
        <label>Media</label>

        <div class="job-images" v-if="form.previews.length > 0">
          <article v-for="(preview, index) in form.previews" :key="index" class="job-images-item">
            <img :src="preview" alt="">
            <button type="button" class="remove" @click="removeImage(index)"><i class="fas fa-xmark"></i></button>
          </article>
        </div>
        <div v-else>
          No media uploaded yet.
        </div>

        <label class="upload-file" for="profile_picture">
          <i class="fas fa-upload"></i> <span>Upload media</span>
        </label>

        <input type="file" id="profile_picture" @change="addMedia" multiple hidden>
      </div>

      <label class="checkbox-wrapper published">
          <input type="checkbox" v-model="form.active"/>
          Job Published
      </label>

      <div class="form-actions">
        <button type="submit" class="button" :disabled="form.processing"><i class="fas fa-save"></i> Save job</button>
      </div>
    </form>
</template>
