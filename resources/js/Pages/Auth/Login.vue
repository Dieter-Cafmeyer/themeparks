<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/TextInput.vue';

const form = useForm({
    email: null,
    password: null,
})

const submit = () => {
    form.post(route('login'), {
        onError: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title=" | Login" />

    <div class="login-page">
        <div class="login-form">
            <h1>Login</h1>

            <form @submit.prevent="submit">
                <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" />
                <TextInput name="Password" type="password" v-model="form.password" :message="form.errors.password" />

                <div class="form-actions">
                    <div class="form-actions__login">
                        Haven't got an account? <Link :href="route('register')">Register</Link>
                    </div>
                    <button class="button" :disabled="form.processing">Login</button>
                </div>
            </form>
        </div>
    </div>

</template>