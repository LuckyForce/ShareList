const mix = require('laravel-mix');

mix.browserSync({
    proxy: process.env.APP_URL,
    notify: false
});
mix.js('resources/js/app.js', 'public/js').vue();
mix.postCss('resources/css/app.css', 'public/css', [
    require('tailwindcss'),
    ]);
