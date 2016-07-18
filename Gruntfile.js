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
      temp: '.tmp'
    },

    devUrl: 'dbjr.local',
    devBrowser: 'google chrome'
  };

  require('time-grunt')(grunt);

  require('load-grunt-config')(grunt, { config: options });

  // See the `grunt/` directory for individual task configurations.

  grunt.initConfig({

    // Compile styles
    less: {
      core: {
        options: {
          paths: [
            'assets/front/less',
            'bower_components'
          ],
          rootpath: ''
        },
        files: {
          'project/css/core.css': 'project/less/core.less'
        }
      },
      custom: {
        files: {
          'project/css/custom.css': 'project/less/custom.less'
        }
      },
      front: {
        files: {
          'project/css/style.css': [
            'project/css/core.css',
            'project/css/custom.css'
          ]
        }
      }
    },

    autoprefixer: {
      options: {
        browsers: [
          'Android 2.3',
          'Android >= 4',
          'Chrome >= 20',
          'Firefox >= 24', // Firefox 24 is the latest ESR
          'Explorer >= 8',
          'iOS >= 6',
          'Opera >= 12',
          'Safari >= 6'
        ]
      },
      front: {
        src: 'project/css/style.css'
      }
    },

    cssmin: {
      options: {
        restructuring: false
      },
      front: {
        files: {
          'project/css/style.min.css': 'project/css/style.css'
        }
      }
    },

    // Watch task
    watch: {
      less: {
        files: [
          'assets/front/less/**/*.less',
          'project/less/*.less'
        ],
        tasks: ['build-css']
      }
    }
  });

  // Load tasks
  grunt.loadNpmTasks('grunt-autoprefixer');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Aliases
  grunt.registerTask('build-css', [
    'less',
    'autoprefixer',
    'cssmin'
  ]);

  grunt.registerTask('default', 'build-css');
};
