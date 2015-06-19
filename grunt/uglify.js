'use strict';

module.exports = {

  front: {
    files: {
      '<%= paths.dist %>/js/web.min.js': '<%= paths.dist %>/js/web.js'
    }
  },
  admin: {
    files: {
      '<%= paths.dist %>/js/admin.min.js': '<%= paths.dist %>/js/admin.js'
    }
  }

};
