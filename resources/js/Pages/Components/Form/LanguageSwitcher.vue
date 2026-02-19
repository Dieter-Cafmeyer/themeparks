<script setup>
import { usePage } from '@inertiajs/vue3';
import "flag-icons/css/flag-icons.min.css";

const props = usePage().props;
const locale = props.locale;
const t = (key) => props.translations?.[key] ?? key;

const languages = [
  { code: 'en', label: t('english'), flag: 'gb' },
  { code: 'nl', label: t('dutch'), flag: 'nl' },
  { code: 'fr', label: t('french'), flag: 'fr' },
];

</script>

<template>
  <div class="language-switcher">
    <div class="language-switcher__active">
      <div>
        <i class="fa-solid fa-globe"></i> {{ usePage().props.locale }} 
      </div>
      <i class="fa-solid fa-angle-down"></i> 
    </div>
    <div class="language-switcher__dropdown">
        <Link v-for="lang in languages" :key="lang.code" :class="{ active: locale === lang.code }" :href="route('lang.switch', lang.code)">    
           <span :class="`fi fi-${lang.flag}`"></span> {{ lang.label }}
        </Link>
    </div>
  </div>
</template>

