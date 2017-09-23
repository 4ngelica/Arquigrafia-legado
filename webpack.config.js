var path = require('path');
var webpack = require('webpack');
var vueLoaderConfig = require('./vue-loader.conf')

module.exports = {
  entry: {
    suggestions: './javascript/suggestions',
    suggestionsList: './javascript/suggestions-list',
    contributions: './javascript/contributions',
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
    })
  ],
};
