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
					'www/css/sd.css': 'www/less/alternative-design.less'
				}
			}
        },

		// Lint custom JS
		jshint: {
			files: ['www/js/main.js']
		},

		// Concat all JS
		concat: {
			dist: {
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
			}
		},

		// Minify all JS
		uglify: {
			dist: {
				files: {
					'www/js/bootstrap.min.js': ['www/js/bootstrap.js']
				}
			}
		},

		// Clean temporary files
		clean: [
			'www/js/bootstrap.js'
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
		//'jshint',
		'concat',
		'uglify',
		'clean'
	]);
};
