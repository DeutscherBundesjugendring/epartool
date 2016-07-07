'use strict';

module.exports = {

  front: {
    files: [{
      expand: true,
      cwd: '<%= paths.src.front %>/images/',
      src: ['**/*.{jpg,png,gif,ico}'],
      dest: '<%= paths.dist %>/images/'
    }]
  },
  admin: {
    files: [{
      expand: true,
      cwd: '<%= paths.src.admin %>/images/',
      src: ['**/*.{png,gif,ico}'],
      dest: '<%= paths.dist %>/images/admin/'
    }]
  }

};
