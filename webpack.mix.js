const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

const path = require('path');
mix.webpackConfig({
   resolve: {
      alias: {
         '@': path.resolve('./resources/js'),
      }
   }
});


mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .browserSync({
      disableHostCheck: true,
      contentBase: path.join(__dirname, "public"),
      publicPath: '/',
      host: '0.0.0.0',
      port: 8888,
      proxy: {
         target: 'http://lp-easy-order-nginx:21400'
      },
      files: [
         './resources/**/*',
         './app/**/*',
         './config/**/*',
         './routes/**/*',
         './public/**/*',
      ],
      open: false,
      reloadOnRestart: true,
   });
