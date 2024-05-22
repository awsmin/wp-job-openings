/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import {__} from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
// import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
import {useEffect} from "@wordpress/element";
import {InnerBlocks, useBlockProps} from "@wordpress/block-editor";

import ServerSideRender from '@wordpress/server-side-render';

import WidgetInspectorControls from "./inspector";
import {useSelect} from "@wordpress/data";
export default function Edit(props) {
	const {
		attributes: { filter_options, layout, search, enable_job_filter,search_placeholder},
		setAttributes,
	} = props;
	console.log(props.attributes);

	const blockProps = useBlockProps();

	let specifications = awsmJobsAdmin.awsm_filters;
	specifications = specifications.filter(spec => {
		if (
			typeof filter_options !== "undefined" &&
			filter_options.includes(spec.key)
		) {
			return spec;
		}
	});

	return (
		<div {...blockProps}>
			<WidgetInspectorControls {...props} />
			<ServerSideRender
			 block = "wp-job-openings/wjo-block"
			 attributes  = {props.attributes}
			/>
		</div>
	);
}
