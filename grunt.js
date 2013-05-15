module.exports = function(grunt) {

    grunt.initConfig({
        recess: {
            compile: {
                src: ['less/main.less'],
                dest: 'css/dbjr.css',
                options: {
                    compile: true
                }
            },
            dist: {
                src: ['less/main.less'],
                dest: 'css/dbjr.min.css',
                options: {
                    compile: true,
                    compress: true
                }
            },
			ej: {
				src: ['less/alternative-design.less'],
				dest: 'css/sd.css',
				options: {
					compile: true,
					compress: true
				}
			}
        },
        watch: {
            less: {
                files: ['less/*.less'],
                tasks: 'recess'
            }
        }
    });

    grunt.registerTask('default', 'recess');

    grunt.loadNpmTasks('grunt-recess');
};
