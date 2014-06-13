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
                    "www/js/web.js": "www/coffee/web.coffee"
                }
            }
        },


		// Compile LESS
        less: {
            dev: {
				files: {
					'www/css/<%= pkg.name %>.css': 'www/less/main.less'
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

		// Clean temporary files
		clean: [
			'www/js/bootstrap.js',
			'www/js/jquery.ui.js'
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
        }
    });

	grunt.registerTask('default', [
		'less',
        'coffee',
		'jshint',
		'concat',
		'uglify',
		'clean'
	]);
};
