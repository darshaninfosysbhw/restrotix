import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '192.168.1.68', // <-- Ye line add karo (Aapka IP)
        // host: '127.0.0.1',
        port: 5173,
        strictPort: true,
        hmr: {
            // host: '127.0.0.1', // <-- Taki mobile pe auto-refresh bhi chale
            host: '192.168.1.68',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
