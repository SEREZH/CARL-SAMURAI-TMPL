var gulp = require('gulp');
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
  lib: {
    src: {
      styles: {
        css: 'resources/lib/css/**/*.css',
        min: 'resources/lib/css/*.min.css',
        scss:'resources/lib/scss/**/*.scss'
      },
      scripts: {
        js:     'resources/lib/js/*.js',
        min:    'resources/lib/js/*.min.js',
        min_lib:'resources/lib/js/lib.min.js'
      }
    }
  },
  //paths.app.src.mdb450.scripts.min
  app: {
    src: {
      styles: {
        css:  'resources/app/css/**/*.css',
        min:  'resources/app/css/app.min.css',
        scss: 'resources/app/scss/**/*.scss'
      },
      scripts: {
        js:     'resources/app/js/*.js',
        min:    'resources/app/js/app.min.js',
        min_lib:'resources/app/js/lib.min.js'
      },
      mdb450: {
        styles: {
          min:  'resources/app/mdb450/css/*.min.css',
          scss: 'resources/app/mdb450/scss/**/*.scss'
        },
        scripts: {
          min: 'resources/app/mdb450/js/*.min.js',
          min_xcl1: 'resources/app/mdb450/js/jquery-3.2.1.min.js',
          min_xcl2: 'resources/app/mdb450/js/popper.min.js'
        }
      }
    }
  },
  //paths.ezz.src.scripts.min
  ezz: {
    src: {
      styles: {
        css: 'resources/ezz/**/*.css',
        min: 'resources/ezz/*.min.css',
        scss:'resources/ezz/**/*.scss'
      },
      scripts: {
        js:     'resources/ezz/js/*.js',
        min:    'resources/ezz/js-min/*.min.js',
        min_lib:'resources/ezz/js/ezh.min.js'
      },
      images: {
        img:    'resources/ezz/img/**/*.{jpg,jpeg,png}'
      },
    }
  },
};

// copy LIB - min:  "resources/lib/css/lib.min.css",
function copy_lib_styles() {
  return gulp.src([ paths.lib.src.styles.min,
                    paths.app.src.mdb450.scripts.min,
                    '!' + paths.app.src.mdb450.scripts.min_x
    ], {allowEmpty: true} )
    .pipe(strip_css_comments({ preserve: false }))
    .pipe(concat('lib.min.css'))
    .pipe(gulp.dest(paths.styles.dest));
}
function copy_lib_scripts() {
  return gulp.src([ paths.lib.src.scripts.min, 
                    paths.app.src.mdb450.scripts.min_xcl1,  //jquery-3.2.1.min.js
                    paths.app.src.mdb450.scripts.min_xcl2,  //popper.min.js
                    '!' + paths.lib.src.scripts.min_lib
                  ], { allowEmpty: true })
    .pipe(strip_comments({ preserve: false }))
    .pipe(concat('lib.min.js'))
    .pipe(gulp.dest(paths.scripts.dest));
}
// copy APP - mdb450
function copy_app_styles_mdb() {
  return gulp.src([paths.app.src.mdb450.styles.min], { allowEmpty: true })
    .pipe(strip_css_comments({ preserve: false }))
    .pipe(concat('app.min.css'))
    .pipe(gulp.dest(paths.styles.dest));
}
function copy_app_scripts_mdb() {
  return gulp.src([paths.app.src.mdb450.scripts.min,
          '!' + paths.app.src.mdb450.scripts.min_xcl1,  //jquery-3.2.1.min.js
          '!' + paths.app.src.mdb450.scripts.min_xcl2   //popper.min.js
          ], { allowEmpty: true })
    .pipe(strip_comments({ preserve: false }))
    .pipe(concat('app.min.js'))
    .pipe(gulp.dest(paths.scripts.dest));
}
// comp&copy EZZ
function copy_ezz_styles() {
  return gulp.src([paths.ezz.src.styles.scss], { allowEmpty: true })
    .pipe(sass({ outputStyle: 'nested' }).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 10 versions'],
      cascade: false
    }))
    .pipe(strip_css_comments({ preserve: false }))
    .pipe(cssmin())
    .pipe(concat('ezz.min.css'))
    //.pipe(rename({ basename: 'ezz', suffix: '.min' }))
    .pipe(gulp.dest(paths.styles.dest));
}
function copy_ezz_scripts() {
  return gulp.src([paths.ezz.src.scripts.js], { allowEmpty: true })
    .pipe(sourcemaps.init())
    .pipe(strip_comments({ preserve: false }))
    .pipe(babel({ presets: ['es2015'] }))
    .pipe(uglify())
    .pipe(concat('ezz.min.js'))
    .pipe(sourcemaps.write(".")) // For external source map file: In this case: main.min.js.map
    .pipe(gulp.dest(paths.scripts.dest));
}

function proc_images() {
  return gulp.src(paths.ezz.src.images.img, { since: gulp.lastRun(proc_images) })
    //.pipe(imagemin({ optimizationLevel: 5 }))
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
    ]))
    .pipe(gulp.dest(paths.images.dest));
}

// Live Server - Begin
function serve(done) {
  browserSync.init({
    server: {
      baseDir: "./dist",
      index: "index.html"
    },
    ghostMode: false
  });
  done();
}

function reload(done) {
  browserSync.reload();
  done();
}

function live_server() {
  return browserSync.init({
    server: {
      baseDir: "./dist",
      directory: true
    },
    notify: false
  }),
    gulp.watch("**/*", { cwd: './dist/' }, gulp.series(reload));
}
// Live Server - End

exports.copy_app_scripts_mdb = copy_app_scripts_mdb;
exports.copy_app_styles_mdb = copy_app_styles_mdb;
var copy_app_mdb = gulp.series(copy_app_scripts_mdb, copy_app_styles_mdb);
exports.copy_lib_scripts = copy_lib_scripts;
exports.copy_lib_styles = copy_lib_styles;
var copy_lib = gulp.series(copy_lib_scripts, copy_lib_styles);
exports.copy_ezz_scripts = copy_ezz_scripts;
exports.copy_ezz_styles = copy_ezz_styles;
var copy_ezz = gulp.series(copy_ezz_scripts, copy_ezz_styles);
exports.proc_images = proc_images;
var proc_images = gulp.series(proc_images);

gulp.task('copy_app_mdb', copy_app_mdb);
gulp.task('copy_lib', copy_lib);
gulp.task('copy_ezz', copy_ezz);
gulp.task('proc_images', proc_images);

function watch_ezz() {
  gulp.watch(paths.ezz.src.styles.scss, gulp.series(copy_ezz_styles, reload));
  gulp.watch(paths.ezz.src.scripts.js, gulp.series(copy_ezz_scripts, reload));
  gulp.watch(paths.ezz.src.images.img, gulp.series(proc_images, reload));
  gulp.watch("./dist/*.html").on("change", browserSync.reload);
}
var go_ezz = gulp.series(serve, watch_ezz);
gulp.task('go_ezz', go_ezz);

var build_all = gulp.series(
  copy_app_scripts_mdb, copy_app_styles_mdb,
  copy_lib_scripts, copy_lib_styles,
  copy_ezz_scripts, copy_ezz_styles,
  proc_images
);
gulp.task('build_all', build_all);

/*
 * Define default task that can be called by just running `gulp` from cli
 */
// gulp.task('default', build);
