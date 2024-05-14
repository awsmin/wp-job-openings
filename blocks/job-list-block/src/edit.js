import {useEffect} from "@wordpress/element";
import {__} from "@wordpress/i18n";
import {InnerBlocks, useBlockProps} from "@wordpress/block-editor";

import WidgetInspectorControls from "./inspector";
import "./editor.scss";

export default function Edit(props) {
	const {
		attributes: { filter_options},
		setAttributes
	} = props;
	const blockProps = useBlockProps();
	
	let specs = awsmJobsAdmin.awsm_filters; 
	specs = specs.filter(spec => {
		if (
			typeof filter_options !== "undefined" &&
			filter_options.includes(spec.key)
		) {
			return spec;
		}
	});
	
	const awsmDropDown = $elem => {
		if (
			"selectric" in awsmJobsPublic.vendors &&
			awsmJobsPublic.vendors.selectric
		) {
			$elem.selectric({
				onInit: function(select, selectric) {
					var id = select.id;
					var $input = jQuery(selectric.elements.input);
					jQuery(select).attr("id", "selectric-" + id);
					$input.attr("id", id);
				},
				arrowButtonMarkup:
					'<span class="awsm-selectric-arrow-drop">&#x25be;</span>',
				customClass: {
					prefix: "awsm-selectric",
					camelCase: false
				}
			});
		}
	};

	useEffect(() => {
		awsmDropDown(jQuery(".awsm-job-select-control"));
	});
	
	return (
		<div {...blockProps}>
			<WidgetInspectorControls {...props} />
			<div className="awsm-jobs-alerts-widget-wrapper">
			{specs.length > 0 && (
				<div className="awsm-jobs-alerts-form-group awsm-jobs-alerts-specs-group">
					{specs.map(spec => {
						const dropDown = (
							<div className="awsm-jobs-alerts-specs-group-in">
							<select
								name={`awsm_job_alerts_spec[${spec.key}]`}
								className="awsm-job-select-control"
								id="awsm_job_alerts_specs"
								multiple
							>
							<option value="">{spec.label}</option>
							{spec.terms.map(term => {
								return <option value={term.term_id}>{term.name}</option>;
							})}
							</select>
							</div>
						);
						return dropDown;
					})}
				</div>
			)}
			</div>
		</div>
	);
};

