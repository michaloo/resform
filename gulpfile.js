var gulp   = require("gulp"),
    zip    = require("gulp-zip"),
    clean  = require("gulp-clean"),
    ejs    = require("gulp-ejs"),
    rename = require("gulp-rename"),
    exec   = require('child_process').exec,
    fs     = require("fs");

var package, settings, update = {};

gulp.task('release', ['zip']);

gulp.task('version', [], function (done) {


    exec("cat src/resform.php | grep Version | awk -F': ' '{print $2}'", function (err, stdout, stderr) {
      package = {
        "version": stdout.replace(/\s/, '')
      };
      settings = {
          "path": 'resform/',
          "name": 'resform_' + package.version + '.zip'
      };
      done(stderr);
    });
});

gulp.task('copy', ['clean'], function () {

    return gulp.src([
            './**',
            '!./src/assets/vendor/jquery-ui/themes/**',
            '!./src/vendor/components/jqueryui/themes/**'
        ])
        .pipe(gulp.dest('./dist/resform_plugin'));
});

// gulp.task('parse', ['copy'], function (done) {
//     exec("cd dist/theme && grunt build", function (err, stdout, stderr) {
//       done(stderr);
//     });
// });

gulp.task('rename', ['copy'], function () {

    return gulp.src('dist/resform_plugin/src/**')
        .pipe(gulp.dest('dist/resform'));
});

gulp.task('zip', ['rename'], function () {

    return gulp.src('dist/resform/**', { base: 'dist/' })
        .pipe(zip(settings.name))
        .pipe(gulp.dest('dist'));
});

gulp.task('clean', ['version'], function() {
    return gulp.src('./dist/', {read: false})
        .pipe(clean());
});
