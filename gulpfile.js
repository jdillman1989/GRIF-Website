'use strict';

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var sass = require('gulp-sass');
var imagemin = require('gulp-imagemin');

gulp.task('default', function () {
	gulp.src('./assets/js/application.js')
	.pipe(uglify())
	.pipe(gulp.dest('./assets/js/application.min.js'));

	gulp.src('./assets/sass/application.scss')
	.pipe(sass().on('error', sass.logError))
	.pipe(gulp.dest('./assets/styles/application.css'));

	gulp.src('./assets/images/raw/*')
	.pipe(imagemin())
	.pipe(gulp.dest('./assets/images'));
});