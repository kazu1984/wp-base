const { src, dest, watch, series, parallel } = require("gulp");

// 共通
const rename = require("gulp-rename");
const del = require("del");

const rootPath = "../";
// const themeName = rootPath + "nobel"
const themeName = "wp-base"

// 入出力フォルダ
const srcBase = rootPath + "src/";
const srcPath = {
  css: srcBase + "sass/**/*.scss",
  img: srcBase + "images/**/*",
  js: srcBase + "js/**/*.js",
  php: rootPath + "/**/*.php",
};
const distBase = rootPath + "assets/";
const distPath = {
  css: distBase + "css/",
  img: distBase + "images/",
  js: distBase + "js/",
};

// ブラウザーシンク（リアルタイムでブラウザに反映させる処理）
const browserSync = require("browser-sync");
const browserSyncOption = {
  proxy: 'http://wp-base.localhost/', 
  // server: themeName + "/",
};
const browserSyncFunc = () => {
  browserSync.init(browserSyncOption);
};
const browserSyncReload = (done) => {
  browserSync.reload();
  done();
};

// Sass
const sass = require("gulp-sass")(require("sass"));
const sassGlob = require("gulp-sass-glob-use-forward");
const plumber = require("gulp-plumber");
const notify = require("gulp-notify");
const postcss = require("gulp-postcss");
const cssnext = require("postcss-cssnext");
const cleanCSS = require("gulp-clean-css");
const sourcemaps = require("gulp-sourcemaps");
const browsers = [
  "last 2 versions",
  "> 5%",
  "ie = 11",
  "not ie <= 10",
  "ios >= 8",
  "and_chr >= 5",
  "Android >= 5",
];

const cssSass = () => {
  return src(srcPath.css)
    .pipe(sourcemaps.init())
    .pipe(
      plumber({
        errorHandler: notify.onError("Error:<%= error.message %>"),
      })
    )
    .pipe(sassGlob())
    .pipe(
      sass.sync({
        includePaths: ["src/sass"],
        outputStyle: "expanded",
      })
    )
    .pipe(postcss([cssnext(browsers)]))
    .pipe(sourcemaps.write("./"))
    .pipe(dest(distPath.css))
    .pipe(
      notify({
        message: "CSS ok", 
        onLast: true,
      })
    );
};

// javascript
const js = () => {
  return src(srcPath.js)
    .pipe(sourcemaps.init())
    .pipe(dest(distPath.js))
    .pipe(
      notify({
        message: "Js ok",
        onLast: true,
      })
    );
};


// 画像圧縮
const imagemin = require("gulp-imagemin");
const imageminMozjpeg = require("imagemin-mozjpeg");
const imageminPngquant = require("imagemin-pngquant");
const imageminSvgo = require("imagemin-svgo");
const imgImagemin = () => {
  return src(srcPath.img)
    .pipe(
      imagemin(
        [
          imageminMozjpeg({ quality: 80 }),
          imageminPngquant(),
          imageminSvgo({ plugins: [{ removeViewbox: false }] }),
        ],
        {
          verbose: true,
        }
      )
    )
    .pipe(dest(distPath.img));
};

// ファイルの変更を検知
const watchFiles = () => {
  watch(srcPath.js, series(js, browserSyncReload));
  watch(srcPath.css, series(cssSass, browserSyncReload));
  watch(srcPath.img, series(imgImagemin, browserSyncReload));
  watch(srcPath.php, series(browserSyncReload));
};

/**
 * clean
 */
const clean = () => {
  return del([distBase + "/**"], {
    force: true,
  });
};

// npx gulp
exports.default = series(
  series(clean, js, cssSass, imgImagemin),
  parallel(watchFiles, browserSyncFunc)
);
