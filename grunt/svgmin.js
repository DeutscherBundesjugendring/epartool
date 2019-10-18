'use strict';

module.exports = {

  front: {
    files: [{
      expand: true,
      cwd: '<%= paths.src.front %>/images/',
      src: ['*.svg'],
      dest: '<%= paths.dist %>/images/'
    }]
  },
  admin: {
    files: [{
      expand: true,
      cwd: '<%= paths.src.admin %>/images/',
      src: ['*.svg'],
      dest: '<%= paths.dist %>/images/admin/'
    }, {
      expand: true,
      cwd: '<%= paths.npm %>/bootstrap.ui/src/images/',
      src: ['*.svg'],
      dest: '<%= paths.dist %>/images/admin/'
    }]
  }

};
