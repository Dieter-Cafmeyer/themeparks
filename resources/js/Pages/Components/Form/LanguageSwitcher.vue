<script setup>
import { usePage } from '@inertiajs/vue3';
import "flag-icons/css/flag-icons.min.css";


const languages = [
  { code: 'en', label: 'English', flag: 'gb' },
  { code: 'nl', label: 'Nederlands', flag: 'nl' },
  { code: 'fr', label: 'Français', flag: 'fr' },
];

const props = usePage().props;
const locale = props.locale;
</script>

<template>
  <div class="language-switcher">
    <div class="language-switcher__active"><i class="fa-solid fa-globe"></i> {{ usePage().props.locale }} <i class="fa-solid fa-angle-down"></i> </div>
    <div class="language-switcher__dropdown">
        <Link v-for="lang in languages" :key="lang.code" :class="{ active: locale === lang.code }" :href="route('lang.switch', lang.code)">    
           <span :class="`fi fi-${lang.flag}`"></span> {{ lang.label }}
        </Link>
    </div>
  </div>
</template>

