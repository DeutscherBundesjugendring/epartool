module.exports = function(grunt) {

    grunt.initConfig({

		// Compile LESS
        less: {
            development: {
				files: {
					'www/css/dbjr.css': 'www/less/main.less'
				}
            },
            production: {
                options: {
                    yuicompress: true
                },
				files: {
					'www/css/bootstrap.min.css': 'www/components/bootstrap/less/bootstrap.less',
					'www/css/dbjr.min.css': 'www/less/main.less'
				}
            },
			ej: {
				options: {
					yuicompress: true
				},
				files: {
					'/projects/sd/css/style.css': 'projects/sd/less/style.less'
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
					'www/components/bootstrap/js/bootstrap-affix.js',
					'www/components/bootstrap/js/bootstrap-alert.js',
					'www/components/bootstrap/js/bootstrap-button.js',
					'www/components/bootstrap/js/bootstrap-carousel.js',
					'www/components/bootstrap/js/bootstrap-collapse.js',
					'www/components/bootstrap/js/bootstrap-dropdown.js',
					'www/components/bootstrap/js/bootstrap-modal.js',
					'www/components/bootstrap/js/bootstrap-scrollspy.js',
					'www/components/bootstrap/js/bootstrap-tab.js',
					'www/components/bootstrap/js/bootstrap-tooltip.js',
					'www/components/bootstrap/js/bootstrap-popover.js',
					'www/components/bootstrap/js/bootstrap-transition.js',
					'www/components/bootstrap/js/bootstrap-typeahead.js'
				],
				dest: 'www/js/bootstrap.js'
			},
			jqueryUi: {
				options: {
					separator: ';'
				},
				src: [
                    'www/components/jquery.ui/ui/jquery.ui.core.js',
                    'www/components/jquery.ui/ui/jquery.ui.widget.js',

                    'www/components/jquery.ui/ui/jquery.ui.position.js',
                    'www/components/jquery.ui/ui/jquery.ui.mouse.js',

                    'www/components/jquery.ui/ui/jquery.ui.draggable.js',
                    'www/components/jquery.ui/ui/jquery.ui.droppable.js',

                    'www/components/jquery.ui/ui/jquery.ui.accordion.js',
                    'www/components/jquery.ui/ui/jquery.ui.autocomplete.js',
                    'www/components/jquery.ui/ui/jquery.ui.button.js',
                    'www/components/jquery.ui/ui/jquery.ui.datepicker.js',
                    'www/components/jquery.ui/ui/jquery.ui.dialog.js',
                    'www/components/jquery.ui/ui/jquery.ui.menu.js',
                    'www/components/jquery.ui/ui/jquery.ui.progressbar.js',
                    'www/components/jquery.ui/ui/jquery.ui.resizable.js',
                    'www/components/jquery.ui/ui/jquery.ui.selectable.js',
                    'www/components/jquery.ui/ui/jquery.ui.slider.js',
                    'www/components/jquery.ui/ui/jquery.ui.sortable.js',
                    'www/components/jquery.ui/ui/jquery.ui.spinner.js',
                    'www/components/jquery.ui/ui/jquery.ui.tabs.js',
                    'www/components/jquery.ui/ui/jquery.ui.tooltip.js',

                    'www/components/jquery.ui/ui/jquery.ui.effect.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-blind.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-bounce.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-clip.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-drop.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-explode.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-fade.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-fold.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-highlight.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-pulsate.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-scale.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-shake.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-slide.js',
                    'www/components/jquery.ui/ui/jquery.ui.effect-transfer.js'

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
                tasks: ['www/less']
            }
        }
    });

	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', [
		'less',
		'jshint',
		'concat',
		'uglify',
		'clean'
	]);
};
