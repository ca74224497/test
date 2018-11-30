const path = require("path");
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const ExtractTextPlugin = require("extract-text-webpack-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const HtmlWebpackPlugin = require("html-webpack-plugin");
const TSLintPlugin = require("tslint-webpack-plugin");
const VueLoaderPlugin = require("vue-loader/lib/plugin");
const exec = require('child_process').exec;
let conf = {
    entry: [
        "./src/js/init.ts"
    ],
    output: {
        path: path.resolve(__dirname, "./assets"),
        filename: "js/main.js",
        publicPath: "assets/"
    },
    optimization: {
        minimizer: [
            // new UglifyJsPlugin({
            //     uglifyOptions: {
            //         output: {
            //             comments: false
            //         }
            //     }
            // }),
            new OptimizeCSSAssetsPlugin({
                cssProcessorPluginOptions: {
                    preset: ['default', {discardComments: {removeAll: true}}],
                }
            })
        ]
    },
    devServer: {
        overlay: {
            warnings: false,
            errors: true
        }
    },
    resolve: {
        extensions: [".json", ".js", ".ts", ".vue"],
        alias: {
            vue$: "vue/dist/vue.esm.js"
        }
    },
    module: {
        rules: [
            {
                test: /\.vue.ts$/,
                enforce: "pre",
                use: [{
                    loader: "vue-tslint-loader",
                    options: {emitErrors: true}
                }]
            },
            {
                test: /\.vue$/,
                loader: "vue-loader",
                options: {
                    loaders: {
                        ts: "ts-loader"
                    },
                    esModule: true
                }
            },
            {
                test: /\.ts$/,
                loader: "ts-loader",
                exclude: /node_modules/,
                options: {
                    appendTsSuffixTo: [/\.vue$/],
                }
            },
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    fallback: "style-loader",
                    use: "css-loader"
                })
            },
            {
                test: /\.(png|jpe?g|svg)$/,
                use: [{
                    loader: "url-loader",
                    options: {
                        limit: 8000, // Convert images < 8kb to base64 strings
                        name: "[name].[ext]",
                        publicPath: "../images",
                        outputPath: "/images"
                    }
                }]
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2)$/,
                use: [{
                    loader: "file-loader",
                    options: {
                        name: "[name].[ext]",
                        publicPath: "../fonts",
                        outputPath: "/fonts"
                    }
                }]
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin(),
        new TSLintPlugin({
            files: ["./src/**/*.ts"]
        }),
        new HtmlWebpackPlugin({
            filename: "./../index.html",
            template: "./template.html",
            inject: false,
            hash: true,
            xhtml: true
        }),
        new ExtractTextPlugin("css/main.css"),
        new CopyWebpackPlugin([
            {from: "src/images", to: "images"},
            {from: "src/fonts", to: "fonts"}
        ]),
        {
            apply: (compiler) => {
                compiler.hooks.afterEmit.tap('AfterEmitPlugin', (compilation) => {
                    exec('./webpack.sh', (err, stdout, stderr) => {
                        if (stdout) process.stdout.write(stdout);
                        if (stderr) process.stderr.write(stderr);
                    });
                });
            }
        }
    ]
};
module.exports = (env, options) => {
    const production = options.mode === "production";
    conf.devtool = production ?
        false : "eval-sourcemap";
    return conf;
};