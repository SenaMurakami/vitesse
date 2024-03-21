import { defineConfig } from 'vite';
import autoprefixer from "autoprefixer";
import path from 'path';

export default defineConfig(({ mode }) => {
  return {
    build: {
      outDir: path.resolve( __dirname, './assets' ),
      rollupOptions: {
        input: {
          main: path.resolve( __dirname + '/src/js/main.js' )
        },
        output: {
          entryFileNames: `js/[name].js`,
          chunkFileNames: `js/[name].js`,
          assetFileNames: ( { name } ) => {
            if ( /\.css$/.test( name ?? '' ) ) {
              return 'css/[name].[ext]';
            }
            if ( /\.js$/.test( name ?? '' ) ) {
              return 'js/[name].[ext]';
            }
            return '[name].[ext]';
          }
        },
      },
      sourcemap: mode === 'development',
      minify: mode === 'production',
    },
    css: {
      postcss: {
        plugins: [autoprefixer],
      },
    },
  };
});
