<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import TextInput from '../Components/Form/TextInput.vue';

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

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
    <Head :title="` | ${t('register')}`" />

    <div class="register-page">
        <div class="register-form">
            <h1>{{t('register_now')}}</h1>

            <form @submit.prevent="submit">
                <TextInput :name="t('name')" v-model="form.name" :message="form.errors.name" />
                <TextInput :name="t('firstname')" v-model="form.firstname" :message="form.errors.firstname" />
                <TextInput :name="t('email')" type="email" v-model="form.email" :message="form.errors.email" />
                <TextInput :name="t('password')" type="password" v-model="form.password" :message="form.errors.password" />
                <TextInput :name="t('confirm_password')" type="password" v-model="form.password_confirmation"  />
                
                <div class="form-actions">
                    <div class="form-actions__login">
                        Already a user? <Link :href="route('login')">{{ t('login') }}</Link>
                    </div>
                    <button class="button" :disabled="form.processing">{{ t('register') }}</button>
                </div>
            </form>
        </div>
    </div>

</template>