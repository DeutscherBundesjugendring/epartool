'use strict';

module.exports = {

  front: {
    files: {
      '<%= paths.temp %>/web.js': '<%= paths.src %>/coffee/web.coffee'
    }
  },
  admin: {
    files: {
      '<%= paths.temp %>/admin.js': '<%= paths.src %>/coffee/admin.coffee'
    }
  }

};
