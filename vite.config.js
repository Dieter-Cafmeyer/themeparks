import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import fs from 'fs'


export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: 'themeparks.test',
        https: {
            key: fs.readFileSync(
                '/Users/dieter.cafmeyer/.config/valet/Certificates/themeparks.test.key'
            ),
            cert: fs.readFileSync(
                '/Users/dieter.cafmeyer/.config/valet/Certificates/themeparks.test.crt'
            ),
        },
        hmr: {
            host: 'themeparks.test',
        }
    },
});
