const path = require('path');

module.exports = () => ({
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
  optimization: { minimize: true },
  output: {
    filename: '[name].min.js',
    path: path.resolve(__dirname, './www/js'),
  },
  resolve: {
    extensions: ['.js', '.jsx', '.json'],
  },
});
