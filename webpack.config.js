const path = require('path');
const webpack = require('webpack');

// To build in production mode:
// $ env NODE_ENV=production webpack -p
const environment = process.env.NODE_ENV;
const isProduction = environment === 'production';

const config = {
  devServer: {
    inline: true,
    hot: true,
  },
  entry: {
    'followup-timeline': [
      'whatwg-fetch',
      './assets/front/react/src/main.jsx',
    ],
  },
  module: {
    rules: [
      {
        test: /assets\/front\/react\/src\/.*\.(js|jsx)$/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
          },
        },
      },
    ],
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': { NODE_ENV: JSON.stringify(environment) },
    }),
  ],
  output: {
    path: path.resolve(__dirname, './www/js'),
    filename: isProduction ? '[name].min.js' : '[name].js',
  },
  resolve: {
    extensions: ['.js', '.jsx', '.json'],
  },
};

if (isProduction) {
  config.plugins.push(
    new webpack.optimize.UglifyJsPlugin({ minimalize: true })
  );
}

module.exports = config;
