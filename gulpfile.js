// Require necessary elements
const gulp = require('gulp'),
    sass = require('gulp-sass')(require('sass')),
    cleancss = require('gulp-clean-css'),
    uglify = require('gulp-uglify')
;

// SASS
sass.compiler = require('node-sass');
gulp.task('sass', () => {
    return gulp.src('./dev/scss/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./assets/css'));
});

// Minify CSS
gulp.task('minify-css', () => {
    return gulp.src('./assets/css/*.css')
        .pipe(cleancss({ compatibility: 'ie8' }))
        .pipe(gulp.dest('./assets/css/minified'));
});

// Minify JS
gulp.task('minify-js', function() {
    return gulp.src('./dev/js/*.js')
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js/minified'));
});

// Watch
gulp.task('watch', function() {
    gulp.watch('./dev/scss/**/*.scss', gulp.series('sass'));
    gulp.watch('./assets/css/*.css', gulp.series('minify-css'));
    gulp.watch(['./dev/js/*.js'], gulp.series('minify-js'));
});

// Default task
gulp.task('default', gulp.series(
    'sass', 'minify-css', 'minify-js'
    )
);
