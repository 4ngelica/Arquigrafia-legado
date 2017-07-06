var path = require('path');
var webpack = require('webpack');

module.exports = {
  entry: {
    suggestions: './javascript/suggestions',
  },
  output: {
    filename: '[name].bundle.js',
    path: path.resolve(__dirname, 'public/js/dist')
  },
  module: {
    rules: [
      { test: /\.js$/, exclude: /node_modules/, loader: "babel-loader" }
    ]
  },
  plugins:[
    new webpack.ProvidePlugin({
        Promise: "bluebird",
    })
  ],
};
