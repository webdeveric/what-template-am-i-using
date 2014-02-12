module.exports = function(grunt){
	'use strict';
	grunt.initConfig( {
		pkg: grunt.file.readJSON('package.json'),
		jshint: {
			files: ['Gruntfile.js', 'js/src/**/*.js', '!js/src/**/*.min.js'],
			options: {
				globals: {
					jQuery: true,
					console: true,
					module: true,
					document: true
				}
			}
		},
		concat: {
			js: {
				options: {
					separator: ';'
				},
				src: ['js/src/**/*.js'],
				dest: 'js/dist/<%= pkg.name %>.js'
			},
			css: {
				src: ['css/src/**/*.css'],
				dest: 'css/dist/<%= pkg.name %>.css'
			}
		},
		uglify: {
			build: {
				options: {
					banner: '/* <%= pkg.name %> <%= pkg.version %> <%= grunt.template.today("yyyy-mm-dd") %> */',
					sourceMap: 'js/dist/<%= pkg.name %>.js',
					// mangle: false,
					preserveComments: 'some'
				},
				files: {
					'js/dist/<%= pkg.name %>.min.js' : 'js/dist/<%= pkg.name %>.js'
				}
			}
		},
		compass: {
			css: {
				options: {
					sassDir: 'scss',
					cssDir: 'css/src'
				}
			}
		},
		cssmin: {
			minify: {
				options: {
					banner: '/* <%= pkg.name %> <%= pkg.version %> <%= grunt.template.today("yyyy-mm-dd") %> */',
				},
				files: {
					'css/dist/<%= pkg.name %>.min.css' : 'css/dist/<%= pkg.name %>.css'
				}
			}
		},
		checkwpversion: {
			options:{
				readme: 'readme.txt',
				plugin: '<%= pkg.name %>.php',
			},
			check: {
				version1: 'plugin',
				version2: 'readme',
				compare: '=='
			},
			check2: {
				version1: 'plugin',
				version2: '<%= pkg.version %>',
				compare: '==',
			}
		},
		imagemin: {
			dist: {
				options: {
					optimizationLevel: 7,
					progressive: true
				},
				files: [{
					expand: true,
					cwd: 'imgs/',
					src: '**/*',
					dest: 'imgs/'
				}]
			}
		},
		watch: {
			compass: {
				files: ['scss/**/*.{scss,sass}'],
				tasks: ['compass', 'concat:css', 'cssmin']
			},
			css: {
				files: '<%= concat.css.src %>',
				tasks: ['concat:css', 'cssmin']
			},
			js: {
				files: '<%= jshint.files %>',
				tasks: ['jshint', 'concat:js', 'uglify']
			}
		},
	} );

	grunt.loadNpmTasks( 'grunt-contrib-imagemin' );
	grunt.loadNpmTasks( 'grunt-contrib-compass' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-checkwpversion' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );

	grunt.registerTask( 'test', [ 'checkwpversion', 'jshint' ] );
	grunt.registerTask( 'default', [ 'checkwpversion', 'compass', 'jshint', 'concat', 'uglify', 'cssmin' ] );
};