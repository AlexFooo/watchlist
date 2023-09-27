import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
  ],
  build: {
    commonjsOptions: {
      transformMixedEsModules: true,
    },
    rollupOptions: {
      output: {
        assetFileNames: (assetInfo) => {
          let extType = assetInfo?.name?.split('.')[1]
          if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType || '')) {
            extType = 'img'
          }
          // return `${extType}/[name][extname]`
          return `[name][extname]`
        },
        chunkFileNames: '[name].js',
        entryFileNames: '[name].js',
      },
    },
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  }
})
