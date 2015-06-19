'use strict';

module.exports = function (grunt) {

  var options = {
    pkg: grunt.file.readJSON('package.json'),

    paths: {
      src: 'assets',
      dist: 'www',
      bower: 'bower_components',
      temp: '.tmp'
    },

    devUrl: 'dbjr-ip.local'
  };

  require('time-grunt')(grunt);

  require('load-grunt-config')(grunt, { config: options });

  // See the `grunt/` directory for individual task configurations.
};
