import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        chunkSizeWarningLimit: 900,
        rolldownOptions: {
            checks: {
                pluginTimings: false,
            },
            output: {
                codeSplitting: {
                    groups: [
                        {
                            name: 'react-vendor',
                            test: /node_modules[\\/](react|react-dom|@inertiajs)[\\/]/,
                            priority: 30,
                        },
                        {
                            name: 'ui-vendor',
                            test: /node_modules[\\/](@radix-ui|lucide-react|class-variance-authority|clsx|tailwind-merge)[\\/]/,
                            priority: 20,
                        },
                        {
                            name: 'vendor',
                            test: /node_modules[\\/]/,
                            priority: 10,
                        },
                    ],
                },
            },
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        inertia(),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ],
});
