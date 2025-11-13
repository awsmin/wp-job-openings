/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import icon from './icon';
import metadata from './block.json';
import { __ } from "@wordpress/i18n";

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType( metadata.name, {
	title: __( 'Job Listings', 'wp-job-openings' ), // Block title.
	description: __( 'Super simple Job Listing plugin to manage Job Openings and Applicants on your WordPress site.', 'wp-job-openings' ), // Block description
	icon: icon.block, // Block icon
	category: 'widgets', // Block category,
	keywords: [ __( 'jobs listings', 'wp-job-openings' ), __( 'add jobs', 'wp-job-openings' ), __( 'job application', 'wp-job-openings' ) ], // Access the block easily with keyword aliases
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,
} );
