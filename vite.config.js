import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    build: {
        manifest: true,
        outDir: 'public/build', // Dove generare i file
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
                style: 'resources/css/app.css',
            },
            output: {
                // NIENTE .vite! Qui forziamo tutto in public/build
                assetFileNames: 'assets/[name]-[hash][extname]',
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
            },
        },
        emptyOutDir: true,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
});
