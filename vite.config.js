import { defineConfig } from "vite";
import autoprefixer from "autoprefixer";
import path from "path";
import browserSyncPlugin from 'vite-plugin-browser-sync';

export default defineConfig(({ mode }) => {

  return {
    build: {
      outDir: path.resolve(__dirname, "./assets"),
      rollupOptions: {
        input: {
          main: path.resolve(__dirname + "/src/js/main.js"),
        },
        output: {
          entryFileNames: `js/[name].js`,
          chunkFileNames: `js/[name].js`,
          manualChunks(id) {
            // .scss ファイルの場合、ベース名を chunk 名として返す
            if (id.endsWith('.scss')) {
              return path.basename(id, '.scss');
            }
          },
          assetFileNames: ({ name, ext }) => {
            // 開発中はキャッシュをクリアさせるためにハッシュを付与
            if(mode === "development") {
              if (/\.css$/.test(name ?? "")) {
                return "css/[name].[hash][extname]";
              }
              if (/\.js$/.test(name ?? "")) {
                return "js/[name].[hash][extname]";
              }
              return "[name].[hash][extname]";
            } else {
              if (/\.css$/.test(name ?? "")) {
                return "css/[name].[ext]";
              }
              if (/\.js$/.test(name ?? "")) {
                return "js/[name].[ext]";
              }
              return "[name].[ext]";
            }
          },
        },
      },
      manifest: true,
      sourcemap: mode === "development",
      minify: mode === "production",
    },
    css: {
      postcss: {
        plugins: [autoprefixer],
      },
    },
    plugins: [
      browserSyncPlugin({
        buildWatch: {
          enable: true,
          bs: {
            codeSync: true,
            files:[
              './src/scss/**/*.scss',
              './src/js/**/*.js',
              './**/*.php'
            ],
            proxy: 'http://localhost:8000',
          }
        }
      })
    ],
  };
});
