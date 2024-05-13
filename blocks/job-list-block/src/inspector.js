import {useEffect} from "@wordpress/element";
import {__} from "@wordpress/i18n";
import {InspectorControls} from "@wordpress/block-editor";
import {
	PanelBody,
	TextControl,
	TextareaControl,
	ToggleControl,
	SelectControl
} from "@wordpress/components";

const WidgetInspectorControls = props => {
	const {
		attributes: { awsmSpecsOptions,layout,listing_order},
		setAttributes
	} = props;

	const specs =  awsmJobsAdmin.awsm_filters; 
	
	useEffect(() => {
		if (specs.length > 0 && typeof awsmSpecsOptions === 'undefined') {
			let initialSpecs = specs.map(spec => spec.key);
			setAttributes( { awsmSpecsOptions: initialSpecs } );
		}
	});

	const specsHandler = (toggleValue, specKey) => {
		if (typeof awsmSpecsOptions !== 'undefined') {
			jQuery(".awsm-job-select-control").selectric('destroy');

			let modSpecsOptions = [...awsmSpecsOptions];
			if (! toggleValue) {
				modSpecsOptions = modSpecsOptions.filter(specOption => specOption !== specKey);
			} else {
				modSpecsOptions.push(specKey);
			}
			setAttributes( { awsmSpecsOptions: modSpecsOptions } );
		}
	};
	
	return (
		<InspectorControls>
			<PanelBody title = "Appearance" >
				<SelectControl
					label = "Layout"
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
					label = "Listing Order"
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
			<PanelBody title={__("Form", "job-alerts-for-wp-job-openings")}>
				{specs.length > 0 &&
					specs.map(spec => {
						return <ToggleControl label={spec.label} checked={typeof awsmSpecsOptions !== 'undefined' && awsmSpecsOptions.includes(spec.key)} onChange={ (toggleValue) => specsHandler(toggleValue, spec.key) } />;
					})}
			</PanelBody>
		</InspectorControls>
	);
};

export default WidgetInspectorControls;
