<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '../Components/Form/TextInput.vue';

const form = useForm({
    name: null,
    firstname: null,
    email: null,
    password: null,
    password_confirmation: null
})

const submit = () => {
    form.post(route('register'), {
        onError: () => form.reset('password', 'password_confirmation'),
    });
};

</script>

<template>
    <Head title=" | Register" />

    <div class="register-page">
        <div class="register-form">
            <h1>Register a new account</h1>

            <form @submit.prevent="submit">
                <TextInput name="Name" v-model="form.name" :message="form.errors.name" />
                <TextInput name="Firstname" v-model="form.firstname" :message="form.errors.firstname" />
                <TextInput name="Email" type="email" v-model="form.email" :message="form.errors.email" />
                <TextInput name="Password" type="password" v-model="form.password" :message="form.errors.password" />
                <TextInput name="Confirm password" type="password" v-model="form.password_confirmation"  />
                
                <div class="form-actions">
                    <div class="form-actions__login">
                        Already a user? <Link :href="route('login')">Login</Link>
                    </div>
                    <button class="button" :disabled="form.processing">Register</button>
                </div>
            </form>
        </div>
    </div>

</template>