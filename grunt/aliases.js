'use strict';

module.exports = {

  // Tests
  // =====

  'test-php': 'phplint',

  'test': 'test-php',


  // Build
  // =====

  // Email template
  // BEHOLD! Requires Premailer gem (https://github.com/premailer/premailer) installed in the system
  // (`$ gem install premailer`).
  'build-mail': [
    'clean:temp',
    'less:mail',
    'uncss:mail',
    'processhtml:mail',
    'premailer',
    'replace:mail'
  ],

  // CSS
  'build-css-front': [
    'less:front',
    'autoprefixer:front',
    'cssmin:front'
  ],

  'build-css-admin': [
    'less:admin',
    'autoprefixer:admin',
    'cssmin:admin'
  ],

  'build-css': [
    'clean:css',
    'build-css-front',
    'build-css-admin'
  ],

  // JS
  'build-js-front': [
    'coffee:front',
    'concat:front',
    'uglify:front'
  ],

  'build-js-admin': [
    'coffee:admin',
    'concat:admin',
    'po2json:admin',
    'uglify:admin',
    'copy:admin'
  ],

  'build-js': [
    'clean:js',
    'build-js-front',
    'build-js-admin'
  ],

  // Images
  'build-images-front': [
    'svgmin:front',
    'imagemin:front'
  ],

  'build-images-admin': [
    'svgmin:admin',
    'imagemin:admin'
  ],

  'build-images': [
    'clean:images',
    'build-images-front',
    'build-images-admin'
  ],

  // All together
  'build': [
    'clean:temp',
    'clean:fonts',
    'build-css',
    'build-js',
    'build-images',
    'copy:fonts',
    'copy:bower'
  ],


  // Development
  // ===========

  'dev': [
    'test',
    'build',
    'browserSync',
    'watch'
  ],


  // Aliases
  // =======

  'mail': 'build-mail',

  'default': [
    'test',
    'build'
  ]

};
