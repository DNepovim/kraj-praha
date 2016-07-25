const
    gulp            = require('gulp'),
    plumber         = require('gulp-plumber'),
    rename          = require('gulp-rename'),
    concat          = require('gulp-concat'),
    imagemin        = require('gulp-imagemin'),
    pngquant        = require('imagemin-pngquant'),
    less            = require('gulp-less'),
    postcss         = require('gulp-postcss'),
    autoprefixer    = require('autoprefixer'),
    pixrem          = require('gulp-pixrem'),
    sourcemaps      = require('gulp-sourcemaps'),
    minifycss       = require('gulp-minify-css'),
    uglify          = require('gulp-uglify'),
    jade            = require('gulp-jade-php'),
    browserSync     = require('browser-sync');

const
    localhostURL = 'praha.dev'
    pathToTemplate = 'wp-content/themes/template/';


gulp.task('images', function () {
    return gulp.src(pathToTemplate + 'src/images/*')
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest(pathToTemplate + 'dist/images'));
});

gulp.task('styles', function () {
    gulp.src([pathToTemplate + 'src/styles/**/*.less'])
        .pipe(plumber({
            errorHandler: function (error) {
                console.log(error.message);
                this.emit('end');
            }
        }))
        .pipe(concat('styles.less'))
        .pipe(less())
        .pipe(sourcemaps.init())
        .pipe(pixrem())
        .pipe(postcss([autoprefixer({browsers: ['last 2 versions']})]))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(pathToTemplate + 'dist/'))
        .pipe(rename({suffix: '.min'}))
        .pipe(minifycss())
        .pipe(gulp.dest(pathToTemplate + 'dist/'))
        .pipe(browserSync.stream())
});

gulp.task('scripts', function () {
    return gulp.src(pathToTemplate + 'src/scripts/**/*.js')
        .pipe(plumber({
            errorHandler: function (error) {
                console.log(error.message);
                this.emit('end');
            }
        }))
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest(pathToTemplate + 'dist/'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest(pathToTemplate + 'dist/'))
        .pipe(browserSync.stream())
});

gulp.task('templates', function() {
    gulp.src(pathToTemplate + 'src/templates/*.jade')
        .pipe(jade())
        .pipe(gulp.dest(pathToTemplate))
        .pipe(browserSync.stream())
});

gulp.task('default', ['images', 'styles', 'scripts','templates'] , function () {
    browserSync.init({
        proxy: localhostURL
    });
    gulp.watch(pathToTemplate + 'src/images/**', ['images']);
    gulp.watch(pathToTemplate + 'src/styles/**/*.less', ['styles']);
    gulp.watch(pathToTemplate + 'src/scripts/**/*.js', ['scripts']);
    gulp.watch(pathToTemplate + 'src/templates/**/*.jade', ['templates']);
    gulp.watch('**/*.php').on('change', browserSync.reload);
});
