'use strict';

module.exports = {

  options: {
    paths: ['<%= paths.bower %>'],
    sourceMap: true
  },
  admin: {
    options: {
      rootpath: '<%= paths.projectToCore %>'
    },
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
