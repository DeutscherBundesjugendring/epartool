module.exports = function(grunt) {

    grunt.initConfig({
        recess: {
            compile: {
                src: ['public/less/main.less'],
                dest: 'public/css/dbjr.css',
                options: {
                    compile: true
                }
            },
            dist: {
                src: ['public/less/main.less'],
                dest: 'public/css/dbjr.min.css',
                options: {
                    compile: true,
                    compress: true
                }
            },
			ej: {
				src: ['public/less/alternative-design.less'],
				dest: 'public/css/ej.css',
				options: {
					compile: true,
					compress: true
				}
			}
        },
        watch: {
            less: {
                files: ['public/less/*.less'],
                tasks: 'recess'
            }
        }
    });

    grunt.registerTask('default', 'recess');

    grunt.loadNpmTasks('grunt-recess');
};
