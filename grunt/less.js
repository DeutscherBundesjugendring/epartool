'use strict';

module.exports = {

  dev: {
    options: {
      paths: ['<%= paths.bower %>']
    },
    files: {
      '<%= paths.dist %>/css/<%= pkg.name %>.css': '<%= paths.src %>/less/main.less',
      '<%= paths.dist %>/css/admin.css': '<%= paths.src %>/less/admin.less'
    }
  },
  mail: {
    files: {
      '<%= paths.temp %>/mail.css': '<%= paths.src %>/less/mail.less'
    }
  }

};
