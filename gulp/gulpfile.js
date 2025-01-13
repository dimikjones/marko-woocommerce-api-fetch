// Include gulp.
var gulp = require( 'gulp' );
var path = require( 'path' );

// Include libraries.
var sass            = require( 'gulp-sass' )( require( 'node-sass' ) );
var sassGlob        = require( 'gulp-sass-glob' );
var concat          = require( 'gulp-concat' );
var uglify          = require( 'gulp-uglify' );
var rename          = require( 'gulp-rename' );
var cssmin          = require( 'gulp-clean-css' );
var autoprefixer    = require( 'autoprefixer' );
var postcss         = require( 'gulp-postcss' );
var browserSync     = require( 'browser-sync' ).create();
var zip             = require( 'gulp-zip' );
var checktextdomain = require( 'gulp-checktextdomain' );

// Support is set to be same as https://make.wordpress.org/core/handbook/best-practices/browser-support/.
var autoprefixerOptions = [
	"> 1%",
	"ie >= 11",
	"last 1 Android versions",
	"last 1 ChromeAndroid versions",
	"last 2 Chrome versions",
	"last 2 Firefox versions",
	"last 2 Safari versions",
	"last 2 iOS versions",
	"last 2 Edge versions",
	"last 2 Opera versions"
];

var plugins = [
	'marko-woocommerce-api-fetch'
];

var pluginsSASSTasks = [];
var pluginsJSTasks   = [];

// Concatenate plugins js and scss files and minify it.
plugins.forEach(
	function (name) {
		pluginsSASSTasks.push( 'sass-' + name );
		pluginsJSTasks.push( 'js-' + name );

		pluginsSASSTasks.push( 'sass-dashboard-' + name );
		pluginsJSTasks.push( 'js-dashboard-' + name );

		gulp.task(
			'sass-' + name,
			function () {
				return gulp.src( '../../' + name + '/assets/source/sass/frontend/*.scss' )
					.pipe( sassGlob() )
					.pipe(
						sass(
							{
								outputStyle: 'expanded',
								indentType: 'tab',
								indentWidth: 1
							}
						).on(
							'error',
							sass.logError
						)
					)
					.pipe( postcss( [autoprefixer( {overrideBrowserslist: autoprefixerOptions, cascade: false} )] ) )
					.pipe( gulp.dest( '../../' + name + '/assets/css/front' ) )
					.pipe( cssmin() )
					.pipe( rename( {suffix: '.min'} ) )
					.pipe( gulp.dest( '../../' + name + '/assets/css/front' ) )
					.pipe( browserSync.stream() );
			}
		);

		gulp.task(
			'js-' + name,
			function () {
				return gulp.src(
					[
						'../../' + name + '/assets/source/js/frontend/*.js'
					]
				)
					.pipe( gulp.dest(  '../../' + name + '/assets/js/front' ) )
					.pipe( uglify() )
					.pipe( rename( {suffix: '.min'} ) )
					.pipe( gulp.dest(  '../../' + name + '/assets/js/front' ) );
			}
		);

		gulp.task(
			'sass-dashboard-' + name,
			function () {
				return gulp.src( '../../' + name + '/assets/source/sass/admin/*.scss' )
					.pipe( sassGlob() )
					.pipe(
						sass(
							{
								outputStyle: 'expanded',
								indentType: 'tab',
								indentWidth: 1
							}
						).on(
							'error',
							sass.logError
						)
					)
					.pipe( postcss( [autoprefixer( {overrideBrowserslist: autoprefixerOptions, cascade: false} )] ) )
					.pipe( gulp.dest( '../../' + name + '/assets/css/admin' ) )
					.pipe( cssmin() )
					.pipe( rename( {suffix: '.min'} ) )
					.pipe( gulp.dest( '../../' + name + '/assets/css/admin' ) )
					.pipe( browserSync.stream() );
			}
		);

		gulp.task(
			'js-dashboard-' + name,
			function () {
				return gulp.src(
					[
						'../../' + name + '/assets/source/js/admin/*.js'
					]
				)
				.pipe( gulp.dest(  '../../' + name + '/assets/js/admin' ) )
				.pipe( uglify() )
				.pipe( rename( {suffix: '.min'} ) )
				.pipe( gulp.dest(  '../../' + name + '/assets/js/admin' ) );
			}
		);
	}
);

// Compile all plugins sass files.
gulp.task(
	'sass-plugins',
	gulp.parallel( pluginsSASSTasks )
);

// Compile all plugins js files.
gulp.task(
	'js-plugins',
	gulp.parallel( pluginsJSTasks )
);

// Watch all core files for changes (css/js).
var watchSASSSRC = [];
var watchJSSRC   = [];
plugins.forEach(
	function (name) {
		watchSASSSRC.push( '../../' + name + '/**/assets/css/scss/**/*.scss' );

		watchJSSRC.push( '../../' + name + '/**/assets/js/parts/*.js' );
		watchJSSRC.push( '../../' + name + '/**/assets/js/admin-parts/*.js' );
	}
);
gulp.task(
	'watch',
	function () {
		gulp.watch(
			watchSASSSRC,
			gulp.series( 'sass-plugins' )
		);

		gulp.watch(
			watchJSSRC,
			gulp.series( 'js-plugins' )
		);
	}
);

gulp.task(
	'default',
	gulp.parallel(
		'sass-plugins',
		'js-plugins'
	)
);

// Check theme and plugins text domains.
var textDomainKeyWords = [
	'__:1,2d',
	'_e:1,2d',
	'_x:1,2c,3d',
	'esc_html__:1,2d',
	'esc_html_e:1,2d',
	'esc_html_x:1,2c,3d',
	'esc_attr__:1,2d',
	'esc_attr_e:1,2d',
	'esc_attr_x:1,2c,3d',
	'_ex:1,2c,3d',
	'_n:1,2,4d',
	'_nx:1,2,4c,5d',
	'_n_noop:1,2,3d',
	'_nx_noop:1,2,3c,4d',
];

var pluginsCheckTextDomain = [];

plugins.forEach(
	function (name) {
		pluginsCheckTextDomain.push( 'check-text-domain-' + name );

		gulp.task(
			'check-text-domain-' + name,
			function () {
				return gulp.src(
					[
						'../../' + name + '/**/*.php',
					]
				)
					.pipe(
						checktextdomain(
							{
								text_domain: name,
								keywords: textDomainKeyWords,
							}
						)
					);
			}
		);
	}
);

gulp.task(
	'check-text-domain',
	gulp.series( pluginsCheckTextDomain )
);

// API helper functions.
var getBlockedFoldersName = [
	path.sep + 'scss',
	path.sep + 'js' + path.sep + 'parts',
	path.sep + 'js' + path.sep + 'admin-parts',
	'node_modules',
];

var checkBlockedFolder = function (file) {
	var addFlag = true;

	getBlockedFoldersName.forEach(
		function (folderName) {

			if (file.dirname.includes( folderName )) {
				addFlag = false;
			}
		}
	);

	return addFlag;
};

var runCMD = function (command, args, done) {
	var exec = require( 'child_process' ).exec;

	exec(
		command + ' ' + args,
		function (error, stdout, stderr) {
			console.log( stdout );
		}
	).on(
		'exit',
		function (code) {
			done();
		}
	);
};

// Creating archives - BEGIN.
var pluginsZIPTasks = [];

// Creating theme and plugins pot files.
gulp.task(
	'create-pot',
	function (done) {
		plugins.forEach(
			function (plugin) {
				var args = '../../' + plugin + ' ../../' + plugin + '/languages/' + plugin + '.pot';

				runCMD( 'wp i18n make-pot', args, done );
			}
		);
	}
);

plugins.forEach(
	function (name) {
		pluginsZIPTasks.push( 'create-zip-' + name );

		gulp.task(
			'create-zip-' + name,
			function () {
				return gulp.src(
					[
						'../../' + name + '/**',
					]
				)
					.pipe(
						rename(
							function (file) {
								var addFlag = checkBlockedFolder( file );

								if (
									'' === file.extname ||
									! addFlag ||
									'webpack.config' === file.basename ||
									('package' === file.basename && '.json' === file.extname)
								) {
									return {
										dirname: '',
										basename: '',
										extname: ''
									};
								}

								if (addFlag) {
									file.dirname = name + '/' + file.dirname;
								}
							}
						)
					)
					.pipe( zip( name + '.zip' ) )
					.pipe( gulp.dest( '../../_wp' ) );
			}
		);
	}
);

gulp.task(
	'create-zip',
	gulp.parallel( pluginsZIPTasks )
);

gulp.task(
	'wordpress',
	gulp.series(
		'create-pot',
		'default',
		'create-zip'
	)
);

// Creating archives - END.
