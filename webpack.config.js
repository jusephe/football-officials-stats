var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.scss) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.js')

    .addEntry('admin_add_game', './assets/js/admin/add_game.js')
    .addEntry('admin_add_game_form', './assets/js/admin/add_game_form.js')
    .addEntry('admin_add_punishments', './assets/js/admin/add_punishments.js')

    .addEntry('site_old_posts', './assets/js/site/old_posts.js')
    .addEntry('site_season_stats', './assets/js/site/season_stats.js')
    .addEntry('site_profile', './assets/js/site/profile.js')
    .addEntry('site_official_profile', './assets/js/site/official_profile.js')
    .addEntry('site_assessor_profile', './assets/js/site/assessor_profile.js')

    .addStyleEntry('login', './assets/css/admin/login.css')
    .addStyleEntry('admin_base', './assets/css/admin/base.css')
    .addStyleEntry('admin_form', './assets/css/admin/form.css')
    .addStyleEntry('admin_add_edit_nomination_lists', './assets/css/admin/add_edit_nomination_lists.css')

    .addStyleEntry('site_base', './assets/css/site/base.css')
    .addStyleEntry('site_post', './assets/css/site/post.css')

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
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

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

    // DataTables fix
    .addLoader({ test: /datatables\.net.*/, loader: 'imports-loader?define=>false' })
;


    module.exports = Encore.getWebpackConfig();
