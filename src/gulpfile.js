/**
 * Gulpfile.
 *
 * Gulp with WordPress.
 *
 * Implements:
 *      1. Live reloads browser with BrowserSync.
 *      2. CSS: Sass to CSS conversion, error catching, Autoprefixing, Sourcemaps,
 *         CSS minification, and Merge Media Queries.
 *      3. JS: Concatenates & uglifies Vendor and Custom JS files.
 *      4. Images: Minifies PNG, JPEG, GIF and SVG images.
 *      5. Watches files for changes in CSS or JS.
 *      6. Watches files for changes in PHP.
 *      7. Corrects the line endings.
 *      8. InjectCSS instead of browser page reload.
 *      9. Generates .pot file for i18n and l10n.
 *
 * @author Ahmad Awais (@ahmadawais)
 * @version 1.0.3
 */

/**
 * Configuration.
 *
 * Project Configuration for gulp tasks.
 *
 * In paths you can add <<glob or array of globs>>. Edit the variables as per your project requirements.
 */

// START Editing Project Variables.
// Project related.
var project                 = 'WooCommerce Avatar Discounts'; // Project Name.
var projectURL              = 'skyverge-wc-avatar-discounts.test'; // Local project URL of your already running WordPress site. Could be something like local.dev or localhost:8888.
var productURL              = '../'; // Theme/Plugin URL. Leave it like it is, since our gulpfile.js lives in the root folder.

// Translation related.
var text_domain             = 'woocommerce-avatar-discounts'; // Your textdomain here.
var translationFile         = 'woocommerce-avatar-discounts.pot'; // Name of the transalation file.
var translationDestination  = '../lang'; // Where to save the translation files.
var packageName             = 'WooCommerceAvatarDiscounts'; // Package name.
var bugReport               = 'https://github.com/solepixel/woocommerce-avatar-discounts'; // Where can users report bugs.
var lastTranslator          = 'Brian DiChiara <briandichiara@gmail.com>'; // Last translator Email ID.
var team                    = 'Brian DiCHiara <briandichiara@gmail.com>'; // Team's Email ID.

// Style related.
var styleSRC                = './sass/avatars.scss'; // Path to main .scss file.
var styleDestination        = '../assets/css/'; // Path to place the compiled CSS file.
var styleBuildDestination   = './build/css/'; // Path to place the compiled CSS file.
// Default set to root folder.

// JS General related.
var jsDestination           = '../assets/js/'; // Path to place the compiled JS files.
var jsBuildDestination      = './build/js/'; // Path to place the compiled JS build files.

// JS main related.
var jsMainSRC               = './scripts/*.js'; // Path to JS custom scripts folder.
var jsMainFile              = 'wc-avatar-discounts'; // Compiled JS custom file name.

// Watch files paths.
var styleWatchFiles         = './sass/**/*.scss'; // Path to all *.scss files inside css folder and inside them.
var jsWatchFiles            = './scripts/**/*.js'; // Path to all custom JS files.
var projectPHPWatchFiles    = '../**/*.php'; // Path to all PHP files.


// Browsers you care about for autoprefixing.
// Browserlist https        ://github.com/ai/browserslist
const AUTOPREFIXER_BROWSERS = [
    'last 2 version',
    '> 1%',
    'ie >= 9',
    'ie_mob >= 10',
    'ff >= 30',
    'chrome >= 34',
    'safari >= 7',
    'opera >= 23',
    'ios >= 7',
    'android >= 4',
    'bb >= 10'
  ];

// STOP Editing Project Variables.

/**
 * Load Plugins.
 *
 * Load gulp plugins and passing them semantic names.
 */
var gulp         = require('gulp'); // Gulp of-course

// CSS related plugins.
var sass         = require('gulp-sass'); // Gulp pluign for Sass compilation.
var minifycss    = require('gulp-uglifycss'); // Minifies CSS files.
var autoprefixer = require('gulp-autoprefixer'); // Autoprefixing magic.
var mmq          = require('gulp-merge-media-queries'); // Combine matching media queries into one media query definition.

// JS related plugins.
var concat       = require('gulp-concat'); // Concatenates JS files
var uglify       = require('gulp-uglify'); // Minifies JS files

// Utility related plugins.
var rename       = require('gulp-rename'); // Renames files E.g. style.css -> style.min.css
var lineec       = require('gulp-line-ending-corrector'); // Consistent Line Endings for non UNIX systems. Gulp Plugin for Line Ending Corrector (A utility that makes sure your files have consistent line endings)
var filter       = require('gulp-filter'); // Enables you to work on a subset of the original files by filtering them using globbing.
var sourcemaps   = require('gulp-sourcemaps'); // Maps code in a compressed file (E.g. style.css) back to it’s original position in a source file (E.g. structure.scss, which was later combined with other css files to generate style.css)
//var notify       = require('gulp-notify'); // Sends message notification to you
var browserSync  = require('browser-sync').create(); // Reloads browser and injects CSS. Time-saving synchronised browser testing.
var wpPot        = require('gulp-wp-pot'); // For generating the .pot file.
var sort         = require('gulp-sort'); // Recommended to prevent unnecessary changes in pot-file.
var reload       = function( done ) {
	browserSync.reload(); // For manual browser reload.
	done();
};

/**
 * Task: `browser-sync`.
 *
 * Live Reloads, CSS injections, Localhost tunneling.
 *
 * This task does the following:
 *    1. Sets the project URL
 *    2. Sets inject CSS
 *    3. You may define a custom port
 *    4. You may want to stop the browser from openning automatically
 */
gulp.task( 'browser-sync', function() {
  return browserSync.init( {

    // For more options
    // @link http://www.browsersync.io/docs/options/

    // Project URL.
    proxy: projectURL,
    https: true,

    // `true` Automatically open the browser with BrowserSync live server.
    // `false` Stop the browser from automatically opening.
    open: true,

    // Inject CSS changes.
    // Commnet it to reload browser for every CSS change.
    injectChanges: true,

    // Use a specific port (instead of the one auto-detected by Browsersync).
    // port: 7000,

  } );
});


/**
 * Task: `styles`.
 *
 * Compiles Sass, Autoprefixes it and Minifies CSS.
 *
 * This task does the following:
 *    1. Gets the source scss file
 *    2. Compiles Sass to CSS
 *    3. Writes Sourcemaps for it
 *    4. Autoprefixes it and generates style.css
 *    5. Renames the CSS file with suffix .min.css
 *    6. Minifies the CSS file and generates style.min.css
 *    7. Injects CSS or reloads the browser via browserSync
 */
 gulp.task('styles', function () {
    return gulp.src( styleSRC )
    .pipe( sourcemaps.init() )
    .pipe( sass( {
      errLogToConsole: true,
      outputStyle: 'compact',
      // outputStyle: 'compressed',
      // outputStyle: 'nested',
      // outputStyle: 'expanded',
      precision: 10
    } ) )
    .on('error', console.error.bind(console))
    .pipe( sourcemaps.write( { includeContent: false } ) )
    .pipe( sourcemaps.init( { loadMaps: true } ) )
    .pipe( autoprefixer( AUTOPREFIXER_BROWSERS ) )

    .pipe( sourcemaps.write ( './' ) )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( styleBuildDestination ) )

    .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
    .pipe( mmq( { log: true } ) ) // Merge Media Queries only for .min.css version.

    .pipe( browserSync.stream() ) // Reloads style.css if that is enqueued.

    .pipe( rename( { suffix: '.min' } ) )
    .pipe( minifycss() )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( styleDestination ) )

    .pipe( filter( '**/*.css' ) ) // Filtering stream to only css files
    .pipe( browserSync.stream() )// Reloads style.min.css if that is enqueued.
    //.pipe( notify( { message: 'TASK: "styles" Completed! 💯', onLast: true } ) )
 });


 /**
  * Task: `mainJS`.
  *
  * Concatenate and uglify main JS scripts.
  *
  * This task does the following:
  *     1. Gets the source folder for JS main files
  *     2. Concatenates all the files and generates main.js
  *     3. Renames the JS file with suffix .min.js
  *     4. Uglifes/Minifies the JS file and generates main.min.js
  */
 gulp.task( 'mainJS', function() {
    return gulp.src( jsMainSRC )
    .pipe( concat( jsMainFile + '.js' ) )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( jsBuildDestination ) )
    .pipe( rename( {
      basename: jsMainFile,
      suffix: '.min'
    }))
    .pipe( uglify() )
    .pipe( lineec() ) // Consistent Line Endings for non UNIX systems.
    .pipe( gulp.dest( jsDestination ) );
    //.pipe( notify( { message: 'TASK: "customJs" Completed! 💯', onLast: true } ) );
 });


 /**
  * WP POT Translation File Generator.
  *
  * * This task does the following:
  *     1. Gets the source of all the PHP files
  *     2. Sort files in stream by path or any custom sort comparator
  *     3. Applies wpPot with the variable set at the top of this file
  *     4. Generate a .pot file of i18n that can be used for l10n to build .mo file
  */
 gulp.task( 'translate', function () {
     return gulp.src( projectPHPWatchFiles )
         .pipe(sort())
         .pipe(wpPot( {
             domain        : text_domain,
             package       : packageName,
             bugReport     : bugReport,
             lastTranslator: lastTranslator,
             team          : team
         } ))
        .pipe(gulp.dest(translationDestination + '/' + translationFile ))
        //.pipe( notify( { message: 'TASK: "translate" Completed! 💯', onLast: true } ) )

 });

 /**
 * Watch Tasks.
 *
 * Watches for file changes and runs specific tasks.
 */
 gulp.task( 'watch', function () {
   gulp.watch( projectPHPWatchFiles, gulp.series( reload ) ); // Reload on PHP file changes.
   gulp.watch( styleWatchFiles, gulp.series( 'styles' ) ); // Reload on SCSS file changes.
   gulp.watch( jsWatchFiles, gulp.series( 'mainJS', reload ) ); // Reload on customJS file changes.
 } );

/**
 * Run Browser-Sync and Watch via Parallel
 */
gulp.task( 'final', gulp.parallel( 'browser-sync', 'watch' ) );

 /**
  * Default Task
  *
  * Compile, enable Browser Sync, then Watch
  */
gulp.task( 'default', gulp.series( 'styles', 'mainJS', 'translate', 'final' ) );
