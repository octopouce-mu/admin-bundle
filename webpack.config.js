require('./Resources/assets/scss/style.scss');
require('./Resources/assets/scss/login.scss');

// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
// the project directory where all compiled assets will be stored
    .setOutputPath('Resources/public/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/public')

    // will create public/build/app.js and public/build/app.css
    .addEntry('app', './Resources/assets/js/admin.js')
    .addEntry('app', './Resources/assets/js/dashboard.js')

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