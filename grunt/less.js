'use strict';

module.exports = {

  options: {
    paths: ['<%= paths.bower %>'],
    sourceMap: true
  },
  front: {
    files: {
      '<%= paths.dist %>/css/<%= pkg.name %>.css': '<%= paths.src.front %>/less/main.less'
    }
  },
  admin: {
    files: {
      '<%= paths.dist %>/css/admin.css': '<%= paths.src.admin %>/less/main.less'
    }
  },
  mail: {
    files: {
      '<%= paths.temp %>/mail.css': '<%= paths.src.front %>/less/mail.less'
    }
  }

};
