<script setup>
const model = defineModel({
    type: null,
    required: true,
})

defineProps({
    name: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    message: {
        type: String,
    },
})

</script>

<template>
    <div class="form-item">
        <label>{{ name }}</label>
        <input :type="type" v-model="model" :class="{'error' : message}">
        <small class="message-error" v-if="message">{{ message }}</small>
    </div>
</template>