import { __ } from '@wordpress/i18n';
import { useBlockProps,InspectorControls } from '@wordpress/block-editor';
import {Panel,PanelBody,SelectControl,TextControl,Snackbar,ToggleControl} from '@wordpress/components';
import './editor.scss';
import ServerSideRender from '@wordpress/server-side-render';
import {useEntityRecords} from '@wordpress/core-data';
import { useSelect, useDispatch } from '@wordpress/data';

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
							
						<ToggleControl
                            label="Show Filter"
                            checked={attributes.show_filter_flag}
                            onChange={(show_filter_flag) => setAttributes({ show_filter_flag })}
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
