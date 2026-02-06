import './bootstrap';
import '../css/app.scss';

import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { CkeditorPlugin } from '@ckeditor/ckeditor5-vue';

import Layout from './Layouts/Layout.vue';

createInertiaApp({
  title: (title) => `Themeparks ${title}`,
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    let page =  pages[`./Pages/${name}.vue`];
    page.default.layout = page.default.layout || Layout;
    return page;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(CkeditorPlugin)
      .component('Head', Head)
      .component('Link', Link)
      .mount(el)
  },
})