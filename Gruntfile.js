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
                    "www/js/admin.js": "www/coffee/admin.coffee",
                    "www/js/web.js": "www/coffee/web.coffee",
                    "www/js/admin_mediaPopup.js": "www/coffee/admin_mediaPopup.coffee"
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
                    'www/js/i18n/admin.cs.js': ['languages/cs/admin-js.po'],
                    'www/js/i18n/admin.en.js': ['languages/en/admin-js.po'],
                    'www/js/i18n/admin.de.js': ['languages/de/admin-js.po'],
                    'www/js/i18n/admin.es.js': ['languages/es/admin-js.po'],
                    'www/js/i18n/admin.fr.js': ['languages/fr/admin-js.po']
                }
            }
        },

        // Compile LESS
        less: {
            dev: {
                files: {
                    'www/css/<%= pkg.name %>.css': 'www/less/main.less',
                    'www/css/admin.css': 'www/less/admin.less',
                    '.tmp/mail.css': 'www/less/mail.less'
                }
            },
            dist: {
                options: {
                    yuicompress: true
                },
                files: {
                    'www/css/bootstrap.min.css': 'www/components/bower/bootstrap/less/bootstrap.less',
                    'www/css/<%= pkg.name %>.min.css': 'www/less/main.less'
                }
            }
        },

        // Lint custom JS
        jshint: {
            files: ['www/js/main.js']
        },

        // Concat all JS
        concat: {
            bootstrap: {
                options: {
                    separator: ';'
                },
                src: [
                    'www/components/bower/bootstrap/js/bootstrap-affix.js',
                    'www/components/bower/bootstrap/js/bootstrap-alert.js',
                    'www/components/bower/bootstrap/js/bootstrap-button.js',
                    'www/components/bower/bootstrap/js/bootstrap-carousel.js',
                    'www/components/bower/bootstrap/js/bootstrap-collapse.js',
                    'www/components/bower/bootstrap/js/bootstrap-dropdown.js',
                    'www/components/bower/bootstrap/js/bootstrap-modal.js',
                    'www/components/bower/bootstrap/js/bootstrap-scrollspy.js',
                    'www/components/bower/bootstrap/js/bootstrap-tab.js',
                    'www/components/bower/bootstrap/js/bootstrap-tooltip.js',
                    'www/components/bower/bootstrap/js/bootstrap-popover.js',
                    'www/components/bower/bootstrap/js/bootstrap-transition.js',
                    'www/components/bower/bootstrap/js/bootstrap-typeahead.js'
                ],
                dest: 'www/js/bootstrap.js'
            },
            jqueryUi: {
                options: {
                    separator: ';'
                },
                src: [
                    'www/components/bower/jquery.ui/ui/jquery.ui.core.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.widget.js',

                    'www/components/bower/jquery.ui/ui/jquery.ui.position.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.mouse.js',

                    'www/components/bower/jquery.ui/ui/jquery.ui.draggable.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.droppable.js',

                    'www/components/bower/jquery.ui/ui/jquery.ui.accordion.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.autocomplete.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.button.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.datepicker.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.dialog.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.menu.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.progressbar.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.resizable.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.selectable.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.slider.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.sortable.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.spinner.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.tabs.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.tooltip.js',

                    'www/components/bower/jquery.ui/ui/jquery.ui.effect.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-blind.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-bounce.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-clip.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-drop.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-explode.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-fade.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-fold.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-highlight.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-pulsate.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-scale.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-shake.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-slide.js',
                    'www/components/bower/jquery.ui/ui/jquery.ui.effect-transfer.js'
                ],
                dest: 'www/js/jquery.ui.js'
            }
        },

        // Minify all JS
        uglify: {
            dist: {
                files: {
                    'www/js/bootstrap.min.js': ['www/js/bootstrap.js'],
                    'www/js/jquery.ui.min.js': ['www/js/jquery.ui.js']
                }
            }
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
            'www/js/bootstrap.js',
            'www/js/jquery.ui.js',
            '.tmp'
        ],

        // Watch task
        watch: {
            less: {
                files: ['www/less/**/*.less'],
                tasks: ['less']
            },
            coffee: {
                files: ['www/coffee/**/*.coffee'],
                tasks: ['coffee']
            },
            js: {
                files: ['www/js/**/*(!.min).js'],
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
