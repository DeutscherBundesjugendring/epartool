'use strict';

module.exports = {

  compile: {
    files: {
      '<%= paths.temp %>/web.js': '<%= paths.src %>/coffee/web.coffee',
      '<%= paths.temp %>/admin.js': '<%= paths.src %>/coffee/admin.coffee'
    }
  }

};
