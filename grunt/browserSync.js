'use strict';

module.exports = {

  dev: {
    bsFiles: {
      src: [
        '<%= paths.dist %>/css/*.css',
        '<%= paths.dist %>/js/*.js',
        '<%= paths.dist %>/images/**/*.svg',
        '<%= paths.dist %>/images/**/*.png',
        '<%= paths.dist %>/images/**/*.jpg',
        'application/layouts/scripts/*.phtml',
        'application/modules/admin/views/**/*.phtml',
        'application/modules/default/views/**/*.phtml'
      ]
    },
    options: {
      proxy: '<%= devUrl %>',
      watchTask: true,
      browser: '<%= devBrowser %>',
      snippetOptions: {
        rule: {
          match: /<body[^>]*>/i,
          fn: function (snippet, match) {
            if (match === '<body id=\\"tracy-debug\\">') {
              return match
            }
            return match + snippet;
          }
        }
      }
    }
  }

};
