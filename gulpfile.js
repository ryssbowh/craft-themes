var babel = require('gulp-babel')
var gulp = require('gulp')
var uglify = require('gulp-uglify');
var cleanCSS = require('gulp-clean-css');
var gulpSass = require('gulp-sass');
var dartSass = require('sass');
var sass = gulpSass(dartSass);
var sourcemaps = require('gulp-sourcemaps');

gulp.task('js-prod', function() {
  return gulp
    .src('src/assets/js/*.js')
    .pipe(babel({ presets: ['@babel/preset-env'] }))
    .pipe(uglify())
    .pipe(gulp.dest('src/assets/dist'))
});

gulp.task('sass-prod', function(){
  return gulp.src('src/assets/sass/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(gulp.dest('src/assets/dist'))
});

gulp.task('prod', gulp.parallel('js-prod', 'sass-prod'));

gulp.task('watch', function(){
  gulp.watch('src/assets/sass/*.scss', gulp.parallel('sass-prod'));
  gulp.watch('src/assets/js/*.js', gulp.parallel('js-prod'));
})