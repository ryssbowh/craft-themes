
const path = require('path')

module.exports = {
    productionSourceMap: false,
    lintOnSave: process.env.NODE_ENV !== 'production',
    runtimeCompiler: true,
    filenameHashing: false,
    outputDir: 'vue/dist',
    css: {
        extract: false
    },
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
            display: "./vue/src/display/main.js",
            fields: "./vue/src/fields/main.js",
            vue: "./vue/src/vue.js",
        },
        output: {
            filename: "js/[name].js"
        },
        optimization: {
            concatenateModules: false,
            providedExports: false,
            usedExports: false
        },
        resolve: {
          alias: {
            vue: path.resolve('./node_modules/vue')
          }
        }
    }
}