const ImageMinimizerPlugin = require("image-minimizer-webpack-plugin");
const MiniCSSExtractPlugin = require("mini-css-extract-plugin")
// const NodePolyfillPlugin = require('node-polyfill-webpack-plugin');
const path = require("path")

let mode = "development",
    source_map = "source-map"

// if NODE_ENV is set to prod, we disable source-maps,
// and set webpack mode is production for it to use
// its built in optimizations accordingly eg minified/optimized
// files.
if (process.env.NODE_ENV === "production") {
    mode = "production"
    source_map = "eval"
}

module.exports = {
    mode: mode,

   /**
     * entries for raw js files (source)
     */
    entry: {
        chat_nv: path.resolve(__dirname, 'src/chat_nv.js'),
    },
    /**
     * output folder,
     * where [name] === entry[name]/entry[i] from above
     */
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, '../dist'),
        clean: true,
    },

    /**
     * devtools controls if and how source maps are generated.
     */
    devtool: source_map,

    /**
     * https://webpack.js.org/configuration/plugins/
     */
    plugins: [
      // new NodePolyfillPlugin(),
      new MiniCSSExtractPlugin()
    ],

    /**
     * https://webpack.js.org/configuration/module/#rule
     */
    module: {
        rules: [
            {
                test: /\.(sc|c)ss$/i,
                /**
                 * postcss-loader (postcss.config.js),
                 * css-loader and
                 * finally we extract css to
                 * a separate file with MiniCSSExtractPlugin.loader plugin.
                 * Another option, is to use style-loader to inject inline css into
                 * our template files but we don't need that approach.
                 */
                use:[
                  {
                    loader: MiniCSSExtractPlugin.loader,
                    // options: { publicPath: "" }
                  },
                    "css-loader",
                    "postcss-loader",
                    "sass-loader"
                ]
            },
            {
                test: /\.(js|ts)x$/i,
                exclude: /node_modules/,
                loader: "babel-loader",
            },
            {
                test: /\.(woff(2)?|ttf|eot|png|jpe?g|gif|svg)$/i,
                type: "asset/resource"
            },
        ]
    },
    optimization: {
        minimizer: [
            "...",
            new ImageMinimizerPlugin({
                minimizer: {
                    implementation: ImageMinimizerPlugin.squooshMinify,
                    options: {
                        // Your options for `squoosh`
                    },
                },
            }),
        ],
    },

}
