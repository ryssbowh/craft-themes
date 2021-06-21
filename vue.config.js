
const path = require('path')

module.exports = {
    lintOnSave: process.env.NODE_ENV !== 'production',
    runtimeCompiler: true,
    outputDir: 'vue/dist',
    chainWebpack: config => {
        // delete default entry point 'app'
        config.entryPoints.delete("app").end();
        //delete default 'html' plugin - in case you don't want default index.html file
        //delete 'prefetch' and 'preload' plugins which are dependent on 'html' plugin
        config.plugins
        .delete("html")
        .delete("prefetch")
        .delete("preload");
    },
    configureWebpack: {
        entry: {
            blocks: "./vue/src/blocks/main.js",
            blockOptions: "./vue/src/blockOptions/main.js",
            display: "./vue/src/display/main.js",
            fieldDisplayers: "./vue/src/fieldDisplayers/main.js",
            fileDisplayers: "./vue/src/fileDisplayers/main.js",
            fields: "./vue/src/fields/main.js"
        },
        output: {
            filename: "js/[name].js"
        },
        resolve: {
          alias: {
            vue: path.resolve('./node_modules/vue')
          }
        }
    }
}