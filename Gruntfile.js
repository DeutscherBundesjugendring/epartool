module.exports = function (grunt) {

  // Autoload all tasks instead of grunt.loadNpmTasks(...)
  require('matchdep').filterAll('grunt-*').forEach(grunt.loadNpmTasks);

  grunt.initConfig({

    // Load meta info from package.json
    pkg: grunt.file.readJSON('package.json'),

    paths: {
      src: 'assets',
      dist: 'www',
      bower: 'bower_components',
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
          '<%= paths.dist %>/css/admin.css': [
              '<%= paths.src %>/less/admin.less'
              //'<%= paths.bower %>/jquery.ui/themes/base/jquery.ui.core.css'
            ],
          '<%= paths.temp %>/mail.css': '<%= paths.src %>/less/mail.less'
        }
      },
      dist: {
        options: {
          paths: ['<%= paths.bower %>'],
          yuicompress: true
        },
        files: {
          '<%= paths.dist %>/css/<%= pkg.name %>.min.css': '<%= paths.src %>/less/main.less',
          '<%= paths.dist %>/css/admin.min.css': '<%= paths.src %>/less/admin.less'
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

    // Lint PHP templates
    phplint: {
      all: [
        'application/**/*.php',
        'application/**/*.phtml'
      ]
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
    /*jshint: {
      files: ['<%= paths.src %>/js/main.js']
    },*/

    // Compile Coffee script
    coffee: {
      compile: {
        files: {
          '<%= paths.temp %>/web.js': '<%= paths.src %>/coffee/web.coffee',
          '<%= paths.temp %>/admin.js': '<%= paths.src %>/coffee/admin.coffee'
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
          '<%= paths.dist %>/js/admin.i18n.cs.js': ['languages/cs/admin-js.po'],
          '<%= paths.dist %>/js/admin.i18n.en.js': ['languages/en/admin-js.po'],
          '<%= paths.dist %>/js/admin.i18n.de.js': ['languages/de/admin-js.po'],
          '<%= paths.dist %>/js/admin.i18n.es.js': ['languages/es/admin-js.po'],
          '<%= paths.dist %>/js/admin.i18n.fr.js': ['languages/fr/admin-js.po']
        }
      }
    },

    // Concat all JS
    concat: {
      options: {
        separator: ';',
        sourceMap: true
      },
      web: {
        src: [
          '<%= paths.bower %>/jquery/jquery.min.js',
          '<%= paths.bower %>/pwstrength-bootstrap/dist/pwstrength-bootstrap-1.1.5.js',
          '<%= paths.bower %>/bootstrap/js/alert.js',
          '<%= paths.bower %>/bootstrap/js/dropdown.js',
          '<%= paths.bower %>/bootstrap/js/transition.js',
          '<%= paths.src %>/js/FollowUp.js',
          '<%= paths.src %>/js/SocialShare.js',
          //'<%= paths.src %>/js/main.js',
          '<%= paths.temp %>/web.js'
        ],
        dest: '<%= paths.dist %>/js/web.js'
      },
      admin: {
        src: [
          '<%= paths.bower %>/jquery/jquery.min.js',
          '<%= paths.bower %>/moment/min/moment-with-locales.min.js',
          '<%= paths.bower %>/select2/select2.min.js',
          '<%= paths.bower %>/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
          '<%= paths.src %>/vendor/ckeditor/ckeditor.js',
          '<%= paths.src %>/vendor/ckeditor/adapters/jquery.js',
          '<%= paths.bower %>/bootstrap/js/alert.js',
          '<%= paths.bower %>/bootstrap/js/collapse.js',
          '<%= paths.bower %>/bootstrap/js/dropdown.js',
          '<%= paths.bower %>/bootstrap/js/modal.js',
          '<%= paths.bower %>/bootstrap/js/tooltip.js',
          '<%= paths.bower %>/bootstrap/js/transition.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.core.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.widget.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.mouse.js',
          '<%= paths.bower %>/jquery.ui/ui/jquery.ui.sortable.js',
          '<%= paths.bower %>/synergic-ui/src/js/confirmation.js',
          '<%= paths.bower %>/synergic-ui/src/js/disable.js',
          '<%= paths.bower %>/synergic-ui/src/js/filterable.js',
          '<%= paths.bower %>/synergic-ui/src/js/sortable-table.js',
          '<%= paths.temp %>/admin.js'
        ],
        dest: '<%= paths.dist %>/js/admin.js'
      }
    },

    // Minify all JS
    uglify: {
      dist: {
        files: {
          '<%= paths.dist %>/js/web.min.js': ['<%= paths.dist %>/js/web.js'],
          '<%= paths.dist %>/js/admin.min.js': ['<%= paths.dist %>/js/admin.js']
        }
      }
    },

    // Copy files
    copy: {
      fonts: {
        files: [
          {
            expand: true,
            cwd: '<%= paths.src %>/fonts',
            src: ['**/*.{eot,svg,ttf,woff}'],
            dest: '<%= paths.dist %>/font'
          },
          {
            expand: true,
            cwd: '<%= paths.bower %>/bootstrap/dist/fonts',
            src: ['*'],
            dest: '<%= paths.dist %>/font/glyphicons'
          }
        ]
      },
      bower: {
        files: {
          '<%= paths.dist %>/js/html5shiv.min.js': '<%= paths.bower %>/html5shiv/dist/html5shiv.min.js'
        }
      },
      vendor: {
        files: [
          {
            expand: true,
            cwd: '<%= paths.src %>/vendor',
            src: ['**/*'],
            dest: '<%= paths.dist %>/vendor'
          }
        ]
      }
    },

    // Clean temporary files
    clean: {
      css: '<%= paths.dist %>/css/*',
      js: '<%= paths.dist %>/js/*',
      fonts: '<%= paths.dist %>/font/*',
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
          '<%= paths.src %>/js/*.js'
        ],
        tasks: ['build-js']
      },
      php: {
        files: [
          'application/**/*.php',
          'application/**/*.phtml'
        ],
        tasks: ['phplint']
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
          watchTask: true,
          snippetOptions: {
            rule: {
              match: /<body[^>]*>/i,
              fn: function (snippet, match) {
                if (match === '<body id=\\"tracy-debug\\">') {
                  return match
                }
                return match + snippet;
              }
            }
          }
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
    //'jshint',
    'coffee',
    'concat',
    'uglify',
    'po2json'
  ]);

  // Build task
  grunt.registerTask('build', [
    'clean:temp',
    'clean:fonts',
    'phplint',
    'build-css',
    'build-js',
    'copy'
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
