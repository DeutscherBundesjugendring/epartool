'use strict';

module.exports = {

  options: {
    restructuring: false
  },
  front: {
    files: {
      '<%= paths.dist %>/css/<%= pkg.name %>.min.css': '<%= paths.dist %>/css/<%= pkg.name %>.css'
    }
  },
  admin: {
    files: {
      '<%= paths.dist %>/css/admin.min.css': '<%= paths.dist %>/css/admin.css'
    }
  }

};
