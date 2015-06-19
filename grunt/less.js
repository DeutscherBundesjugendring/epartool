'use strict';

module.exports = {

  options: {
    paths: ['<%= paths.bower %>']
  },
  front: {
    files: {
      '<%= paths.dist %>/css/<%= pkg.name %>.css': '<%= paths.src %>/less/main.less'
    }
  },
  admin: {
    files: {
      '<%= paths.dist %>/css/admin.css': '<%= paths.src %>/less/admin.less'
    }
  },
  mail: {
    files: {
      '<%= paths.temp %>/mail.css': '<%= paths.src %>/less/mail.less'
    }
  }

};
