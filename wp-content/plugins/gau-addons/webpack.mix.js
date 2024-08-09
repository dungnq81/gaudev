const mix = require('laravel-mix');
const path = require('path');

const directory = path.basename(path.resolve(__dirname));
const dir = `./wp-content/plugins/${directory}`;
const resources = `${dir}/resources`;
const assets = `${dir}/assets`;

mix.disableNotifications();

// Compile SASS files
mix.sass(`${resources}/sass/editor-style.scss`, `${assets}/css`);

// Compile JS files
mix.js(`${resources}/js/custom_order.js`, `${assets}/js`)
    .js(`${resources}/js/lazyload.js`, `${assets}/js`)
    .js(`${resources}/js/recaptcha.js`, `${assets}/js`)
    .js(`${resources}/js/addon.js`, `${assets}/js`);
