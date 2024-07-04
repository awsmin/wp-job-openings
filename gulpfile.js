/**
 * Automating Development Tasks
 * ----------------------------
 * @package wp-job-openings
 * @since 1.0.0
 */

"use strict";

/*============================= Dependencies =============================*/
const sass = require('gulp-sass')(require('sass'));

// Paths to your SCSS and CSS files
const scssFiles = [
	'wjo-block/src/editor.scss',
    'wjo-block/src/style.scss'
];


const gulp = require("gulp"),
	config = require("./config"),
	concat = require("gulp-concat"),
	rename = require("gulp-rename"),
	lineEC = require("gulp-line-ending-corrector"),
	bs = require("browser-sync").create();

/* --- Dependencies: css --- */
const cleanCSS = require("gulp-clean-css"), // Minify CSS
	autoprefixer = require("gulp-autoprefixer");

/* --- Dependencies: js --- */
const uglify = require("gulp-uglify"), // Minify JavaScript
	stripDebug = require("gulp-strip-debug"); // Remove debugging stuffs

/* --- Dependencies: i18n --- */
const wpPot = require("gulp-wp-pot"),
	sort = require("gulp-sort");

/*================================= Tasks =================================*/

let init = cb => {
	console.log("-------------------------------------------");
	console.log("<<<<<-------- WP Job Openings -------->>>>>");
	console.log("-------------------------------------------");
	cb();
};

/* --- Tasks: Browsersync --- */

let browserSync = cb => {
	bs.init({
		ghostMode: false,
		proxy: config.previewURL,
		notify: false
	});
	cb();
};
let bsReload = cb => {
	bs.reload();
	cb();
};
browserSync.description = `Initialize Browsersync and proxy ${config.previewURL}`;
gulp.task("browser-sync", browserSync);
// Task to compile SCSS to CSS
gulp.task('sass', function() {
    return gulp.src(scssFiles, { sourcemaps: config.debug ? true : false })
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(cleanCSS({ compatibility: 'ie9' }))
        .pipe(rename({ suffix: '.min' }))
        .pipe(lineEC())
        .pipe(gulp.dest(function(file) {
            return file.base; // Compiled CSS will be placed in the same directory as the SCSS file
        }, { sourcemaps: config.debug ? '.' : false }))
        .pipe(bs.stream());
});

/* --- Tasks: CSS --- */

for (let type in config.style) {
	let styleTask = () => {
		let src =
			type === "general"
				? [config.style.general.src + "*.css"]
				: [
						config.style[type].src + "vendors/*.css",
						config.style[type].src + "includes/*.css",
						config.style[type].src + "*.css"
				  ];
		let outputName = config.style[type].outputName;
		let dest = config.style[type].dest;

		return gulp
			.src(src, {sourcemaps: config.debug ? true : false})
			.pipe(concat(outputName))
			.pipe(autoprefixer())
			.pipe(cleanCSS({compatibility: "ie9"}))
			.pipe(rename({suffix: ".min"}))
			.pipe(lineEC())
			.pipe(gulp.dest(dest, {sourcemaps: config.debug ? "." : false}));
	};
	let loadStyleTask = () => {
		let src = config.style[type].dest;
		return gulp.src(src + "*.css").pipe(bs.stream());
	};
	styleTask.description = `Concatenate ${type} styles and minify it`;
	gulp.task(`${type}-style`, styleTask);
	gulp.task(`load-${type}-styles`, gulp.series(`${type}-style`, loadStyleTask));
}

/* --- Tasks: JS --- */

for (let type in config.scripts) {
	let scriptTask = () => {
		let src = [
			config.scripts[type].src + "vendors/*.js",
			config.scripts[type].src + "*.js"
		];
		let outputName = config.scripts[type].outputName;
		let dest = config.scripts[type].dest;

		let stream = gulp.src(src, {sourcemaps: config.debug ? true : false});
		if (!config.debug) {
			stream = stream.pipe(stripDebug());
		}
		stream = stream
			.pipe(concat(outputName))
			.pipe(uglify())
			.pipe(rename({suffix: ".min"}))
			.pipe(lineEC())
			.pipe(gulp.dest(dest, {sourcemaps: config.debug ? "." : false}));
		return stream;
	};
	scriptTask.description = `Concatenate ${type} js files and minify it`;
	gulp.task(`${type}-scripts`, scriptTask);
	gulp.task(`load-${type}-scripts`, gulp.series(`${type}-scripts`, bsReload));
}

/* --- Tasks: i18n --- */

let i18n = () => {
	return gulp
		.src(["./**/*.php", "!./build/**/*.php", "!./vendor/**/*.php"])
		.pipe(sort())
		.pipe(
			wpPot({
				domain: config.translation.domain,
				package: config.translation.package,
				team: config.translation.team
			})
		)
		.pipe(gulp.dest(config.translation.dest));
};
i18n.description = "Generates pot file for plugin localization";
gulp.task("translate", i18n);

/* --- Generic Tasks --- */

const genericTasks = [
	"general-style",
	"public-style",
	"admin-style",
	"admin-global-style",
	"admin-overview-style",
	"public-scripts",
	"admin-scripts",
	"admin-overview-scripts"
];

/* --- Tasks: Watch files for any change --- */

// Watch task for SCSS files
gulp.task('watch-sass', function() {
    gulp.watch(scssFiles, gulp.series('sass'));
});


let watchFiles = () => {
	gulp.watch("./**/*.php", bsReload);
	for (let type in config.style) {
		gulp.watch(
			config.style[type].src + "**/*.css",
			gulp.series(`load-${type}-styles`)
		);
	}
	for (let type in config.scripts) {
		gulp.watch(
			config.scripts[type].src + "**/*.js",
			gulp.series(`load-${type}-scripts`)
		);
	}
	gulp.watch(scssFiles, gulp.series('sass'));
};
watchFiles.description = "Watch PHP, JS and CSS files for any change";
gulp.task(
	"watch",
	gulp.series(browserSync, gulp.parallel(...genericTasks), watchFiles)
);

/* --- Tasks: Default tasks --- */

gulp.task(
	"default",
	gulp.series(
		init,
		gulp.parallel(...genericTasks),
		browserSync
	)
);

/* --- Tasks: Build tasks --- */

gulp.task(
	"build",
	gulp.series(
		init,
		gulp.parallel(...genericTasks)
	)
);


gulp.task(
    "watch",
    gulp.series(browserSync, gulp.parallel(...genericTasks, 'sass'), watchFiles)
);

gulp.task(
    "default",
    gulp.series(
        init,
        gulp.parallel(...genericTasks, 'sass'),
        browserSync
    )
);

gulp.task(
    "build",
    gulp.series(
        init,
        gulp.parallel(...genericTasks, 'sass')
    )
);
