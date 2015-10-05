'use strict';

module.exports = {

  front: {
    files: [{
      expand: true,
      cwd: '<%= paths.src.front %>/images/',
      src: ['**/*.{jpg,png,gif}'],
      dest: '<%= paths.dist %>/images/'
    }]
  },
  admin: {
    files: [{
      expand: true,
      cwd: '<%= paths.src.admin %>/images/',
      src: ['**/*.{png,ico}'],
      dest: '<%= paths.dist %>/images/admin/'
    }]
  }

};