'use strict';

module.exports = {

  options: {
    processors: [
      require('autoprefixer')({
        browsers: [
          'Android 2.3',
          'Android >= 4',
          'Chrome >= 20',
          'Firefox ESR',
          'Explorer >= 8',
          'iOS >= 6',
          'Opera >= 12',
          'Safari >= 6',
        ],
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
