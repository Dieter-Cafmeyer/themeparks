<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
    required: true
  },
  star: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <label class="switch">
    <input type="checkbox" :checked="modelValue" @change="$emit('update:modelValue', $event.target.checked)" />
    <span class="slider">
      <i v-if="star" class="far fa-star"></i>
    </span>
  </label>
</template>