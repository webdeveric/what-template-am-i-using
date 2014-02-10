module.exports = function(grunt){
	'use strict';
	grunt.initConfig( {
		pkg: grunt.file.readJSON('package.json'),
		jshint: {
			files: ['Gruntfile.js', 'js/src/*.js', '!js/src/*.min.js'],
			options: {
				globals: {
					jQuery: true,
					console: true,
					module: true,
					document: true
				}
			}
		},
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> v<%= pkg.version %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
				sourceMap: true,
				mangle: false,
				preserveComments: 'some'
			},
			build: {
				files: [ {
					expand: true,
					cwd: 'js/src',
					src: '**/*.js',
					dest: 'js/min',
					ext: '.min.js'
				} ]
			}
		},
		concat: {
			js: {
				options: {
					separator: ';'
				},
				src: ['js/min/*.js'],
				dest: 'js/dist/<%= pkg.name %>.js'
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
				expand: true,
				cwd: 'css/src',
				src: ['*.css', '!*.min.css'],
				dest: 'css/min',
				ext: '.min.css'
			},
			combine: {
				files: {
					'css/dist/<%= pkg.name %>.css': ['css/min/*.css']
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
		watch: {
			files: ['scss/*', 'css/src/*', 'js/src/*'],
			tasks: ['default']
		}
	} );

	grunt.loadNpmTasks( 'grunt-contrib-compass' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-checkwpversion' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );

	grunt.registerTask( 'test', [ 'checkwpversion', 'jshint' ] );

	grunt.registerTask( 'default', [ 'checkwpversion', 'jshint', 'uglify', 'compass', 'concat', 'cssmin' ] );
};