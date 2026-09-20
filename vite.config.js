import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // chart.js entry terpisah: hanya dimuat halaman admin yang punya chart,
            // supaya bundel sisi orang tua (mobile-first) tidak ikut membawa Chart.js.
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/chart.js'],
            refresh: true,
        }),
    ],
});
