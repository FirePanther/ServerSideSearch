/**
 * Watch the source files, on change compress them into a single file.
 *
 * @author           Suat Secmen (http://suat.be)
 * @copyright        2016 Suat Secmen
 * @license          MIT License
 */

module.exports = grunt => {
	grunt.loadNpmTasks('grunt-shell');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-sass');
	
	grunt.initConfig({
		shell: {
			concat: {
				command: 'php -f concat.php'
			}
		},
		sass: {
			dist: {
				options: {
					style: 'compressed',
					sourcemap: 'none'
				},
				files: {
					'src/style.css': 'src/style.scss'
				}
			}
		},
		watch: {
			options: {
				interrupt: true
			},
			php: {
				files: 'src/*.php',
				tasks: 'shell:concat',
			},
			scss: {
				files: 'src/*.scss',
				tasks: ['sass', 'shell:concat'],
			}
		}
	});
	
	grunt.registerTask('default', ['sass', 'shell:concat']);
};