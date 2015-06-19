'use strict';

module.exports = {

  main: {
    options: {
      verbose: true
    },
    files: {
      '<%= paths.temp %>/mail_inline_css.html': ['<%= paths.temp %>/mail.html']
    }
  }

};
