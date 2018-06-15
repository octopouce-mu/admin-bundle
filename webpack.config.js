// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where all compiled assets will be stored
    .setOutputPath('Resources/public/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/public')

    // will create public/build/app.js and public/build/app.css
    .addEntry('js/admin', './Resources/assets/js/admin.js')
    .addEntry('js/dashboard', './Resources/assets/js/dashboard.js')

    .addEntry('css/login', './Resources/assets/scss/login.scss')
    .addEntry('css/style', './Resources/assets/scss/style.scss')
    .addEntry('css/vendor/jvector', './Resources/assets/scss/jvectormap/jquery-jvectormap-2.0.3.css')

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    // enable source maps during development
    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    // create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning()

    // allow sass/scss files to be processed
    .enableSassLoader()
;

// export the final configuration
module.exports = Encore.getWebpackConfig();