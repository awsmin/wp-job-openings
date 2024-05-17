import {useEffect} from "@wordpress/element";
import {__} from "@wordpress/i18n";
import {InspectorControls} from "@wordpress/block-editor";
import {
	PanelBody,
	ToggleControl,
	TextControl,
	SelectControl
} from "@wordpress/components";

const WidgetInspectorControls = props => {
	const {
		attributes: {filter_options,layout,search,pagination,enable_job_filter,search_placeholder},
		setAttributes
	} = props;

	const specifications = awsmJobsAdmin.awsm_filters; 
	
	useEffect(() => {
		if (specifications.length > 0 && typeof filter_options === 'undefined') {
			let initialspecs = specifications.map(spec => spec.key);
			setAttributes( { filter_options: initialspecs } );
		}
	});

	const specifications_handler = (toggleValue, specKey) => {
		if (typeof filter_options !== 'undefined') {
			jQuery(".awsm-job-select-control").selectric('destroy');

			let modfilteroptions = [...filter_options];
			if (! toggleValue) {
				modfilteroptions = modfilteroptions.filter(specOption => specOption !== specKey);
			} else {
				modfilteroptions.push(specKey);
			}
			setAttributes( { filter_options: modfilteroptions } );
		}
	};
	
	return (
		<InspectorControls>
			<PanelBody title={__("Appearance", "wp-job-openings")}>
				<SelectControl
					label = {__("Layout", "wp-job-openings")}
					value = {layout}
					options = {
					[
						{ label: __('List Layout', 'wp-job-openings'),value :"list" },
						{ label: __('Grid Layout', 'wp-job-openings'),value :"grid" }
					]
					}
					onChange ={(layout)=>setAttributes({layout})}
				/>
				
				<SelectControl
					label = {__("Pagination", "wp-job-openings")}
					value = {pagination}
					options = {
					[
						{label: __('Classic', 'wp-job-openings'),value :"classic"},
						{label: __('Modern', 'wp-job-openings'),value :"modern"}
					]
					}
					onChange ={(pagination)=>setAttributes({pagination})}
				/>

				<ToggleControl label={ __( 'Enable Search', 'wp-job-openings' ) } checked={ search } onChange={ search => setAttributes( { search } ) } />

				<TextControl
					label={__("Search Placeholder", "wp-job-openings")}
					value={ search_placeholder }
					onChange={ search_placeholder => setAttributes( { search_placeholder } ) }
				/>

				<ToggleControl label={ __( 'Enable Job Filters', 'wp-job-openings' ) } checked={ enable_job_filter } onChange={ enable_job_filter => setAttributes( { enable_job_filter } ) } />

				{/* <SelectControl
					label = {__("Listing Order", "wp-job-openings")}
					value = {listing_order}
					options = {
					[
						{label: __('Ascending', 'wp-job-openings'),value :"ascending"},
						{label: __('Descending', 'wp-job-openings'),value :"descending"}
					]
					}
					onChange ={(listing_order)=>setAttributes({listing_order})}
				/> */}
			</PanelBody>
			{enable_job_filter && enable_job_filter == 1 &&
			<PanelBody title={__("Filter Options", "wp-job-openings")}>
				{specifications.length > 0 &&
					specifications.map(spec => {
						return <ToggleControl label={spec.label} checked={typeof filter_options !== 'undefined' && filter_options.includes(spec.key)} onChange={ (toggleValue) => specifications_handler(toggleValue, spec.key) } />;
					})}
			</PanelBody>
			}
		</InspectorControls>
	);
};

export default WidgetInspectorControls;
