'use strict';

module.exports = {

  options: {
    processors: [
      require('autoprefixer')({
        map: true,
      }),
      require('cssnano')({
        autoprefixer: false,
        mergeRules: false,
        zindex: false,
      }),
    ],
  },
  admin: {
    src: '<%= paths.temp %>/admin.css',
    dest: '<%= paths.dist %>/css/admin.min.css',
  },
  front: {
    src: '<%= paths.temp %>/front.css',
    dest: '<%= paths.dist %>/css/front.min.css',
  },

};
