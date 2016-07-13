'use strict';

module.exports = {

  admin: {
    files: [
      {
        expand: true,
        cwd: '<%= paths.src.admin %>/images/',
        src: ['**/*.{png,gif}'],
        dest: '<%= paths.dist %>/images/admin/'
      }, {
        expand: true,
        cwd: '<%= paths.bower %>/bootstrap.ui/src/images/',
        src: ['*.svg'],
        dest: '<%= paths.dist %>/images/admin/'
      }
    ]
  },
  front: {
    files: [
      {
        expand: true,
        cwd: '<%= paths.src.front %>/images/',
        src: ['**/*.{jpg,png,gif}'],
        dest: '<%= paths.dist %>/images/'
      }
    ]
  },

};
