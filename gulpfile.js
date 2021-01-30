var babel = require('gulp-babel')
var gulp = require('gulp')
var uglify = require('gulp-uglify');
var cleanCSS = require('gulp-clean-css');
var sass = require('gulp-sass');

gulp.task('js', function() {
  return gulp
    .src('src/assets/js/*.js')
    .pipe(babel({ presets: ['@babel/env'] }))
    .pipe(uglify())
    .pipe(gulp.dest('src/assets/dist'))
});

gulp.task('sass', function(){
  return gulp.src('src/assets/sass/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(cleanCSS({compatibility: 'ie8'}))
    .pipe(gulp.dest('src/assets/dist'))
});

gulp.task('build', gulp.parallel('js', 'sass'));

gulp.task('watch', function(){
  gulp.watch('src/assets/sass/*.scss', gulp.parallel('sass'));
  gulp.watch('src/assets/js/*.js', gulp.parallel('js'));
})