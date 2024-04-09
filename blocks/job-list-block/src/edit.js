/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps,InspectorControls } from '@wordpress/block-editor';
import {Panel,PanelBody,SelectControl,TextControl,Snackbar} from '@wordpress/components';
/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
 import './editor.scss';
import ServerSideRender from '@wordpress/server-side-render';
import {useEntityRecords} from '@wordpress/core-data';
import { useSelect, useDispatch } from '@wordpress/data';
/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */


export default function Edit({attributes,setAttributes}) {

	const { records , hasResolved} = useEntityRecords();
	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<Panel>
					<PanelBody title = "Appearance" >
						
							<SelectControl
							 label = "Layout"
							 value = {attributes.layout}
							 options = {
								[
									{label: "List Layout",value :"list"},
									{label: "Grid Layout",value :"grid"}
								]
							 }
							 onChange ={(layout)=>setAttributes({layout})}
							/>
							
							<SelectControl
							 label = "Listing Order"
							 value = {attributes.listing_order}
							 options = {
								[
									{label: "Ascending",value :"ascending"},
									{label: "Descending",value :"descending"}
								]
							 }
							 onChange ={(listing_order)=>setAttributes({listing_order})}
							/>
						    {/* <TextControl
          					  label="Listing Per Page"
         					  value={ attributes.listing_per_page }
            				  onChange={ ( listing_per_page ) => setAttributes( listing_per_page ) }
       						 /> */}

					</PanelBody>
				</Panel>
			</InspectorControls>
			<ServerSideRender
			 block = "create-block/job-list-block"
			 attributes  = {attributes}
			/>
		</div>
	);
							}
