import {useEffect} from "@wordpress/element";
import {__} from "@wordpress/i18n";
import {InspectorControls} from "@wordpress/block-editor";
import {
	PanelBody,
	ToggleControl,
	SelectControl
} from "@wordpress/components";

const WidgetInspectorControls = props => {
	const {
		attributes: {filter_options,layout,listing_order},
		setAttributes
	} = props;

	const specs = awsmJobsAdmin.awsm_filters; 
	
	useEffect(() => {
		if (specs.length > 0 && typeof filter_options === 'undefined') {
			let initialSpecs = specs.map(spec => spec.key);
			setAttributes( { filter_options: initialSpecs } );
		}
	});

	const specsHandler = (toggleValue, specKey) => {
		if (typeof filter_options !== 'undefined') {
			jQuery(".awsm-job-select-control").selectric('destroy');

			let modSpecsOptions = [...filter_options];
			if (! toggleValue) {
				modSpecsOptions = modSpecsOptions.filter(specOption => specOption !== specKey);
			} else {
				modSpecsOptions.push(specKey);
			}
			setAttributes( { filter_options: modSpecsOptions } );
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
						{label: "List Layout",value :"list"},
						{label: "Grid Layout",value :"grid"}
					]
					}
					onChange ={(layout)=>setAttributes({layout})}
				/>
				<SelectControl
					label = {__("Listing Order", "wp-job-openings")}
					value = {listing_order}
					options = {
					[
						{label: "Ascending",value :"ascending"},
						{label: "Descending",value :"descending"}
					]
					}
					onChange ={(listing_order)=>setAttributes({listing_order})}
				/>
			</PanelBody>
			<PanelBody title={__("Filter Options", "wp-job-openings")}>
				{specs.length > 0 &&
					specs.map(spec => {
						return <ToggleControl label={spec.label} checked={typeof filter_options !== 'undefined' && filter_options.includes(spec.key)} onChange={ (toggleValue) => specsHandler(toggleValue, spec.key) } />;
					})}
			</PanelBody>
		</InspectorControls>
	);
};

export default WidgetInspectorControls;
