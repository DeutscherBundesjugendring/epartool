'use strict';

module.exports = {

  options: {
    browsers: [
      'Android 2.3',
      'Android >= 4',
      'Chrome >= 20',
      'Firefox >= 24', // Firefox 24 is the latest ESR
      'Explorer >= 8',
      'iOS >= 6',
      'Opera >= 12',
      'Safari >= 6'
    ],
    map: true
  },
  admin: {
    src: '<%= paths.dist %>/css/admin.css'
  },
  front: {
    src: '<%= paths.dist %>/css/front.css'
  },

};
