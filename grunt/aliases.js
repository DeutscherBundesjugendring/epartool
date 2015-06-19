'use strict';

module.exports = {

  // BEHOLD! Requires Premailer gem (https://github.com/premailer/premailer) installed in the system
  // (`$ gem install premailer`).
  'mail': [
    'clean:temp',
    'less:mail',
    'uncss:mail',
    'processhtml:mail',
    'premailer',
    'replace:mail'
  ],

  'build-css-dev': [
    'clean:css',
    'less:dev',
    'autoprefixer:dev'
  ],

  'build-css-dist': [
  'build-css-dev',
    'cssmin'
  ],

  'build-js-dev': [
    'clean:js',
    'coffee',
    'concat',
    'po2json',
    'copy:js'
  ],

  'build-js-dist': [
    'build-js-dev',
    'uglify'
  ],

  'build-dev': [
    'clean:temp',
    'clean:fonts',
    'phplint',
    'build-css-dev',
    'build-js-dev',
    'copy:fonts',
    'copy:bower'
  ],

  'build-dist': [
    'clean:temp',
    'clean:fonts',
    'phplint',
    'build-css-dist',
    'build-js-dist',
    'copy:fonts',
    'copy:bower'
  ],

  'build': 'build-dist',

  'dev': [
    'build-dev',
    'browserSync',
    'watch'
  ],

  'default': 'build'

};
