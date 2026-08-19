import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: '127.0.0.1', // Membuka akses jaringan lokal
        allowedHosts: true, // Mengizinkan semua domain ngrok untuk mengakses Vite kamu
        cors: true, // Tambahkan ini agar tidak kena block CORS
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
