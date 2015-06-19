'use strict';

module.exports = {

  front: {
    files: {
      '<%= paths.temp %>/front.js': '<%= paths.src.front %>/coffee/main.coffee'
    }
  },
  admin: {
    files: {
      '<%= paths.temp %>/admin.js': '<%= paths.src.admin %>/coffee/main.coffee'
    }
  }

};
