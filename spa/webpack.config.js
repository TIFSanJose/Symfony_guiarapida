const Encore = require('@symfony/webpack-encore');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const webpack = require('webpack');


Encore
    .setOutputPath('public/')
    .setPublicPath('/')
    .cleanupOutputBeforeBuild()
    .addEntry('app', './src/app.js')
    .enablePreactPreset()
    .enableSingleRuntimeChunk()
    .addPlugin(new HtmlWebpackPlugin({ template: 'src/index.ejs', alwaysWriteToDisk: true }))
    .enableSassLoader()
    .addPlugin(new webpack.DefinePlugin({
        'ENV_API_ENDPOINT': JSON.stringify(proccess.env.API_ENDPOINT),
    }))
;

module.exports = Encore.getWebpackConfig();
