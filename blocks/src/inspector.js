import { useEffect, Fragment, useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { InspectorControls, BlockEdit } from "@wordpress/block-editor";
import { addFilter } from '@wordpress/hooks';
import {
	PanelBody,
	ToggleControl,
	TextControl,
	SelectControl,
	TabPanel,
	Button,
	__experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from "@wordpress/components";

const WidgetInspectorControls = props => {
	const {
		attributes: {
			filter_options,
			other_options,
			layout,
			listing_per_page,
			number_of_columns,
			search,
			pagination,
			enable_job_filter,
			search_placeholder,
			hide_expired_jobs
		},
		setAttributes
	} = props;

	let block_appearance_list = [];
	let block_job_listing = [];

	const specifications = awsmJobsAdmin.awsm_filters_block;
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

	const onchange_number_of_columns = (value) => {
		const columnsValue = parseInt(value, 10);		
		setAttributes({ number_of_columns: isNaN(columnsValue) ? 0 : columnsValue });
	};

	return (
		<InspectorControls>
			<PanelBody title={__("Search & Filters", "wp-job-openings")}>
				<ToggleControl
					label={__("Enable Search & Filters", "wp-job-openings")}
				/>

			<ToggleGroupControl
				label="Placement"
				value="top"
				isBlock
				__nextHasNoMarginBottom
				__next40pxDefaultSize
       		>
           		<ToggleGroupControlOption value="top" label="Top" />
            	<ToggleGroupControlOption value="slide" label="Slide" />
        	</ToggleGroupControl>
				
				<TextControl
					label={__("Search Placeholder", "wp-job-openings")}
					value={search_placeholder}
					onChange={(search_placeholder) =>
						setAttributes({ search_placeholder })
					}
					placeholder={__("Search Jobs", "wp-job-openings")}
				/>

				{enable_job_filter && (
					<>
						<h2>{__("Available Filters", "wp-job-openings")}</h2>
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

				<Button isPrimary>
                    {__("Dropdown", "my-text-domain")}
                </Button>

				<Button isPrimary>
                    {__("Checkbox", "my-text-domain")}
                </Button>

			</PanelBody>
			<PanelBody title={__("Job Listing", "wp-job-openings")}>
			    <h2>{__("List Type", "wp-job-openings")}</h2>
				<TabPanel
					className="placement-tab-panel"
					activeClass="is-active"
					tabs={[
						{
							name: 'all',
							title: 'All Jobs',
							className: 'placement-top',
						},
						{
							name: 'filtered',
							title: 'Filtered List',
							className: 'placement-slide',
						},
					]}
				>
					{(tab) => {
						if (tab.name === 'all') {
						}

						if (tab.name === 'filtered') {
						}

						return null;
					}}
				</TabPanel>
				<p> Disply all jobs or filtered by job specifications </p>

				<h2>{__("Layout", "wp-job-openings")}</h2>
				<TabPanel
					className="placement-tab-panel"
					activeClass="is-active"
					tabs={[
						{
							name: 'list',
							title: 'List',
							className: 'placement-top',
						},
						{
							name: 'grid',
							title: 'Grid',
							className: 'placement-slide',
						},
						{
							name: 'stack',
							title: 'Stack',
							className: 'placement-slide',
						},
					]}
				>
					{(tab) => {
						if (tab.name === 'list') {
						}

						if (tab.name === 'grid') {
						}

						if (tab.name === 'stack') {
						}

						return null;
					}}
				</TabPanel>

				<h2>{__("Available Filters", "wp-job-openings")}</h2>
					{specifications.map(spec => (
						<ToggleControl
							key={spec.key}
							label={spec.label}
							checked={filter_options.includes(spec.key)}
							onChange={toggleValue => specifications_handler(toggleValue, spec.key)}
						/>
				))}

				<SelectControl
					label={__("Order By", "wp-job-openings")}
					options={[
						{ label: __("Newest to oldest", "wp-job-openings"),  value: "new" },
						{ label: __("Oldest to newest", "wp-job-openings"), value: "old" },
					]}
				/>

				<ToggleControl
					label={__("Hide Expired Jobs", "wp-job-openings")}
					checked={hide_expired_jobs}
					onChange={hide_expired_jobs => setAttributes({ hide_expired_jobs })}
				/>

				{ wp.hooks.doAction( 'after_awsm_block_job_listing',block_job_listing,props ) }
				{ block_job_listing }

				<SelectControl
					label={__("Pagination", "wp-job-openings")}
					value={pagination}
					options={[
						{ label: __("Classic", "wp-job-openings"), value: "classic" },
						{ label: __("Modern", "wp-job-openings"), value: "modern" }
					]}
					onChange={pagination => setAttributes({ pagination })}
				/>

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
