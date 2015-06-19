'use strict';

module.exports = {

  mail: {
    src: '<%= paths.temp %>/mail_inline_css.html',
    dest: 'application/layouts/scripts/mail.phtml',
    replacements: [
      {
        from: '<!-- <?',
        to: '<?'
      },
      {
        from: '?> -->',
        to: '?>'
      }
    ]
  }

};
