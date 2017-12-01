'use strict';

module.exports = {

  options: {
    paths: [
      '<%= paths.bower %>',
      '<%= paths.npm %>',
    ],
  },
  admin: {
    files: {
      '<%= paths.temp %>/admin.css': '<%= paths.src.admin %>/less/main.less'
    }
  },
  front: {
    files: {
      '<%= paths.temp %>/front.css': '<%= paths.src.front %>/less/main.less'
    }
  },
  mail: {
    files: {
      '<%= paths.temp %>/mail.css': '<%= paths.src.front %>/less/mail.less'
    }
  },
};
