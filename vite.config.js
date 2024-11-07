import { defineConfig } from 'vite'
import laravel, { refreshPaths } from 'laravel-vite-plugin'
import fs from 'fs'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js','resources/css/navbar.css'],
            refresh: [
                ...refreshPaths,
                'app/Filament/**',
                'app/Forms/Components/**',
                'app/Livewire/**',
                'app/Infolists/Components/**',
                'app/Providers/Filament/**',
                'app/Tables/Columns/**',
            ],
        }),
        {
            name: 'copy-leaflet-assets',
            enforce: 'post',
            apply: 'build',
            generateBundle() {
                const leafletAssets = [
                    {
                        src: 'node_modules/leaflet/dist/images/marker-icon.png',
                        dest: 'public/marker-icon.png'
                    },
                    {
                        src: 'node_modules/leaflet/dist/images/marker-icon-2x.png',
                        dest: 'public/marker-icon-2x.png'
                    },
                    {
                        src: 'node_modules/leaflet/dist/images/marker-shadow.png',
                        dest: 'public/marker-shadow.png'
                    }
                ];

                leafletAssets.forEach(asset => {
                    this.emitFile({
                        type: 'asset',
                        fileName: asset.dest,
                        source: fs.readFileSync(asset.src)
                    });
                });
            }
        }
    ],
})
