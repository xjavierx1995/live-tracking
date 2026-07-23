import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import { quasar, transformAssetUrls } from "@quasar/vite-plugin";
import path from "path";

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue({
      template: { transformAssetUrls },
    }),
    quasar({
      sassVariables: path.resolve(__dirname, "src/quasar-variables.sass"),
    }),
  ],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  server: {
    host: "0.0.0.0",

    port: 5173,

    strictPort: true,

    watch: {
      usePolling: true,
      interval: 1000,
      ignored: [
        "**/node_modules/**",
        "**/.git/**",
        "**/dist/**",
      ],
    },

    hmr: {
      host: "localhost",
      port: 5173,
    },

    proxy: {
      "/api": {
        target: "http://backend:8000",
        changeOrigin: true,
      },
    },
  },
});
