module.exports = function ( grunt ) {
	// Project configuration
	var autoprefixer = require( 'autoprefixer' );
	var flexibility = require( 'postcss-flexibility' );

	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),

		postcss: {
			options: {
				map: false,
				processors: [ flexibility, autoprefixer( { cascade: false } ) ],
			},
			style: {
				expand: true,
				src: [ 'assets/css/**.css', '!assets/css/**-rtl.css' ],
			},
		},

		rtlcss: {
			options: {
				// rtlcss options
				config: {
					preserveComments: true,
					greedy: true,
				},
				// generate source maps
				map: false,
			},
			dist: {
				files: [
					{
						expand: true,
						cwd: 'assets/css',
						src: [ '*.css', '!*-rtl.css' ],
						dest: 'assets/css/',
						ext: '-rtl.css',
					},
				],
			},
		},

		copy: {
			main: {
				options: {
					mode: true,
				},
				src: [
					'**',
					'!node_modules/**',
					'!build/**',
					'!css/sourcemap/**',
					'!.git/**',
					'!.github',
					'!.distignore',
					'!.wordpress-org',
					'!.stylelintrc.json',
					'!bin/**',
					'!.gitlab-ci.yml',
					'!bin/**',
					'!tests/**',
					'!phpunit.xml.dist',
					'!phpstan-baseline.neon',
					'!phpstan.neon',
					'!*.sh',
					'!*.map',
					'!*.zip',
					'!Gruntfile.js',
					'!package.json',
					'!.gitignore',
					'!phpunit.xml',
					'!README.md',
					'!sass/**',
					'!codesniffer.ruleset.xml',
					'!vendor/**',
					'!playwright-report/**',
					'!test-results/**',
					'!composer.json',
					'!composer.lock',
					'!package-lock.json',
					'!phpcs.xml.dist',
					'!jsconfig.json',
					'!webpack.config.js',
					'!cypress/**',
					'!cypress.json',
					'!scripts/**',
					'!artifact/**',
					'!playwright.config.js',
					'!postcss.config.js',
					'!tailwind.config.js',
					'!wizard-webpack-config.js',
					'!admin-core/assets/src/**',
					'!modules/gutenberg/node_modules/**',
					'!modules/gutenberg/config/**',
					'!modules/gutenberg/scripts/**',
					'!modules/gutenberg/src/blocks/**',
					'!modules/gutenberg/src/componants/**',
					'!modules/gutenberg/src/gutenberg-webpack-config.js',
					'!modules/gutenberg/package.json',
					'!modules/gutenberg/package-lock.json',
					'!modules/gutenberg/.gitignore',
					'!modules/gutenberg/.gitattributes',
				],
				dest: 'custom-post-shortcode-creator/',
			},
		},
		compress: {
			main: {
				options: {
					archive: 'custom-post-shortcode-creator-<%= pkg.version %>.zip',

					mode: 'zip',
				},
				files: [
					{
						src: [ './custom-post-shortcode-creator/**' ],
					},
				],
			},
		},
		clean: {
			main: [ 'custom-post-shortcode-creator' ],
			zip: [ '*.zip' ],
		},
		makepot: {
			target: {
				options: {
					domainPath: '/',
					mainFile: 'custom-post-shortcode-creator.php',
					potFilename: 'languages/custom-post-shortcode-creator.pot',
					exclude: [ 'node_modules/.*' ],
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true,
					},
					type: 'wp-plugin',
					updateTimestamp: true,
				},
			},
		},
		addtextdomain: {
			options: {
				textdomain: 'custom-post-shortcode-creator',
				updateDomains: true,
			},
			target: {
				files: {
					src: [
						'*.php',
						'**/*.php',
						'!node_modules/**',
						'!vendor/**',
						'!php-tests/**',
						'!bin/**',
						'!tests/**',
					],
				},
			},
		},

		bumpup: {
			options: {
				updateProps: {
					pkg: 'package.json',
				},
			},
			file: 'package.json',
		},

		replace: {
			plugin_main: {
				src: [ 'custom-post-shortcode-creator.php' ],
				overwrite: true,
				replacements: [
					{
						from: /Version: \bv?(?:0|[1-9]\d*)\.(?:0|[1-9]\d*)\.(?:0|[1-9]\d*)(?:-[\da-z-A-Z-]+(?:\.[\da-z-A-Z-]+)*)?(?:\+[\da-z-A-Z-]+(?:\.[\da-z-A-Z-]+)*)?\b/g,
						to: 'Version: <%= pkg.version %>',
					},
				],
			},
			plugin_const: {
				src: [ 'classes/class-cpsc-loader.php' ],
				overwrite: true,
				replacements: [
					{
						from: /CPSC_VER', '.*?'/g,
						to: "CPSC_VER', '<%= pkg.version %>'",
					},
				],
			},
			stable_tag: {
				src: [ 'readme.txt' ],
				overwrite: true,
				replacements: [
					{
						from: /Stable tag:\ .*/g,
						to: 'Stable tag: <%= pkg.version %>',
					},
				],
			},
			plugin_function_comment: {
				src: [
					'*.php',
					'**/*.php',
					'!node_modules/**',
					'!php-tests/**',
					'!bin/**',
					'!tests/**',
				],
				overwrite: true,
				replacements: [
					{
						from: 'x.x.x',
						to: '<%=pkg.version %>',
					},
				],
			},
		},

		/* Minify Js and Css */
		cssmin: {
			options: {
				keepSpecialComments: 0,
			},
			css: {
				files: [
					{
						expand: true,
						cwd: 'assets/css',
						src: [ '*.css' ],
						dest: 'assets/min-css',
						ext: '.min.css',
					},
				],
			},
		},

		uglify: {
			js: {
				options: {
					compress: {
						drop_console: true,
					},
				},
				files: [
					{
						expand: true,
						cwd: 'assets/js',
						src: [ '*.js' ],
						dest: 'assets/min-js',
						ext: '.min.js',
					},
				],
			},
		},
	} );

	// Load grunt tasks
	grunt.loadNpmTasks( 'grunt-rtlcss' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-bumpup' );
	grunt.loadNpmTasks( 'grunt-text-replace' );
	grunt.loadNpmTasks( 'grunt-postcss' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );

	// Autoprefix
	grunt.registerTask( 'style', [ 'postcss:style' ] );

	// rtlcss, you will still need to install ruby and sass on your system manually to run this
	grunt.registerTask( 'rtl', [ 'rtlcss' ] );
	grunt.registerTask( 'release', [
		'clean:zip',
		'copy',
		'compress',
		'clean:main',
	] );
	grunt.registerTask( 'textdomain', [ 'addtextdomain' ] );
	grunt.registerTask( 'i18n', [ 'addtextdomain', 'makepot' ] );

	// min all
	grunt.registerTask( 'minify', [
		'style',
		'rtlcss',
		'cssmin:css',
		'uglify:js',
	] );

	// Bump Version - `grunt version-bump --ver=<version-number>`
	grunt.registerTask( 'version-bump', function ( ver ) {
		var newVersion = grunt.option( 'ver' );
		if ( newVersion ) {
			newVersion = newVersion ? newVersion : 'patch';

			grunt.task.run( 'bumpup:' + newVersion );
			grunt.task.run( 'replace' );
		}
	} );
}; 