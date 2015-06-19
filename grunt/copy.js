'use strict';

module.exports = {

  fonts: {
    files: [
      {
        expand: true,
        cwd: '<%= paths.src %>/fonts',
        src: ['**/*.{eot,svg,ttf,woff}'],
        dest: '<%= paths.dist %>/font'
      },
      {
        expand: true,
        cwd: '<%= paths.bower %>/bootstrap/dist/fonts',
        src: ['*'],
        dest: '<%= paths.dist %>/font/glyphicons'
      }
    ]
  },
  bower: {
    files: [
      {'<%= paths.dist %>/vendor/html5shiv.min.js': '<%= paths.bower %>/html5shiv/dist/html5shiv.min.js'},
      {
        expand: true,
        cwd: '<%= paths.bower %>/ckeditor',
        src: ['**/*'],
        dest: '<%= paths.dist %>/vendor/ckeditor'
      }
    ]
  },
  js: {
    files: [
      {'<%= paths.dist %>/js/ckeditor.web_config.js': '<%= paths.src %>/js/ckeditor.web_config.js'},
      {'<%= paths.dist %>/js/ckeditor.email_config.js': '<%= paths.src %>/js/ckeditor.email_config.js'},
    ]
  }

};
