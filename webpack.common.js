const path = require('path');
const webpack = require('webpack');
const vueLoaderConfig = require('./vue-loader.conf')

module.exports = {
  entry: {
    suggestions: './javascript/containers/suggestions',
    suggestionsList: './javascript/containers/suggestions-list',
    contributions: './javascript/containers/contributions',
  },
  output: {
    filename: '[name].bundle.js',
    path: path.resolve(__dirname, 'public/js/dist')
  },
  resolve: {
    alias: {
      vue: 'vue/dist/vue.js'
    }
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: vueLoaderConfig
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        loader: "babel-loader"
      },
    ]
  },
  plugins:[
    new webpack.ProvidePlugin({
      Promise: "bluebird",
    }),
  ],
};