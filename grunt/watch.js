'use strict';

module.exports = {

  'less-admin': {
    files: ['<%= paths.src.admin %>/less/**/*.less'],
    tasks: ['build-css-admin']
  },
  'js-front': {
    files: [
      '<%= paths.src.front %>/coffee/*.coffee',
      '<%= paths.src.front %>/js/*.js'
    ],
    tasks: ['build-js-front']
  },
  'js-admin': {
    files: [
      '<%= paths.src.admin %>/coffee/*.coffee',
      '<%= paths.src.admin %>/js/*.js'
    ],
    tasks: ['build-js-admin']
  },
  php: {
    files: [
      'application/**/*.php',
      'application/**/*.phtml'
    ],
    tasks: ['test-php']
  }

};
