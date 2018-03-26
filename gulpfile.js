
//gulp-php-minify
//gulp-about
//gulp-as-css-imports
//gulp-base64-img
//gulp-concat-folders
//gulp-deploy-git
//gulp-exif
//gulp-filelist
//gulp-ftp
//gulp-gulp
//gulp-if
//gulp-ignore
//gulp-micromatch
//gulp-pretty-url

var gulp = require('gulp');
var htmlmin = require('gulp-htmlmin');
var htmlhint = require("gulp-htmlhint");
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var strip_css_comments = require('gulp-strip-css-comments');
var strip_comments = require('gulp-strip-comments');
var cssmin = require('gulp-cssmin');
var babel = require('gulp-babel');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
//var uglify = require('gulp-uglify-es').default;
//var rollup = require('gulp-rollup');
var imagemin = require('gulp-imagemin');
var minify = require('gulp-minify');
var sourcemaps = require('gulp-sourcemaps');
var rename = require('gulp-rename');
var del = require('del');
var plumber = require('gulp-plumber');
var plumberNotifier = require('gulp-plumber-notifier');
var browserSync = require('browser-sync').create();

var debug = require('gulp-debug');

//paths.styles.dest
//paths.scripts.dest
var paths = {
  styles: {
    dest:     './dist/css/',
  },
  scripts: {
    dest:    './dist/js/',
  },
  images: {
    dest: './dist/img/',
    src: 'resources/ezz/img/**/*.{jpg,jpeg,png}'
  },
  //paths.lib.src.scripts.min
  lib1: {
    src: {
      styles: {
        css: 'resources/lib1/css/**/*.css',
        min: 'resources/lib1/css/**/*.min.css',
      },
      scripts: {
        js: 'resources/lib1/js/**/*.js',
        min: 'resources/lib1/js/**/*.min.js',
      }
    }
  },
  lib2: {
    src: {
      styles: {
        css1: 'resources/lib2/css/mdb.min.css',
        css2: 'resources/lib2/css/flipclock.css',
        css3: 'resources/lib2/css/*.css',
        min1: 'resources/lib2/css/mdb.min.css',
        min2: 'resources/lib2/css/flipclock.css',
        min3: 'resources/lib2/css/*.min.css',
      },
      scripts: {
        js: 'resources/lib2/js/*.js',
        min: 'resources/lib2/js/*.min.js',
      }
    }
  },
  //paths.ezz.src.scripts.min
  ezz: {
    src: {
      styles: {
        css: 'resources/ezz/**/*.css',
        min: 'resources/ezz/**/*.min.css',
        scss:'resources/ezz/**/*.scss'
      },
      scripts: {
        js:     'resources/ezz/js/**/*.js',
        min:    'resources/ezz/js-min/**/*.min.js',
      },
      images: {
        img:    'resources/ezz/img/**/*.{jpg,jpeg,png}'
      },
    }
  },
};

// copy LIB - min:  "resources/lib/css/lib.min.css",
function copy_lib1_styles() {
  return gulp.src([ paths.lib1.src.styles.min], {allowEmpty: true} )
    .pipe(debug({ title: 'src' }))
    .pipe(strip_css_comments({ preserve: false }))
    .pipe(concat('lib1.min.css'))
    .pipe(debug({ title: 'concat' }))
    .pipe(gulp.dest(paths.styles.dest));
}
function copy_lib2_styles() {
  return gulp.src([paths.lib2.src.styles.css1,
                   paths.lib2.src.styles.css2,
                   paths.lib2.src.styles.css3
                  ], { allowEmpty: true })
    .pipe(debug({ title: 'src' }))              
    .pipe(strip_css_comments({ preserve: false }))
    .pipe(cssmin())
    .pipe(concat('lib2.min.css'))
    .pipe(debug({ title: 'concat' }))
    .pipe(gulp.dest(paths.styles.dest));
}
function copy_lib1_scripts() {
  return gulp.src([ paths.lib1.src.scripts.min], { allowEmpty: true })
    .pipe(debug({ title: 'src' }))                
    .pipe(strip_comments({ preserve: false }))
    .pipe(concat('lib1.min.js'))
    .pipe(debug({ title: 'concat' }))
    .pipe(gulp.dest(paths.scripts.dest));
}
function copy_lib2_scripts() {
  return gulp.src([paths.lib2.src.scripts.min], { allowEmpty: true })
    .pipe(debug({ title: 'src' }))              
    .pipe(strip_comments({ preserve: false }))
    .pipe(concat('lib2.min.js'))
    .pipe(debug({ title: 'concat' }))
    .pipe(gulp.dest(paths.scripts.dest));
}
// comp&copy EZZ
function copy_ezz_styles() {
  return gulp.src([paths.ezz.src.styles.scss], { allowEmpty: true })
    .pipe(debug({ title: 'src' }))              
    .pipe(plumber())
    .pipe(sass({ outputStyle: 'nested' }).on('error', sass.logError))
    .pipe(debug({ title: 'sass' }))              
    .pipe(autoprefixer({
      browsers: ['last 10 versions'],
      cascade: false
    }))
    //.pipe(strip_css_comments({ preserve: false })) // что-то похоже, что комменты и без этого убираются?!
    .pipe(cssmin())
    .pipe(concat('ezz.min.css'))
    .pipe(debug({ title: 'concat' }))              
    //.pipe(rename({ basename: 'ezz', suffix: '.min' }))
    .pipe(plumber.stop())
    .pipe(gulp.dest(paths.styles.dest));
}
function copy_ezz_scripts() {
  return gulp.src([paths.ezz.src.scripts.js], { allowEmpty: true })
    .pipe(debug({ title: 'src' }))              
    .pipe(sourcemaps.init())
    .pipe(strip_comments({ preserve: false }))
    .pipe(babel({ presets: ['es2015'] }))
    .pipe(uglify())
    .pipe(concat('ezz.min.js'))
    .pipe(debug({ title: 'concat' }))              
    .pipe(sourcemaps.write(".")) // For external source map file: In this case: main.min.js.map
    .pipe(gulp.dest(paths.scripts.dest));
}
function copy_ezz_images() {
  return gulp.src(paths.ezz.src.images.img, { since: gulp.lastRun(copy_ezz_images) })
    .pipe(debug({ title: 'src' }))              
    .pipe(imagemin([
      imagemin.gifsicle({ interlaced: true }),
      imagemin.jpegtran({ progressive: true }),
      imagemin.optipng({ optimizationLevel: 5 }),
      imagemin.svgo({
        plugins: [
          { removeViewBox: true },
          { cleanupIDs: false }
        ]
      })
      .pipe(debug({ title: 'imagemin' }))              
    ]))
    .pipe(gulp.dest(paths.images.dest));
}
// Live Server - Begin
function serve(done) {
  browserSync.init({
    /* server: {
      baseDir: "./dist",
      index: "index.php"
    }, 
    ghostMode: false, */
   
    proxy: 'http://carl-samurai.tmpl/',
    host: 'carl-samurai.tmpl',
    open: 'external',

    baseDir: "./dist",

    notify: false
  });
  done();
}
function reload(done) {
  browserSync.reload();
  done();
}
function live_server() {
  return browserSync.init({
    /* server: {
      baseDir: "./dist",
      directory: true
    }, */
    /* notify: false */
    proxy: 'http://carl-samurai.tmpl/',
    host: 'carl-samurai.tmpl',
    open: 'external',

    baseDir: "./dist",

    notify: false

  }),
    gulp.watch("**/*", { cwd: './dist/' }, gulp.series(reload));
}
// Live Server - End
/* --------------------------------------------------- */
exports.copy_lib1_styles = copy_lib1_styles;
exports.copy_lib2_styles = copy_lib2_styles;
exports.copy_lib1_scripts = copy_lib1_scripts;
exports.copy_lib2_scripts = copy_lib2_scripts;
exports.copy_ezz_styles = copy_ezz_styles;
exports.copy_ezz_scripts = copy_ezz_scripts;
exports.copy_ezz_images = copy_ezz_images;
/* --------------------------------------------------- */
var copy_lib1 = gulp.series(copy_lib1_styles, copy_lib1_scripts);
var copy_lib2 = gulp.series(copy_lib2_styles, copy_lib2_scripts);
var copy_ezz  = gulp.series(copy_ezz_styles, copy_ezz_scripts);
var copy_img  = gulp.series(copy_ezz_images);
/* --------------------------------------------------- */
gulp.task('copy_lib1', copy_lib1);
gulp.task('copy_lib2', copy_lib2);
gulp.task('copy_ezz', copy_ezz);
gulp.task('copy_img', copy_img);
/* --------------------------------------------------- */
function watch_ezz() {
  gulp.watch(paths.ezz.src.styles.scss, gulp.series(copy_ezz_styles, reload));
  gulp.watch(paths.ezz.src.scripts.js, gulp.series(copy_ezz_scripts, reload));
  gulp.watch(paths.ezz.src.images.img, gulp.series(copy_img, reload));
  gulp.watch("./dist/*.html").on("change", browserSync.reload);
  gulp.watch("./dist/**/*.php").on("change", browserSync.reload);
}
var go_ezz = gulp.series(serve, watch_ezz);
/* --------------------------------------------------- */
var build_all = gulp.series(
  copy_lib1_styles, copy_lib1_scripts,
  copy_lib2_styles, copy_lib2_scripts,
  copy_ezz_styles, copy_ezz_scripts
);
var build_alli = gulp.series(
  copy_lib1_styles, copy_lib1_scripts,
  copy_lib2_styles, copy_lib2_scripts,
  copy_ezz_styles, copy_ezz_scripts,
  copy_img
);
/* --------------------------------------------------- */
gulp.task('go_ezz', go_ezz);
gulp.task('build_all', build_all);
gulp.task('build_alli', build_alli);
/*
 * Define default task that can be called by just running `gulp` from cli
 */
// gulp.task('default', build);
