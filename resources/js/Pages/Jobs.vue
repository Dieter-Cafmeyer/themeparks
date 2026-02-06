<script setup>
const props = defineProps({
    jobs: Array,
    title: String,
    moreInfo: String
});
</script>

<template>
    <Head :title="` | ${props.title}`" />

    <div class="container">

        <h1 class="space-top-md space-bottom-md">{{ props.title }}</h1>
   
        <div class="job-list space-bottom-lg">
            <article v-for="job in props.jobs" :key="job.id" class="job-item">
            <Link :href="route('jobs.detail', job.id)">
                <div v-if="job.media.length" class="job-item-image">
                    <img :src="`/storage/${job.media[0].path}`" alt="" width="200">
                </div>

                <div class="job-item-content">
                    <div>
                        <div class="job-item-tags">
                            <span v-for="tag in job.tags" :key="tag.id">{{ tag.name }}</span>
                        </div>

                        <h3>{{ job.translations[0]?.title }}</h3>
                    </div>

                    <button class="button">{{ props.moreInfo }} <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </Link>
            </article>
        </div>
    </div>
</template>