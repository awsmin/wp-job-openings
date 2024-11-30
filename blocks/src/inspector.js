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
	FormTokenField,
	RangeControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from "@wordpress/components";

const WidgetInspectorControls = props => {
	const {
		attributes: {
			filter_options,
			other_options,
			listing_per_page,
			number_of_columns,
			search,
			pagination,
			enable_job_filter,
			search_placeholder,
			hide_expired_jobs,
		},
		setAttributes
	} = props;

	let block_appearance_list = [];
	let block_job_listing = [];


	// Local state for block settings
	const specifications = awsmJobsAdmin.awsm_filters_block;
	const [isProEnabled, setIsProEnabled] = useState(false);
	const [sliderValue, setSliderValue] = useState(50);
	const [placement, setPlacement] = useState("top");
	const [filter_type, setFilterType] = useState("dropdown");
	const [listType, setListType] = useState("all");
	const [layout, setLayout] = useState("list");
	const [orderBy, setOrderBy] = useState('new');
	const [selectedTerm, setSelectedTerm] = useState({}); // Will store term selections by spec key
	const [selectedTerms, setSelectedTerms] = useState([]); // Local state for terms in the token field

	// Handle changes to the placement setting
	const handlePlacementChange = (newValue) => {
		setPlacement(newValue);
	};

	// Handle changes to the filter type (dropdown or checkbox)
	const handleFilterTypeChange = (newValue) => { 
		setFilterType(newValue);
	};

	// Handle changes to the list type (all or filtered)
	const handleListTypeChange = (newValue) => {
		setListType(newValue);
	};

	// Handle changes to the layout (list, grid, stack)
	const handleLayoutChange = (newValue) => {
		setLayout(newValue);
	};

	// Handle changes to the order by setting
	const handleOrderChange = (newValue) => {
		setOrderBy(newValue);
	};

	// Handle term selection changes in the token field
	const handleTermChange = (newTokens, specKey, spec) => {
		// Convert token names back to term_ids
		const newTermIds = newTokens.map(token => {
		  const term = spec.terms.find(t => t.name === token);
		  return term ? term.term_id : null;
		});
	  
		setSelectedTerms(prev => ({
		  ...prev,
		  [specKey]: newTermIds.filter(id => id !== null), // Save only valid term IDs
		}));
	};
	// Sync selected terms with props on mount or when selectedTerm changes
	useEffect(() => {
		if (specifications.length > 0 && typeof filter_options === "undefined") {
			let initialspecs = specifications.map(spec => spec.value);
			setAttributes({ filter_options: initialspecs });
		}

		// Set the pro add-on status
		if (typeof awsmJobsAdmin !== "undefined" && awsmJobsAdmin.isProEnabled) {
			setIsProEnabled(true);
		}

		// Ensure the selected terms are populated correctly based on the spec key
		if (specifications && specifications.length > 0) {
			specifications.forEach(spec => {
				setSelectedTerms(prev => ({
					...prev,
					[spec.key]: selectedTerm[spec.key] || [],
				}));
			});
		}
	}, [selectedTerm, specifications]);

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

	return (
		<InspectorControls>
			<PanelBody title={__("Search & Filters", "wp-job-openings")}>
				<ToggleControl
					label={__("Enable Search & Filters", "wp-job-openings")}
					checked={search}
					onChange={search => setAttributes({ search })}
				/>

				<ToggleGroupControl
					label="Placement"
					value={placement}
					onChange={handlePlacementChange}
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

				<h2>{__("Available Filters", "wp-job-openings")}</h2>
				{specifications.map(spec => (
					<ToggleControl
						key={spec.key}
						label={spec.label}
						checked={filter_options.includes(spec.key)}
						onChange={toggleValue => specifications_handler(toggleValue, spec.key)}
					/>
				))}

				<ToggleGroupControl
					value={filter_type}
					onChange={handleFilterTypeChange}
					isBlock
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				>
					<ToggleGroupControlOption value="dropdown" label="Dropdown" />
					<ToggleGroupControlOption value="checkbox" label="Checkbox" />
				</ToggleGroupControl>
			</PanelBody>

			<PanelBody title={__("Job Listing", "wp-job-openings")}>
				<ToggleGroupControl
					label="List Type"
					value={listType}
        			onChange={handleListTypeChange}
					isBlock
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				>
					<ToggleGroupControlOption value="all" label="All Jobs" />
					<ToggleGroupControlOption value="filtered" label="Filtered List" />
				</ToggleGroupControl>
				<p> Display all jobs or filtered by job specifications </p>
				
				<ToggleGroupControl
					label="Layout"
					value={layout}
        			onChange={handleLayoutChange}
					isBlock
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				>
					<ToggleGroupControlOption value="list" label="List" />
					<ToggleGroupControlOption value="grid" label="Grid" />
					<ToggleGroupControlOption value="stack" label="Stack" />
				</ToggleGroupControl>

				<h2>{__("Available Filters", "wp-job-openings")}</h2>
				{specifications.map(spec => (
					<div key={spec.key} className="filter-item">
						{/* ToggleControl for the specification */}
						<ToggleControl
						label={spec.label}
						checked={filter_options.includes(spec.key)}
						onChange={toggleValue => specifications_handler(toggleValue, spec.key)}
						/>

						{specifications && specifications.length > 0 && specifications.map((spec) => (
							spec.terms && spec.terms.length > 0 && (
								<FormTokenField
								label={__("Select Terms", "wp-job-openings")}
								value={selectedTerms[spec.key] ? selectedTerms[spec.key].map(id => {
									// Find the term by term_id (id is coming from selectedTerms)
									const term = spec.terms.find(term => term.term_id === id);
									return term ? term.name : ''; // Return the term name or an empty string
								}) : []} // Display selected term names as tokens
								onChange={(newTokens) => handleTermChange(newTokens, spec.key,spec)} // Update selected terms
								placeholder={__("Add terms", "wp-job-openings")}
								suggestions={spec.terms.map(term => term.name)} // Suggestion list based on terms
								/>
							)
						))}
					</div>
				))}

				<SelectControl
					label={__("Order By", "wp-job-openings")}
					value={orderBy}
					options={[
						{ label: __("Newest to oldest", "wp-job-openings"),  value: "new" },
						{ label: __("Oldest to newest", "wp-job-openings"), value: "old" },
					]}
					onChange={handleOrderChange}
				/>

				<ToggleControl
					label={__("Hide Expired Jobs", "wp-job-openings")}
					checked={hide_expired_jobs}
					onChange={hide_expired_jobs => setAttributes({ hide_expired_jobs })}
				/>

				{ wp.hooks.doAction( 'after_awsm_job_appearance', block_appearance_list, props ) }
				{ block_appearance_list }

				{ wp.hooks.doAction( 'after_awsm_block_job_listing', block_job_listing, props ) }
				{ block_job_listing }

				<RangeControl
                        label={__("Jobs Per Page", "my-text-domain")}
                        onChange={(value) => setSliderValue(value)}
						value={sliderValue}
                        min={1} 
                        max={10} 
                        step={1}
                        withInputField={true}
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
