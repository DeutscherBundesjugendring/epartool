module.exports = function (grunt) {

  // Autoload all tasks instead of grunt.loadNpmTasks(...)
  require('matchdep').filterAll('grunt-*').forEach(grunt.loadNpmTasks);

  grunt.initConfig({

    // Load meta info from package.json
    pkg: grunt.file.readJSON('package.json'),

    paths: {
      src: 'www/src',
      dist: 'www',
      bower: 'www/components/bower',
      temp: '.tmp'
    },

    // Compile LESS
    less: {
      dev: {
        options: {
          paths: ['<%= paths.bower %>']
        },
        files: {
          '<%= paths.dist %>/css/<%= pkg.name %>.css': '<%= paths.src %>/less/main.less',
          '<%= paths.dist %>/css/admin.css': '<%= paths.src %>/less/admin.less',
          '<%= paths.temp %>/mail.css': '<%= paths.src %>/less/mail.less'
        }
      },
      dist: {
        options: {
          paths: ['<%= paths.bower %>'],
          yuicompress: true
        },
        files: {
          '<%= paths.dist %>/css/bootstrap.min.css': '<%= paths.bower %>/bootstrap/less/bootstrap.less',
          '<%= paths.dist %>/css/<%= pkg.name %>.min.css': '<%= paths.src %>/less/main.less'
        }
      }
    },

    // Remove unused CSS
    uncss: {
      mail: {
        src: ['application/layouts/scripts/src/*.phtml'],
        dest: '<%= paths.temp %>/mail_clean.css',
        options: {
          report: 'min' // optional: include to report savings
        }
      }
    },

    // Process HTML
    processhtml: {
      mail: {
        files: {
          '<%= paths.temp %>/mail.phtml': ['application/layouts/scripts/src/mail.phtml']
        }
      }
    },

    // Inject inline CSS to mail templates from linked stylesheets.
    // Behold! Requires Premailer gem installed in the system (gem install premailer).
    premailer: {
      main: {
        options: {
          verbose: true
        },
        files: {
          'application/layouts/scripts/mail.phtml': ['<%= paths.temp %>/mail.phtml']
        }
      }
    },

    // Lint custom JS
    jshint: {
      files: ['<%= paths.src %>/js/main.js']
    },

    // Compile Coffee script
    coffee: {
      compile: {
        files: {
          '<%= paths.dist %>/js/admin.js': '<%= paths.src %>/coffee/admin.coffee',
          '<%= paths.dist %>/js/web.js': '<%= paths.src %>/coffee/web.coffee',
          '<%= paths.dist %>/js/admin_mediaPopup.js': '<%= paths.src %>/coffee/admin_mediaPopup.coffee'
        }
      }
    },

    // Prepare i18n json file
    po2json: {
      all: {
        options: {
          template: function (data) {
            return 'var exports = {};' + data;
          }
        },
        files: {
          '<%= paths.dist %>/js/i18n/admin.cs.js': ['languages/cs/admin-js.po'],
          '<%= paths.dist %>/js/i18n/admin.en.js': ['languages/en/admin-js.po'],
          '<%= paths.dist %>/js/i18n/admin.de.js': ['languages/de/admin-js.po'],
          '<%= paths.dist %>/js/i18n/admin.es.js': ['languages/es/admin-js.po'],
          '<%= paths.dist %>/js/i18n/admin.fr.js': ['languages/fr/admin-js.po']
        }
      }
    },

    // Concat all JS
    concat: {
      bootstrap: {
        options: {
          separator: ';'
        },
        src: [
          '<%= paths.bower %>/bootstrap/js/bootstrap-affix.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-alert.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-button.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-carousel.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-collapse.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-dropdown.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-modal.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-scrollspy.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-tab.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-tooltip.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-popover.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-transition.js',
          '<%= paths.bower %>/bootstrap/js/bootstrap-typeahead.js'
        ],
        dest: '<%= paths.dist %>/js/bootstrap.js'
      },
      jqueryUi: {
        options: {
          separator: ';'
        },
        src: [
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.core.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.widget.js',

          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.position.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.mouse.js',

          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.draggable.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.droppable.js',

          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.accordion.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.autocomplete.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.button.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.datepicker.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.dialog.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.menu.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.progressbar.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.resizable.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.selectable.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.slider.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.sortable.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.spinner.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.tabs.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.tooltip.js',

          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-blind.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-bounce.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-clip.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-drop.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-explode.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-fade.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-fold.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-highlight.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-pulsate.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-scale.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-shake.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-slide.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.effect-transfer.js'
        ],
        dest: '<%= paths.dist %>/js/jquery.ui.js'
      }
    },

    // Minify all JS
    uglify: {
      dist: {
        files: {
          '<%= paths.dist %>/js/bootstrap.min.js': ['<%= paths.dist %>/js/bootstrap.js'],
          '<%= paths.dist %>/js/jquery.ui.min.js': ['<%= paths.dist %>/js/jquery.ui.js']
        }
      }
    },

    // Clean temporary files
    clean: {
      css: '<%= paths.dist %>/css/*',
      js: '<%= paths.dist %>/js/*',
      //images: '<%= paths.dist %>/images/*',
      temp: '<%= paths.temp %>'
    },

    // Watch task
    watch: {
      less: {
        files: ['<%= paths.src %>/less/**/*.less'],
        tasks: ['build-css']
      },
      js: {
        files: [
          '<%= paths.src %>/coffee/*.coffee',
          '<%= paths.dist %>/js/**/*(!.min).js'
        ],
        tasks: ['build-js']
      }
    },

    // Inject updated templates and assets to connected browsers during development
    browserSync: {
      dev: {
        bsFiles: {
          src: [
            '<%= paths.dist %>/css/*.css',
            '<%= paths.dist %>/js/*.js',
            '<%= paths.dist %>/images/**/*.svg',
            '<%= paths.dist %>/images/**/*.png',
            '<%= paths.dist %>/images/**/*.jpg',
            'application/layouts/scripts/*.phtml',
            'application/modules/admin/views/**/*.phtml',
            'application/modules/default/views/**/*.phtml'
          ]
        },
        options: {
          proxy: 'dbjr-ip.local',
          watchTask: true
        }
      }
    }
  });

  // Build email phtml template.
  // WARNING: Task not executed by default as it requires Ruby and Premailer gem in the system.
  grunt.registerTask('mail', [
    'less:dev',
    'uncss:mail',
    'processhtml:mail',
    'premailer',
    'clean'
  ]);

  // Build subtasks
  grunt.registerTask('build-css', [
    'clean:css',
    'less'
  ]);

  grunt.registerTask('build-js', [
    'clean:js',
    'jshint',
    'coffee',
    'concat',
    'uglify',
    'po2json'
  ]);

  // Build task
  grunt.registerTask('build', [
    'clean:temp',
    'build-css',
    'build-js'
  ]);

  // Development
  grunt.registerTask('dev', [
    'build',
    'browserSync',
    'watch'
  ]);

  // Default task
  grunt.registerTask('default', 'build');
};
