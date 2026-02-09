<script setup>
import { useForm } from '@inertiajs/vue3';

import TextInput from './Components/Form/TextInput.vue';

defineProps({
    job: Object,
    locales: Array,
    tags: Array,
    back: String,
    apply: String,
    submit: String
});

const form = useForm({
    name: '',
    firstname: '',
    email: '',
    phone: '',
});


</script>

<template>
    <div class="container">
        <Link class="user_edit-back space-top-md" :href="route('jobs')"><i class="fas fa-angle-left"></i> {{ back }}</Link>

        <div class="job-detail space-top-md space-bottom-lg">
            <div class="job-detail__content">
                <div class="job-detail__content--header" v-if="job.media.length">
                    <img :src="`/storage/${job.media[0].path}`" />
                </div>

                <div class="job-detail__content--tags" v-if="job.tags.length">
                    <span v-for="tag in job.tags" :key="tag.id" class="tag-pill">{{ tag.name }}</span>
                </div>
                
                <h1 class="job-detail__content--title">{{ job.translations[0]?.title }}</h1>
                
                <div class="job-detail__content--description" v-html="job.translations[0]?.description"></div>

                

                <div class="job-detail__content--media" v-if="job.media.length > 1">
                    <img v-for="m in job.media.slice(1)" :key="m.id" :src="`/storage/${m.path}`" />
                </div>
            </div>

            <aside class="job-detail__form">
                <form @submit.prevent="submit">
                    <h3>{{ apply }}</h3>
                    <TextInput name="Firstname" v-model="form.firstname" :message="form.errors.firstname" />
                    <TextInput name="Name" v-model="form.name" :message="form.errors.name"  />
                    
                    <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" />
                    <TextInput name="Phone" type="tel" v-model="form.phone" :message="form.errors.phone" />

                    <div class="form-actions">
                        <button class="button" :disabled="form.processing">{{ submit }} <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                </form>
            </aside>
        </div>
  </div>
</template>