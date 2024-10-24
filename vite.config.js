import {defineConfig} from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
  base: '',
  build: {
    manifest: true,
    outDir: 'dist',
    emptyOutDir: true,
    assetsDir: 'assets',
    rollupOptions: {
      input: [
        'src/js/main.js',
        'src/sass/main.scss'
      ],
    }
  },
  plugins: [
    laravel({
      publicDirectory: 'dist',
      input: [
        'src/js/main.js',
        'src/sass/main.scss'
      ],
      refresh: [
        '**.php'
      ]
    })
  ],
  resolve: {
    alias: [
      {
        find: /~(.+)/,
        replacement: process.cwd() + '/node_modules/$1'
      },
    ]
  }
})