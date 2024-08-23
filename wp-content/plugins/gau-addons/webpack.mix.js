const mix = require('laravel-mix');
const path = require('path');

const directory = path.basename(path.resolve(__dirname));
const dir = `./wp-content/plugins/${directory}`;
const resources = `${dir}/resources`;
const assets = `${dir}/assets`;
const node_modules = './node_modules';

// Directories to copy
const directoriesToCopy = [{ from: `${resources}/img`, to: `${assets}/img` }];

//mix.disableNotifications();

// SCSS
mix.sass(`${resources}/sass/admin_addons.scss`, `${assets}/css`);

// JS
mix.js(`${resources}/js/custom_sorting.js`, `${assets}/js`)
    .js(`${resources}/js/lazyload.js`, `${assets}/js`)
    .js(`${resources}/js/recaptcha.js`, `${assets}/js`)
    .js(`${resources}/js/select2.js`, `${assets}/js/plugins`)
    .js(`${resources}/js/admin_addons.js`, `${assets}/js`);

// Copy directories
directoriesToCopy.forEach((dir) => mix.copyDirectory(dir.from, dir.to));

// Additional file
mix.copy(`${node_modules}/select2/dist/css/select2.min.css`, `${assets}/css/plugins`);
