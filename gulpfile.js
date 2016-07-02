/**
 * @package hairlov.fr
 * @author contact@wixiweb.fr
 */

var gulp         = require('gulp'),
    less         = require('gulp-less'),
    postcss      = require('gulp-postcss'),
    mqpacker     = require('css-mqpacker'),
    autoprefixer = require('autoprefixer'),
    concat       = require('gulp-concat'),
    uglify       = require('gulp-uglify'),
    minifyCss    = require('gulp-minify-css')
;

/**
 * Tâche par défaut :
 * - Traite les fichiers CSS/JS des librairies externes
 * - Génère les fichiers CSS de l'application à partir des sources LESS
 */
gulp.task('default', [
    'fonts',
    'vendor-less',
    'vendor-js',
    'global-less',
    'global-js',
    'application-less',
    'application-js',
    'professionnal-less',
    'professionnal-js',
    'administration-less',
    'administration-js',
    'components-less',
]);

/**
 * Surveille les modifications sur les fichiers LESS et JS,
 * et lance les tâches Gulp concernées
 */
gulp.task('watch', [
    'global-watch-js',
    'global-watch-less',
    'application-watch-js',
    'application-watch-less',
    'professionnal-watch-js',
    'professionnal-watch-less',
    'administration-watch-less',
    'administration-watch-js',
    'components-watch-less',
]);

/**
 * Traitement des fichiers CSS externes
 */
/*gulp.task('vendor-css', function() {
    var cssFiles = [
        'node_modules/bootstrap/dist/css/bootstrap.css'
    ];
    return gulp.src(cssFiles)
        .pipe(minifyCss())
        .pipe(concat('vendor.css'))
        .pipe(gulp.dest('public/css'));
});*/


/**
 * Copie le dossier fonts
 */
gulp.task('fonts', function() {
    gulp.src([
        'assets/fonts/**/*',
    ])
    .pipe(gulp.dest('public/fonts'));
});

/**
 * Traitement des fichiers JS externes
 */
gulp.task('vendor-js', function() {
    var jsFiles = [
        'node_modules/jquery/dist/jquery.js',
        'node_modules/bootstrap/dist/js/bootstrap.js',
        'assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js',
        'assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.fr.min.js',
        'assets/vendor/owl-carousel/owl.carousel.js',
    ];
    return gulp.src(jsFiles)
        .pipe(uglify({
            mangle: false // Ne pas renommer les variables
        }))
        .pipe(concat('vendor.js'))
        .pipe(gulp.dest('public/js'));
});

/**
 * Traitement des fichiers LESS externes
 */
gulp.task('vendor-less', function() {
    var lessFiles = [
        'assets/less/vendor.less',
        'assets/vendor/owl-carousel/owl.carousel.css',
        'assets/vendor/owl-carousel/owl.theme.css',
        'assets/vendor/owl-carousel/owl.transitions.css',
    ];
    return gulp.src(lessFiles)
        .pipe(less())
        .pipe(postcss([
            autoprefixer(),
            mqpacker({
                // Tris les media-queries dans l'ordre ascendant sur la propriété "min-width"
                sort: true
            })
        ]))
//        .pipe(minifyCss())
        .pipe(concat('vendor.css'))
        .pipe(gulp.dest('public/css'));
});

/**
 * Traitement des composants
 */
gulp.task('components-less', function() {
    var lessFiles = [
        'assets/less/components/_box.less',
        'assets/less/components/_panels.less'
    ];
    return gulp.src(lessFiles)
        .pipe(less())
        .pipe(postcss([
            autoprefixer(),
            mqpacker({
                // Tris les media-queries dans l'ordre ascendant sur la propriété "min-width"
                sort: true
            })
        ]))
//        .pipe(minifyCss())
        .pipe(concat('components.css'))
        .pipe(gulp.dest('public/css'));
});

/**
 * Watcher pour les fichiers LESS globaux
 */
gulp.task('components-watch-less', [
    'components-less'
], function() {
    gulp.watch(
        ['assets/less/components/**/*.less' ],
        ['components-less']
    );
});

/**
 * Traitement des fichiers LESS globaux
 */
gulp.task('global-less', function() {
    var lessFiles = [
        'assets/less/main.less'
        /*'assets/less/components/_box.less',
        'assets/less/components/_panels.less'*/
    ];
    return gulp.src(lessFiles)
        .pipe(less())
        .pipe(postcss([
            autoprefixer(),
            mqpacker({
                // Tris les media-queries dans l'ordre ascendant sur la propriété "min-width"
                sort: true
            })
        ]))
//        .pipe(minifyCss())
        .pipe(concat('hairlov.css'))
        .pipe(gulp.dest('public/css'));
});

/**
 * Traitement des fichiers JS globaux
 */
gulp.task('global-js', function() {
    var jsFiles = [
        'assets/js/module.js',
        'assets/js/main.js'
    ];
    return gulp.src(jsFiles)
        .pipe(uglify({
            mangle: false
        }))
        .pipe(concat('hairlov.js'))
        .pipe(gulp.dest('public/js'));
});

/**
 * Watcher pour les assets globaux
 */
gulp.task('global-watch', [
    'global-watch-js',
    'global-watch-less'
]);

/**
 * Watcher pour les fichiers LESS globaux
 */
gulp.task('global-watch-less', [
    'vendor-less',
    'global-less'
], function() {
    gulp.watch(
        ['assets/less/**/*.less' ],
        ['vendor-less', 'global-less']
    );
});

/**
 * Watcher pour les fichiers JS globaux
 */
gulp.task('global-watch-js', [
    'global-js'
], function() {
    gulp.watch(
        ['assets/js/**/*.js' ],
        ['global-js']
    );
});

/**
 * Traitement des fichiers LESS du module Application
 */
gulp.task('application-less', function() {
    var applicationLessFiles = [
        'module/Application/assets/less/main.less'
    ];
    return gulp.src(applicationLessFiles)
        .pipe(less())
        .pipe(postcss([
            autoprefixer(),
            mqpacker({
                // Tris les media-queries dans l'ordre ascendant sur la propriété "min-width"
                sort: true
            })
        ]))
//        .pipe(minifyCss())
        .pipe(concat('hairlov-application.css'))
        .pipe(gulp.dest('public/css'));
});

/**
 * Traitement des fichiers JS du module Application
 */
gulp.task('application-js', function() {
    var applicationJsFiles = [
        'module/Application/assets/js/module.js',
        'module/Application/assets/js/main.js',
        'module/Application/assets/js/**/*.js'
    ];
    return gulp.src(applicationJsFiles)
        .pipe(uglify({
            mangle: false
        }))
        .pipe(concat('hairlov-application.js'))
        .pipe(gulp.dest('public/js'));
});

/**
 * Watcher pour les assets du module Application
 */
gulp.task('application-watch', [
    'application-watch-js',
    'application-watch-less'
]);

/**
 * Watcher pour les fichiers LESS du module Application
 */
gulp.task('application-watch-less', [
    'application-less'
], function() {
    gulp.watch(
        ['module/Application/assets/less/**/*.less' ],
        ['application-less']
    );
});

/**
 * Watcher pour les fichiers JS du module Application
 */
gulp.task('application-watch-js', [
    'application-js'
], function() {
    gulp.watch(
        ['module/Application/assets/js/**/*.js' ],
        ['application-js']
    );
});

/**
 * Traitement des fichiers JS du module Administration
 */
gulp.task('administration-js', function() {
    var professionnalJsFiles = [
        'module/Administration/assets/js/**/*.js'
    ];
    return gulp.src(professionnalJsFiles)
        .pipe(uglify({
            mangle: false
        }))
        .pipe(concat('hairlov-administration.js'))
        .pipe(gulp.dest('public/js'));
});


/**
 * Traitement des fichiers LESS du module Administration
 */
gulp.task('administration-less', function() {
    var applicationLessFiles = [
        'module/Administration/assets/less/main.less'
    ];
    return gulp.src(applicationLessFiles)
        .pipe(less())
        .pipe(postcss([
            autoprefixer(),
            mqpacker({
                // Tris les media-queries dans l'ordre ascendant sur la propriété "min-width"
                sort: true
            })
        ]))
//        .pipe(minifyCss())
        .pipe(concat('hairlov-administration.css'))
        .pipe(gulp.dest('public/css'));
});

/**
 * Watcher pour les assets du module Administration
 */
gulp.task('administration-watch', [
    'administration-watch-js',
    'administration-watch-less'
]);

/**
 * Watcher pour les fichiers LESS du module Administration
 */
gulp.task('administration-watch-less', [
    'administration-less'
], function() {
    gulp.watch(
        ['module/Administration/assets/less/**/*.less' ],
        ['administration-less']
    );
});

/**
 * Watcher pour les fichiers JS du module Administration
 */
gulp.task('administration-watch-js', [
    'administration-js'
], function() {
    gulp.watch(
        ['module/Administration/assets/js/**/*.js' ],
        ['administration-js']
    );
});


/**
 * Traitement des fichiers LESS du module Professionnal
 */
gulp.task('professionnal-less', function() {
    var applicationLessFiles = [
        'module/Professionnal/assets/less/main.less'
    ];
    return gulp.src(applicationLessFiles)
        .pipe(less())
        .pipe(postcss([
            autoprefixer(),
            mqpacker({
                // Tris les media-queries dans l'ordre ascendant sur la propriété "min-width"
                sort: true
            })
        ]))
//        .pipe(minifyCss())
        .pipe(concat('hairlov-professionnal.css'))
        .pipe(gulp.dest('public/css'));
});


/**
 * Traitement des fichiers JS du module Professionnal
 */
gulp.task('professionnal-js', function() {
    var professionnalJsFiles = [
        'module/Professionnal/assets/vendor/jquery-file-upload-9.11.2/jquery.ui.widget.js',
        'module/Professionnal/assets/vendor/jquery-file-upload-9.11.2/jquery.fileupload.js',
        'module/Professionnal/assets/vendor/jquery-file-upload-9.11.2/jquery.fileupload-process.js',
        'module/Professionnal/assets/vendor/jquery-file-upload-9.11.2/jquery.fileupload-validate.js',
        'module/Professionnal/assets/vendor/jquery-file-upload-9.11.2/jquery.iframe-transport.js',
        'module/Professionnal/assets/js/module.js',
        'module/Professionnal/assets/js/**/*.js'
    ];
    return gulp.src(professionnalJsFiles)
        .pipe(uglify({
            mangle: false
        }))
        .pipe(concat('hairlov-professionnal.js'))
        .pipe(gulp.dest('public/js'));
});

/**
 * Watcher pour les assets du module Professionnal
 */
gulp.task('professionnal-watch', [
    'professionnal-watch-js',
    'professionnal-watch-less'
]);

/**
 * Watcher pour les fichiers LESS du module Professionnal
 */
gulp.task('professionnal-watch-less', [
    'professionnal-less'
], function() {
    gulp.watch(
        ['module/Professionnal/assets/less/**/*.less' ],
        ['professionnal-less']
    );
});

/**
 * Watcher pour les fichiers JS du module Professionnal
 */
gulp.task('professionnal-watch-js', [
    'professionnal-js'
], function() {
    gulp.watch(
        ['module/Professionnal/assets/js/**/*.js' ],
        ['professionnal-js']
    );
});