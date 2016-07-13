'use strict';

module.exports = {

  options: {
    paths: ['<%= paths.bower %>'],
    sourceMap: true
  },
  admin: {
    options: {
      rootpath: '../'
    },
    files: {
      '<%= paths.temp %>/admin.css': '<%= paths.src.admin %>/less/main.less'
    }
  },
  front: {
    options: {
      rootpath: '../'
    },
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
