const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: () => ( {
		...defaultConfig.entry(),
		'job-status-panel': path.resolve(
			process.cwd(),
			'src/job-status-panel',
			'index.js'
		),
		'job-expiry-panel': path.resolve(
			process.cwd(),
			'src/job-expiry-panel',
			'index.js'
		),
	} ),
};
