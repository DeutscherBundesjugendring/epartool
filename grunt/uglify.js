'use strict';

module.exports = {

  front: {
    files: {
      '<%= paths.dist %>/js/<%= pkg.name %>.min.js': '<%= paths.dist %>/js/<%= pkg.name %>.js'
    }
  },
  admin: {
    files: {
      '<%= paths.dist %>/js/admin.min.js': '<%= paths.dist %>/js/admin.js'
    }
  }

};
