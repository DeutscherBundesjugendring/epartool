'use strict';

module.exports = {

  mail: {
    options: {
      verbose: true
    },
    files: {
      '<%= paths.temp %>/mail_inline_css.html': '<%= paths.temp %>/mail.html'
    }
  }

};
