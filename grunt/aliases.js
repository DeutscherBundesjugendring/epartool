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

  'build-css': [
    'clean:css',
    'less',
    'copy:front',
    'autoprefixer',
    'cssmin'
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
