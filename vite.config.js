// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';
// import vue from '@vitejs/plugin-vue'

// export default defineConfig({
//     plugins: [
//         vue(),
//         laravel({
//             input: ['resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
//     server: {
//     host: '192.168.3.120',  
//     port: 3000,
//     strictPort: true,
//   },
// });

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [
    vue(),
    laravel({
      input: ['resources/js/app.js'],
      refresh: true,
    }),
  ],
  // server: {
  //   host: '192.168.3.120',  
  //   port: 3000,
  //   strictPort: true,
  // },
  base: '/',
});