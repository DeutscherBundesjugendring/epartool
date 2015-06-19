'use strict';

module.exports = {

  less: {
    files: ['<%= paths.src %>/less/**/*.less'],
    tasks: ['build-css-dev']
  },
  js: {
    files: [
      '<%= paths.src %>/coffee/*.coffee',
      '<%= paths.src %>/js/*.js'
    ],
    tasks: ['build-js-dev']
  },
  php: {
    files: [
      'application/**/*.php',
      'application/**/*.phtml'
    ],
    tasks: ['phplint']
  }

};
