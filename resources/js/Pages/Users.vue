<script setup>
const props = defineProps({
    users: Array,
    title: String,
});
</script>

<template>
    <Head :title="` | ${props.title}`" />

    <div class="container">
        <h1 class="space-top-md space-bottom-md">{{ props.title }}</h1>
        <div class="users-overview">
            <article class="users-overview__item" v-for="user in props.users" :key="user.id">
                <div class="users-overview__item--content">
                    <div class="users-overview__item--image">
                        <img :src="'/storage/' + user.profile_picture">
                    </div>
                    
                    <div class="users-overview__item--data">
                        <h4>{{ user.firstname }} {{ user.name }}</h4>
                        <p>{{ user.email }}</p>
                    </div>
                </div>
            </article>
        </div>
    </div>
</template>