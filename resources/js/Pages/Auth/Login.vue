<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import TextInput from '../Components/Form/TextInput.vue';

const { props: pageProps } = usePage();
const t = (key) => pageProps.translations?.[key] ?? key;

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
    <Head :title="` | ${t('login')}`" />

    <div class="login-page">
        <div class="login-form">
            <h1>{{ t('login') }}</h1>

            <form @submit.prevent="submit">
                <TextInput :name="t('email')" type="email" v-model="form.email" :message="form.errors.email" />
                <TextInput :name="t('password')" type="password" v-model="form.password" :message="form.errors.password" />

                <div class="form-actions">
                    <div class="form-actions__login">
                        {{ t('no_account') }} <Link :href="route('register')">{{ t('register') }}</Link>
                    </div>
                    <button class="button" :disabled="form.processing">{{ t('login') }}</button>
                </div>
            </form>
        </div>
    </div>

</template>