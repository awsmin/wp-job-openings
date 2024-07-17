import { useEffect, Fragment, useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { InspectorControls, BlockEdit } from "@wordpress/block-editor";
import { addFilter } from '@wordpress/hooks';
import {
	PanelBody,
	ToggleControl,
	TextControl,
	SelectControl
} from "@wordpress/components";

const WidgetInspectorControls = props => {
	const {
		attributes: {
			filter_options,
			other_options,
			layout,
			listing_per_page,
			search,
			pagination,
			enable_job_filter,
			search_placeholder,
			hide_expired_jobs
		},
		setAttributes
	} = props;

	const specifications 	   = awsmJobsAdmin.awsm_filters_block;
	const [isProEnabled, setIsProEnabled] = useState(false);

	useEffect(() => {
		if (specifications.length > 0 && typeof filter_options === "undefined") {
			let initialspecs = specifications.map(spec => spec.value);
			setAttributes({ filter_options: initialspecs });
		}

		// Set the pro add-on status
		if (typeof awsmJobsAdmin !== "undefined" && awsmJobsAdmin.isProEnabled) {
			setIsProEnabled(true);
		}
	}, []);

	const specifications_handler = (toggleValue, specKey) => {
		if (typeof filter_options !== "undefined") {
			jQuery(".awsm-job-select-control").selectric("destroy");

			let modfilteroptions = [...filter_options];
			if (!toggleValue) {
				modfilteroptions = modfilteroptions.filter(
					specOption => specOption !== specKey
				);
			} else {
				modfilteroptions.push(specKey);
			}
			setAttributes({ filter_options: modfilteroptions });
		}
	};
	const other_options_handler = (toggleValue, specKey) => { 
		if (typeof other_options !== "undefined") {
			jQuery(".awsm-job-select-control").selectric("destroy");

			let modfilteroptions = [...other_options];
			if (!toggleValue) { 
				modfilteroptions = modfilteroptions.filter(
					specOption => specOption !== specKey
				);
			} else {
				modfilteroptions.push(specKey);
			} 
			setAttributes({ other_options: modfilteroptions });
		}
	};

	const onchange_listing_per_page = (value) => {
		const numberValue = parseInt(value, 10);
		setAttributes({ listing_per_page: isNaN(numberValue) ? 0 : numberValue });
	};

	return (
		<InspectorControls>
			<PanelBody title={__("Appearance", "wp-job-openings")}>
				<SelectControl
					label={__("Layout", "wp-job-openings")}
					value={layout}
					options={[
						{ label: __("List Layout", "wp-job-openings"), value: "list" },
						{ label: __("Grid Layout", "wp-job-openings"), value: "grid" }
					]}
					onChange={layout => setAttributes({ layout })}
				/>

				<TextControl
					label={__("Listing per page", "wp-job-openings")}
					value={listing_per_page}
					onChange={(listing_per_page) => onchange_listing_per_page(listing_per_page)}
				/>

				<SelectControl
					label={__("Pagination", "wp-job-openings")}
					value={pagination}
					options={[
						{ label: __("Classic", "wp-job-openings"), value: "classic" },
						{ label: __("Modern", "wp-job-openings"), value: "modern" }
					]}
					onChange={pagination => setAttributes({ pagination })}
				/>

				<ToggleControl
					label={__("Hide Expired Jobs", "wp-job-openings")}
					checked={hide_expired_jobs}
					onChange={hide_expired_jobs => setAttributes({ hide_expired_jobs })}
				/>

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
			{specifications.length > 0 && (
				<PanelBody title={__("Filter Options", "wp-job-openings")}>
					<ToggleControl
						label={__("Enable Search", "wp-job-openings")}
						checked={search}
						onChange={search => setAttributes({ search })}
					/>

					{search && (
						<TextControl
							label={__("Search Placeholder", "wp-job-openings")}
							value={search_placeholder}
							onChange={search_placeholder => setAttributes({ search_placeholder })}
						/>
					)}

					<ToggleControl
						label={__("Enable Job Filters", "wp-job-openings")}
						checked={enable_job_filter}
						onChange={enable_job_filter => setAttributes({ enable_job_filter })}
					/>

					{enable_job_filter && (
						<>
							<h2>Available filters</h2>
							{specifications.map(spec => (
								<ToggleControl
									key={spec.key}
									label={spec.label}
									checked={filter_options.includes(spec.key)}
									onChange={toggleValue => specifications_handler(toggleValue, spec.key)}
								/>
							))}
						</>
					)}
				</PanelBody>
			)}

			<PanelBody title={__("Other Options", "wp-job-openings")}>
				<h2>Job specs in the listing</h2>
				{specifications.length > 0 &&
					specifications.map(spec => {
						return (
							<ToggleControl
								label={spec.label}
								checked={
									typeof other_options !== "undefined" &&
									other_options.includes(spec.key)
								}
								onChange={toggleValue =>
									other_options_handler(toggleValue, spec.key)
								}
							/>
						);
					})}

			</PanelBody>
		</InspectorControls>
	);
};

// Define the HOC to add custom inspector controls
const withCustomInspectorControls = (BlockEdit) => (props) => {
	if (props.name !== 'wp-job-openings/blocks') {
		return <BlockEdit {...props} />;
	}

	return (
		<Fragment>
			<BlockEdit {...props} />
			<WidgetInspectorControls {...props} />
		</Fragment>
	);
};

// Add the filter to extend the block's inspector controls
addFilter(
	'editor.BlockEdit',
	'awsm-job-block-settings/awsm-block-inspector-controls',
	withCustomInspectorControls
);

export default withCustomInspectorControls;
