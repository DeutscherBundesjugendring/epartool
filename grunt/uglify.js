'use strict';

module.exports = {

  dist: {
    files: {
      '<%= paths.dist %>/js/web.min.js': ['<%= paths.dist %>/js/web.js'],
      '<%= paths.dist %>/js/admin.min.js': ['<%= paths.dist %>/js/admin.js']
    }
  }

};
