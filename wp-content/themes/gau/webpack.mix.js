const mix = require('laravel-mix');

const path = require('path');
const directory = path.basename(path.resolve(__dirname));

const dir = './wp-content/themes/' + directory;

const resources = dir + '/resources';
const assets = dir + '/assets';
const storage = dir + '/storage';
const node_modules = './node_modules';

// Directories to copy
const directoriesToCopy = [
    { from: `${storage}/fonts/fontawesome/webfonts`, to: `${assets}/webfonts` },
    { from: `${resources}/img`, to: `${assets}/img` },
];

// SASS
const sassFiles = [
    // admin
    'editor-style',
    'admin',

    // plugin
    //'plugins/swiper',
    //'plugins/woocommerce',

    // site
    'fonts',
    'plugins',
    //'app',
];

// JS
const jsFiles = [
    // admin
    //'login',
    'admin',

    // plugin
    // 'plugins/back-to-top',
    // 'plugins/social-share',
    // 'plugins/skip-link-focus',
    // 'plugins/flex-gap',
    // 'plugins/load-scripts',
    // 'plugins/passive-events',
    // 'plugins/mark',
    // 'plugins/swiper',
    // 'plugins/woocommerce',

    // site
    //'app',
];

//mix.disableNotifications();

// Process SASS
sassFiles.forEach((file) => {
    let outputDir = file.includes('/') ? file.split('/')[0] : '';
    mix.sass(`${resources}/sass/${file}.scss`, `${assets}/css${outputDir ? '/' + outputDir : ''}`);
});

// Process JS
jsFiles.forEach((file) => {
    let outputDir = file.includes('/') ? file.split('/')[0] : '';
    mix.js(`${resources}/js/${file}.js`, `${assets}/js${outputDir ? '/' + outputDir : ''}`);
});

// Copy directories
directoriesToCopy.forEach((dir) => mix.copyDirectory(dir.from, dir.to));

// Additional JS file
mix.copy(`${node_modules}/select2/dist/css/select2.min.css`, `${assets}/css/plugins`);
mix.copy(`${node_modules}/select2/dist/js/select2.full.min.js`, `${assets}/js/plugins`);

mix.copy(`${node_modules}/pace-js/pace.min.js`, `${assets}/js/plugins`);
