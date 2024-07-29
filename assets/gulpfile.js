const gulp = require('gulp'),
    watch = require('gulp-watch'),
    concat = require('gulp-concat'),
    sass = require('gulp-sass')(require('sass')),
    uglify = require('gulp-uglify'),
    cssmin = require('gulp-cssmin'),
    autoprefixer = require('gulp-autoprefixer'),
    sourcemaps = require('gulp-sourcemaps'),
    importCss = require('gulp-import-css'),
    browserSync = require('browser-sync').create(),
    pxtorem = require('gulp-pxtorem');

var config = {
    localUrl: "https://localhost/"
};

/**
 * Browser Sync
 */
gulp.task('serve', function () {
    browserSync.init({
        open: false,
        proxy: config.localUrl,
        notify: true,
    });
    gulp.watch(
        [
            'scss/**/*.scss'
        ],
        gulp.parallel('css')
    );

    gulp.watch(
        [
            'js/*.js',
        ],
        gulp.parallel('js')
    );

    gulp.watch(
        [
            "./*.html"
        ]
    ).on('change', browserSync.reload);

    gulp.watch(
        [
            "./**/**/**/**/*.php"
        ]
    ).on('change', browserSync.reload);

});

/**
 * Compile CSS
 */
gulp.task('css', function () {
    return gulp.src('./scss/app.scss')
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(sass().on('error', sass.logError))
        .pipe(importCss())
        .pipe(sass())
        .pipe(autoprefixer({
            overrideBrowserslist: ['last 2 versions'],
            cascade: false
        }))
        .pipe(pxtorem())
        .pipe(cssmin())
        .pipe(concat('bundle.css'))
        .pipe(sourcemaps.write('./', {
            includeContent: true,
            sourceRoot: '../../scss'
        }))
        .pipe(gulp.dest('./css/'))
        .pipe(browserSync.reload({stream: true}))
});

/**
 * Compile JS
 */
gulp.task('js', function () {
    return gulp.src(
        [
            './js/*.js',
        ]
    )
        .pipe(concat('bundle.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./js/'))
        .pipe(browserSync.reload({stream: true}))

});

/**
 * Build
 */
gulp.task('build', gulp.series(['css', 'js']));
/**
 * Default
 */
gulp.task('default', gulp.series('serve'));
