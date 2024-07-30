const mix = require('laravel-mix');
require('mix-tailwindcss');

mix.sass('assets/scss/front.scss', 'assets/dist/css/front.css').tailwind();

mix.js('assets/js/front.js', 'assets/dist/js/front.js')
.js('assets/js/admin/app.js', 'assets/dist/js/admin/app.js')
.options({
    processCssUrls: false,
});

mix.disableNotifications();