<script setup>
import { router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import FavoriteButton from '../../Components/Parks/FavoriteButton.vue';

const props = defineProps({
  park: Object
});

const imageUrl = ref('/storage/parks/placeholder-square.jpg');

onMounted(async () => {
  try {
    const res = await fetch(`/storage/parks/${props.park.api_id}.jpg`, { method: 'HEAD' })
    if (res.ok) {
      imageUrl.value = `/storage/parks/${props.park.api_id}.jpg`
    }
  } catch (e) {
    console.error('Error checking image:', e)
  }
});


</script>

<template>
    <article class="park_item">
      <Link :href="route('parks.detail', park.api_id)">
        <div class="park_item--wrapper">
          <div class="park_item--image">
            <img :src="imageUrl" :alt="park.name" />
          </div>
          
          <div class="park_item--content">
            <h4 class="text-lg font-semibold">{{ park.name }}</h4>
          </div>
        </div>

        <i class="fas fa-angle-right"></i> 
      </Link>

      <FavoriteButton v-if="park.is_favorited != null" :park-id="park.id" :is-favorited="park.is_favorited" />
    </article>
</template>

