var babel = require('gulp-babel')
var gulp = require('gulp')
var uglify = require('gulp-uglify');
var cleanCSS = require('gulp-clean-css');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');

gulp.task('js-dev', function() {
  return gulp
    .src('src/assets/js/*.js')
    .pipe(sourcemaps.init())
    .pipe(babel({ presets: ['@babel/env'] }))
    .pipe(uglify())
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('src/assets/dist'))
});

gulp.task('js-prod', function() {
  return gulp
    .src('src/assets/js/*.js')
    .pipe(babel({ presets: ['@babel/env'] }))
    .pipe(uglify())
    .pipe(gulp.dest('src/assets/dist'))
});

gulp.task('sass-dev', function(){
  return gulp.src('src/assets/sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(sourcemaps.write())
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
  gulp.watch('src/assets/sass/*.scss', gulp.parallel('sass-dev'));
  gulp.watch('src/assets/js/*.js', gulp.parallel('js-dev'));
})