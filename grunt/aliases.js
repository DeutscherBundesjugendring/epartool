'use strict';

module.exports = {

  // Build
  // =====

  'build-install': [
    'copy:install',
  ],

  // CSS
  'build-css-front': [
    'less:front',
    'replace:leafletFront',
    'postcss:front',
  ],

  'build-css-admin': [
    'less:admin',
    'replace:leafletAdmin',
    'postcss:admin',
  ],

  'build-css': [
    'clean:css',
    'build-css-front',
    'build-css-admin',
  ],

  // JS
  'build-js-front': [
    'concat:front',
    'uglify:front',
  ],

  'build-js-admin': [
    'concat:admin',
    'uglify:admin',
    'copy:admin',
  ],

  'build-js': [
    'clean:js',
    'build-js-front',
    'build-js-admin',
  ],

  // Images
  'build-images-front': [
    'svgmin:front',
    'imagemin:front',
  ],

  'build-images-admin': [
    'svgmin:admin',
    'imagemin:admin',
  ],

  'build-images': [
    'clean:images',
    'build-images-front',
    'build-images-admin',
  ],

  // All together now!
  'build': [
    'clean:temp',
    'clean:fonts',
    'build-css',
    'build-js',
    'build-images',
    'copy:fonts',
    'copy:components',
  ],

  // Development
  // ===========

  'dev': [
    'build',
    'browserSync',
    'watch'
  ],

  // Aliases
  // =======

  'default': ['build'],

};
