'use strict';

module.exports = {

  dist: {
    files: {
      '<%= paths.dist %>/css/<%= pkg.name %>.min.css': '<%= paths.dist %>/css/<%= pkg.name %>.css',
      '<%= paths.dist %>/css/admin.min.css': '<%= paths.dist %>/css/admin.css'
    }
  }

};
