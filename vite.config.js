import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import i18n from 'laravel-vue-i18n/vite'; 
import VueI18nPlugin from '@intlify/unplugin-vue-i18n/vite';
import vue from '@vitejs/plugin-vue'
import path from 'path';

export default defineConfig({
    plugins: [
        vue(),
        VueI18nPlugin({
            // Define the path to your locale files
            include: path.resolve(__dirname, 'lang/**')
        }),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        i18n(),
    ],
    resolve: {
        alias: {
            '@modules': path.resolve(__dirname, 'modules'),
        },
    },
    build: {
        chunkSizeWarningLimit: 1200,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('@vue-flow')) {
                            return 'vendor-vue-flow';
                        }
                        if (id.includes('@vueup/vue-quill')) {
                            return 'vendor-vue-quill';
                        }
                        if (id.includes('apexcharts') || id.includes('vue3-apexcharts')) {
                            return 'vendor-apexcharts';
                        }
                        if (id.includes('monaco-editor') || id.includes('prismjs') || id.includes('@wdns/vue-code-block')) {
                            return 'vendor-code-highlighter';
                        }
                        if (id.includes('pusher-js') || id.includes('laravel-echo')) {
                            return 'vendor-realtime';
                        }
                        if (id.includes('vue-tel-input') || id.includes('libphonenumber-js')) {
                            return 'vendor-tel-input';
                        }
                        if (id.includes('vuedraggable')) {
                            return 'vendor-draggable';
                        }
                    }
                },
            },
        },
    },
});