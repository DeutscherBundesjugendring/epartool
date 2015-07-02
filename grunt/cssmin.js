'use strict';

module.exports = {

  options: {
    restructuring: false
  },
  admin: {
    files: {
      '<%= paths.dist %>/css/admin.min.css': '<%= paths.dist %>/css/admin.css'
    }
  }

};
