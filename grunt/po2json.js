'use strict';

module.exports = {

  admin: {
    options: {
      template: function (data) {
        return 'var exports = {};' + data;
      }
    },
    files: {
      '<%= paths.dist %>/js/admin.i18n.cs.js': ['languages/cs/admin-js.po'],
      '<%= paths.dist %>/js/admin.i18n.en.js': ['languages/en/admin-js.po'],
      '<%= paths.dist %>/js/admin.i18n.de.js': ['languages/de/admin-js.po'],
      '<%= paths.dist %>/js/admin.i18n.es.js': ['languages/es/admin-js.po'],
      '<%= paths.dist %>/js/admin.i18n.fr.js': ['languages/fr/admin-js.po']
    }
  }

};
