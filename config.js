/**
 * config file for development
 * ---------------------------
 * @package wp-job-openings
 * @since 1.0.0
 */

"use strict";

const path = require("path");
const assets_DIR = "./assets/";
const DEV_URL = process.env.DEV_URL || "localhost";
const NODE_ENV = process.env.NODE_ENV || "development";

module.exports = {
	previewURL: DEV_URL,
	debug: NODE_ENV == "development" ? true : false,
	style: {
		general: {
			src: assets_DIR + "css/general/",
			dest: assets_DIR + "css/",
			outputName: "general.css"
		},
		public: {
			src: assets_DIR + "css/public/",
			dest: assets_DIR + "css/",
			outputName: "style.css"
		},
		admin: {
			src: assets_DIR + "css/admin/",
			dest: assets_DIR + "css/",
			outputName: "admin.css"
		},
		"admin-global": {
			src: assets_DIR + "css/admin-global/",
			dest: assets_DIR + "css/",
			outputName: "admin-global.css"
		},
		"admin-overview": {
			src: assets_DIR + "css/admin-overview/",
			dest: assets_DIR + "css/",
			outputName: "admin-overview.css"
		}
	},
	scripts: {
		public: {
			src: assets_DIR + "js/public/",
			dest: assets_DIR + "js/",
			outputName: "script.js"
		},
		admin: {
			src: assets_DIR + "js/admin/",
			dest: assets_DIR + "js/",
			outputName: "admin.js"
		},
		"admin-overview": {
			src: assets_DIR + "js/admin-overview/",
			dest: assets_DIR + "js/",
			outputName: "admin-overview.js"
		}
	},
	translation: {
		domain: "wp-job-openings",
		package: "WP Job Openings",
		team: "AWSM innovations <hello@awsm.in>",
		dest: "./languages/wp-job-openings.pot"
	}
};
