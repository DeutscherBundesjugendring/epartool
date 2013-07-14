module.exports = function(grunt) {

    grunt.initConfig({

		// Compile LESS
        less: {
            development: {
				files: {
					'css/dbjr.css': 'less/main.less'
				}
            },
            production: {
                options: {
                    yuicompress: true
                },
				files: {
					'css/dbjr.min.css': 'less/main.less'
				}
            },
			ej: {
				options: {
					yuicompress: true
				},
				files: {
					'css/sd.css': 'less/alternative-design.less'
				}
			}
        },

		// Watch task
        watch: {
            less: {
                files: ['less/**/*.less'],
                tasks: ['less']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', 'less');
};
