var Encore = require('@symfony/webpack-encore')

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
}

Encore.setOutputPath('public/build/')
// directory where compiled assets will be stored

// public path used by the web server to access the output path

if (Encore.isProduction()) {

    Encore.setPublicPath('/build')
} else {
// if (!Encore.isProduction()) {
// this is your *true* public path
    Encore.setPublicPath('/kukupad/public/build')

    // this is now needed so that your manifest.json keys are still `build/foo.js`
    // (which is a file that's used by Symfony's `asset()` function)
    Encore.setManifestKeyPrefix('build')

}

// only needed for CDN's or sub-directory deploy
//.setManifestKeyPrefix('build/')

/*
 * ENTRY CONFIG
 *
 * Add 1 entry for each "page" of your app
 * (including one that's included on every page - e.g. "app")
 *
 * Each entry will result in one JavaScript file (e.g. app.js)
 * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
 */
Encore.addEntry('app', './assets/js/app.js')

    .addEntry('editor', './assets/js/editor.js')
    .addEntry('page', './assets/js/page.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabel(() => {
    }, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // enables Sass/SCSS support
    .enableSassLoader()

// uncomment if you use TypeScript
//.enableTypeScriptLoader()

// uncomment to get integrity="..." attributes on your script & link tags
// requires WebpackEncoreBundle 1.4 or higher
//.enableIntegrityHashes(Encore.isProduction())

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()

// uncomment if you use API Platform Admin (composer req api-admin)
//.enableReactPreset()
//.addEntry('admin', './assets/js/admin.js')

module.exports = Encore.getWebpackConfig()
