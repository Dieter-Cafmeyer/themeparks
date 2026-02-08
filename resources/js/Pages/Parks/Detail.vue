<template class="park_detail">
    <div class="park_detail--image">
        <img :src="`/storage/parks/${props.park.id}.jpg`" :alt="park.name" />

        <h1 class="park_detail--parkname">
           {{ title }}
        </h1>
    </div>
    
</template>


<script setup>

const props = defineProps({
    park: Array,
    title: String
});
</script>
