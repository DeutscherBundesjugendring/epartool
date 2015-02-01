module.exports = function(grunt) {

    // Autoload all tasks instead of grunt.loadNpmTasks(...)
    require('matchdep').filterAll('grunt-*').forEach(grunt.loadNpmTasks);

    grunt.initConfig({

        // Load meta info from package.json
        pkg: grunt.file.readJSON('package.json'),

        // Compile Coffee script
        coffee: {
            compile: {
                files: {
                    "assets/js/admin.js": "assets/coffee/admin.coffee",
                    "assets/js/web.js": "assets/coffee/web.coffee",
                }
            }
        },

        // Prepare i18n json file
        po2json: {
            all: {
                options: {
                    template: function(data) {
                        return 'var exports = {};' + data;
                    }
                },
                files: {
                    'www/js/admin.i18n.cs.js': ['languages/cs/admin-js.po'],
                    'www/js/admin.i18n.en.js': ['languages/en/admin-js.po'],
                    'www/js/admin.i18n.de.js': ['languages/de/admin-js.po'],
                    'www/js/admin.i18n.es.js': ['languages/es/admin-js.po'],
                    'www/js/admin.i18n.fr.js': ['languages/fr/admin-js.po']
                }
            }
        },

        // Compile LESS
        less: {
            dev: {
                files: {
                    'www/css/<%= pkg.name %>.css': 'assets/less/main.less',
                    'www/css/admin.css': 'assets/less/admin.less',
                    '.tmp/mail.css': 'www/less/mail.less'
                }
            },
            dist: {
                options: {
                    yuicompress: true
                },
                files: {
                    'www/css/bootstrap.min.css': 'assets/components/bower/bootstrap/less/bootstrap.less',
                    'www/css/<%= pkg.name %>.min.css': 'assets/less/main.less'
                }
            }
        },

        // Lint custom JS
        jshint: {
            files: ['www/js/main.js']
        },

        // Concat all JS
        concat: {
            options: {
                separator: ';',
                sourceMap: true,
            },
            admin: {
                src: [
                    'assets/components/bower/jquery/jquery.min.js',
                    'assets/components/bower/moment/min/moment-with-locales.min.js',
                    'assets/components/bower/select2/select2.min.js',
                    'assets/components/bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
                    'assets/components/static/ckeditor/ckeditor.js',
                    'assets/components/static/ckeditor/adapters/jquery.js',
                    'assets/components/bower/bootstrap3/js/alert.js',
                    'assets/components/bower/bootstrap3/js/collapse.js',
                    'assets/components/bower/bootstrap3/js/modal.js',
                    'assets/components/bower/bootstrap3/js/tooltip.js',
                    'assets/components/bower/bootstrap3/js/transition.js',
                    'assets/components/bower/jquery.ui/ui/jquery.ui.core.js',
                    'assets/components/bower/jquery.ui/ui/jquery.ui.widget.js',
                    'assets/components/bower/jquery.ui/ui/jquery.ui.mouse.js',
                    'assets/components/bower/jquery.ui/ui/jquery.ui.sortable.js',
                    'assets/components/bower/synergic-ui/src/js/confirmation.js',
                    'assets/components/bower/synergic-ui/src/js/disable.js',
                    'assets/components/bower/synergic-ui/src/js/filterable.js',
                    'assets/components/bower/synergic-ui/src/js/sortable-table.js',
                    'assets/js/admin.js'
                ],
                dest: 'www/js/admin.js'
            },
            web: {
                src: [
                    'assets/components/bower/jquery/jquery.min.js',
                    'assets/components/bower/pwstrength-bootstrap/dist/pwstrength-bootstrap-1.1.5.js',
                    'assets/components/bower/bootstrap/js/bootstrap-alert.js',
                    'assets/components/bower/bootstrap/js/bootstrap-dropdown.js',
                    'assets/components/bower/bootstrap/js/bootstrap-transition.js',
                    'assets/js/FollowUp.js',
                    'assets/js/SocialShare.js',
                    'assets/js/web.js'
                ],
                dest: 'www/js/web.js'
            }
        },

        // Minify all JS
        uglify: {
            dist: {
                files: {
                    'www/js/web.min.js': ['www/js/web.js'],
                    'www/js/admin.min.js': ['www/js/admin.js']
                }
            }
        },

        phplint: {
          all: ['application/**/*.php', 'application/**/*.phtml']
        },

        // Remove unused CSS
        uncss: {
            mail: {
                src: ['application/layouts/scripts/src/*.phtml'],
                dest: '.tmp/mail_clean.css',
                options: {
                    report: 'min' // optional: include to report savings
                }
            }
        },

        // Process HTML
        processhtml: {
            mail: {
                files: {
                    '.tmp/mail.phtml': ['application/layouts/scripts/src/mail.phtml']
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
                    'application/layouts/scripts/mail.phtml': ['.tmp/mail.phtml']
                }
            }
        },

        // Clean temporary files
        clean: [
            '.tmp'
        ],

        // Watch task
        watch: {
            less: {
                files: ['assets/less/**/*.less'],
                tasks: ['less']
            },
            coffee: {
                files: ['assets/coffee/**/*.coffee'],
                tasks: ['coffee']
            },
            js: {
                files: ['assets/js/**/*.js'],
                tasks: ['jshint']
            }
        },

        // Inject updated templates and assets to connected browsers during development
        browserSync: {
            dev: {
                bsFiles: {
                    src : [
                        'www/css/*.css',
                        'www/js/*.js',
                        'www/images/**/*.svg',
                        'www/images/**/*.png',
                        'www/images/**/*.jpg',
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

    // Default task
    grunt.registerTask('build', [
        'phplint',
        'po2json',
        'less',
        'coffee',
        'jshint',
        'concat',
        'uglify',
        'clean'
    ]);

    grunt.registerTask('dev', [
        'build',
        'browserSync',
        'watch'
    ]);

    grunt.registerTask('default', 'build');
};
