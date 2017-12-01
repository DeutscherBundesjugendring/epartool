'use strict';

module.exports = function (grunt) {

  var options = {
    pkg: grunt.file.readJSON('package.json'),

    paths: {
      src: {
        front: 'assets/front',
        admin: 'assets/admin'
      },
      dist: 'www',
      bower: 'bower_components',
      npm: 'node_modules',
      temp: '.tmp'
    },

    devUrl: 'devel.localhost',
    devBrowser: 'google chrome'
  };

  require('time-grunt')(grunt);

  require('load-grunt-config')(grunt, { config: options });

  // See the `grunt/` directory for individual task configurations.
};
