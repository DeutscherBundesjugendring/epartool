'use strict';

module.exports = {

  fonts: {
    files: [
      {
        expand: true,
        cwd: '<%= paths.src.admin %>/fonts',
        src: ['**/*.{woff,woff2}'],
        dest: '<%= paths.dist %>/fonts'
      },
      {
        expand: true,
        cwd: '<%= paths.src.front %>/fonts',
        src: ['**/*.{eot,svg,ttf,woff}'],
        dest: '<%= paths.dist %>/fonts'
      },
      {
        expand: true,
        cwd: '<%= paths.npm %>/bootstrap/dist/fonts',
        src: ['*'],
        dest: '<%= paths.dist %>/fonts/glyphicons'
      }
    ]
  },
  components: {
    files: [
      {'<%= paths.dist %>/vendor/html5shiv.min.js': '<%= paths.npm %>/html5shiv/dist/html5shiv.min.js'},
      {
        expand: true,
        cwd: '<%= paths.npm %>/ckeditor',
        src: ['**/*'],
        dest: '<%= paths.dist %>/vendor/ckeditor'
      },
      {
        expand: true,
        cwd: '<%= paths.npm %>/select2',
        src: ['**/*'],
        dest: '<%= paths.dist %>/vendor/select2'
      },
      {
        expand: true,
        cwd: '<%= paths.npm %>/bootstrap-colorpicker',
        src: ['**/*'],
        dest: '<%= paths.dist %>/vendor/bootstrap-colorpicker'
      },
      {
        expand: true,
        cwd: '<%= paths.npm %>/leaflet/dist/images',
        src: ['**/*'],
        dest: '<%= paths.dist %>/images'
      }
    ]
  },
  admin: {
    files: [
      {'<%= paths.dist %>/js/ckeditor.web_config.js': '<%= paths.src.admin %>/js/ckeditor.web_config.js'},
      {'<%= paths.dist %>/js/ckeditor.email_config.js': '<%= paths.src.admin %>/js/ckeditor.email_config.js'},
      {
        expand: true,
        cwd: '<%= paths.src.admin %>/vendor/bootstrapCollapse',
        src: ['**/*'],
        dest: '<%= paths.dist %>/vendor/ckeditor/plugins/bootstrapCollapse',
      }
    ]
  },
  install: {
    files: [
      {
        expand: true,
        cwd: '<%= paths.npm %>/bootstrap.ui/dist/css',
        src: ['*'],
        dest: 'install/www/css'
      },
      {
        expand: true,
        cwd: '<%= paths.npm %>/bootstrap.ui/dist/fonts',
        src: ['*'],
        dest: 'install/www/fonts'
      },
      {
        expand: true,
        cwd: '<%= paths.npm %>/bootstrap.ui/src/images',
        src: ['*'],
        dest: 'install/www/images'
      },
    ]
  },

};
