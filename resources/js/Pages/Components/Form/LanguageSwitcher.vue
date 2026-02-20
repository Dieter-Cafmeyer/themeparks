<script setup>
import { usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import "flag-icons/css/flag-icons.min.css"

const page = usePage()

const locale = ref(page.props.locale)

const t = (key) => page.props.translations?.[key] ?? key

const languages = [
  { code: 'en', label: 'English', flag: 'gb' },
  { code: 'nl', label: 'Nederlands', flag: 'nl' },
  { code: 'fr', label: 'Français', flag: 'fr' },
]

watch(
  () => page.props.locale,
  (newLocale) => {
    locale.value = newLocale
  }
)
</script>

<template>
  <div class="language-switcher">
    <div class="language-switcher__active glass-button">
      <div>
        <i class="fas fa-globe"></i>
      </div>
    </div>
    <div class="language-switcher__dropdown">
        <Link v-for="lang in languages" :key="lang.code" :class="{ active: locale === lang.code }" :href="route('lang.switch', lang.code)">    
           <span :class="`fi fi-${lang.flag}`"></span> {{ lang.label }}
        </Link>
    </div>
  </div>
</template>

