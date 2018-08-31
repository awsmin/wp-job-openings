/**
 * Automating Development Tasks
 * ----------------------------
 * @package wp-job-openings
 * @since 1.0.0
 */

"use strict";

/*============================= Dependencies =============================*/

const gulp = require("gulp-help")(require("gulp")),
	config = require("./config"),
	concat = require("gulp-concat"),
	rename = require("gulp-rename"),
	lineEC = require("gulp-line-ending-corrector"),
	sourcemaps = require("gulp-sourcemaps"), // Write inline source maps
	browserSync = require("browser-sync").create();

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

gulp.task("init", false, () => {
	console.log("-------------------------------------------");
	console.log("<<<<<-------- WP Job Openings -------->>>>>");
	console.log("-------------------------------------------");
});

/* --- Tasks: Browsersync --- */
gulp.task(
	"browser-sync",
	`Initialize Browsersync and proxy ${config.previewURL}`,
	() => {
		browserSync.init({
			ghostMode: false,
			proxy: config.previewURL,
			notify: false
		});
	}
);

/* --- Tasks: CSS --- */
function minifyStyles(type) {
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

	let stream = gulp.src(src);
	if (config.debug) {
		stream = stream.pipe(sourcemaps.init());
	}
	stream = stream
		.pipe(concat(outputName))
		.pipe(autoprefixer())
		.pipe(cleanCSS({compatibility: "ie9"}))
		.pipe(rename({suffix: ".min"}))
		.pipe(lineEC());
	if (config.debug) {
		stream = stream.pipe(sourcemaps.write()).pipe(lineEC());
	}
	return stream.pipe(gulp.dest(dest));
}

function loadStyles(type) {
	let src = config.style[type].dest;
	return gulp.src(src + "*.css").pipe(browserSync.stream());
}

for (let type in config.style) {
	gulp.task(`${type}-style`, `Concatenate ${type} styles and minify it`, () => {
		minifyStyles(type);
	});
	gulp.task(`load-${type}-styles`, false, [`${type}-style`], () => {
		loadStyles(type);
	});
}

/* --- Tasks: JS --- */
function minifyScripts(type) {
	let src = [
		config.scripts[type].src + "vendors/*.js",
		config.scripts[type].src + "*.js"
	];
	let outputName = config.scripts[type].outputName;
	let dest = config.scripts[type].dest;

	let stream = gulp.src(src);
	if (config.debug) {
		stream = stream.pipe(sourcemaps.init());
	} else {
		stream = stream.pipe(stripDebug());
	}
	stream = stream
		.pipe(concat(outputName))
		.pipe(uglify())
		.pipe(rename({suffix: ".min"}))
		.pipe(lineEC());
	if (config.debug) {
		stream = stream.pipe(sourcemaps.write()).pipe(lineEC());
	}
	return stream.pipe(gulp.dest(dest));
}

for (let type in config.scripts) {
	gulp.task(
		`${type}-scripts`,
		`Concatenate ${type} js files and minify it`,
		() => {
			minifyScripts(type);
		}
	);
	gulp.task(`load-${type}-scripts`, false, [`${type}-scripts`], () => {
		browserSync.reload();
	});
}

/* --- Tasks: i18n --- */
gulp.task("translate", "Generates pot file for plugin localization", () => {
	return gulp
		.src(["./**/*.php", "!./build/**/*.php"])
		.pipe(sort())
		.pipe(
			wpPot({
				domain: config.translation.domain,
				package: config.translation.package,
				team: config.translation.team
			})
		)
		.pipe(gulp.dest(config.translation.dest));
});

/* --- Tasks: Watch files for any change --- */
gulp.task(
	"watch",
	"Watch PHP, JS and CSS files for any change",
	["init", "browser-sync"],
	function() {
		gulp.watch("./**/*.php", function() {
			browserSync.reload();
		});
		gulp.watch(config.style.general.src + "**/*.css", ["load-general-styles"]);
		gulp.watch(config.style.public.src + "**/*.css", ["load-public-styles"]);
		gulp.watch(config.style.admin.src + "**/*.css", ["load-admin-styles"]);
		gulp.watch(config.scripts.public.src + "**/*.js", ["load-public-scripts"]);
		gulp.watch(config.scripts.admin.src + "**/*.js", ["load-admin-scripts"]);
	}
);

/* --- Tasks: Default tasks --- */
gulp.task("default", false, ["init", "help"]);

/* --- Tasks: Build tasks --- */
gulp.task("build", "Generate CSS and JS files to be included in the plugin", [
	"init",
	"general-style",
	"public-style",
	"admin-style",
	"public-scripts",
	"admin-scripts"
]);
