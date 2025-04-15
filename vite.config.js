import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        {
            name: 'copy-manifest',
            closeBundle() {
                const src = path.resolve(__dirname, 'public/build/.vite/manifest.json');
                const dest = path.resolve(__dirname, 'public/build/manifest.json');

                if (fs.existsSync(src)) {
                    fs.copyFileSync(src, dest);
                    console.log('✅ Copiato manifest.json nella root di build/');
                } else {
                    console.warn('⚠️  manifest.json non trovato in .vite');
                }
            }
        }
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: ['resources/css/app.css', 'resources/js/app.js'],
        },
    },
    base: '/build/', // 🔐 Forza URL relativi corretti
});