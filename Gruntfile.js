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
      npm: 'node_modules',
      temp: '.tmp'
    },

    devUrl: 'devel.localhost',
  };

  require('time-grunt')(grunt);

  require('load-grunt-config')(grunt, { config: options });

  // See the `grunt/` directory for individual task configurations.
};
