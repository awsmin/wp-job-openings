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

const WidgetInspectorControls = (props) => {
	const {
	  attributes: {
		search,
		placement,
		filter_options,
		pagination,
		search_placeholder,
		hide_expired_jobs,
		orderBy,
		listType,
		jobsPerPage,
		layout,
		selectedTerms,
	  },
	  setAttributes,
	} = props;
  
	// Local state for block settings
	const specifications = awsmJobsAdmin.awsm_filters_block;
	const [isProEnabled, setIsProEnabled] = useState(false);
	const [selectedTermsState, setSelectedTermsState] = useState(selectedTerms || {});
  
	// Sync selected terms with props on mount or when selectedTerm changes
	useEffect(() => {
	  if (typeof awsmJobsAdmin !== "undefined" && awsmJobsAdmin.isProEnabled) {
		setIsProEnabled(true);
	  }
  
	  // Sync state with selectedTerms attribute
	  const initialSelectedTerms = specifications.reduce((acc, spec) => {
		acc[spec.key] = selectedTerms[spec.key] || []; // Initialize with existing selected terms or empty array
		return acc;
	  }, {});
  
	  setSelectedTermsState(initialSelectedTerms);
	}, [specifications, selectedTerms]);
  
	// Update filter options when toggling filters
	const specifications_handler = (toggleValue, specKey) => {
	  let modfilteroptions = [...filter_options];
	  const existingOptionIndex = modfilteroptions.findIndex(
		(option) => option.specKey === specKey
	  );
  
	  if (!toggleValue) {
		if (existingOptionIndex !== -1) {
		  modfilteroptions.splice(existingOptionIndex, 1);
		}
	  } else {
		if (existingOptionIndex === -1) {
		  modfilteroptions.push({ specKey: specKey, value: "dropdown" });
		}
	  }
	  setAttributes({ filter_options: modfilteroptions });
	};
  
	// Update filter value (dropdown or checkbox)
	const updateFilterValue = (newValue, specKey) => {
	  let modfilteroptions = [...filter_options];
	  const existingOptionIndex = modfilteroptions.findIndex(
		(option) => option.specKey === specKey
	  );
  
	  if (existingOptionIndex !== -1) {
		modfilteroptions[existingOptionIndex].value = newValue;
	  }
	  setAttributes({ filter_options: modfilteroptions });
	};
  
	// Handle the term selection/deselection
	const handleTermChange = (newTokens, specKey, spec) => {
		setSelectedTermsState((prevSelectedTerms) => {
			const updatedSelectedTerms = { ...prevSelectedTerms };
			const selectedTokens = [...newTokens];
	
			const newTermIds = newTokens
				.map((token) => {
					const term = spec.terms.find((t) => t.name === token);
					return term ? term.term_id : null;
				})
				.filter((id) => id !== null); // Filter out invalid IDs
	
			updatedSelectedTerms[specKey] = newTermIds;
	
		    //setAttributes({ selectedTerms: updatedSelectedTerms });
			console.log('Selected tokens:', updatedSelectedTerms);
	
			return updatedSelectedTerms;
		});
	};	
	
  
	return (
	  <InspectorControls>
		{/* Search and Filters */}
		<PanelBody title={__("Search & Filters", "wp-job-openings")}>
		  <ToggleControl
			label={__("Enable Search & Filters", "wp-job-openings")}
			checked={search}
			onChange={(search) => setAttributes({ search })}
		  />
  
		  {search && (
			<>
			  <ToggleGroupControl
				label="Placement"
				value={placement}
				onChange={(placement) => setAttributes({ placement })}
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
			  {specifications.map((spec) => {
				const filterOption = filter_options.find(
				  (option) => option.specKey === spec.key
				);
				return (
				  <div key={spec.key}>
					<ToggleControl
					  label={spec.label}
					  checked={filterOption !== undefined}
					  onChange={(toggleValue) =>
						specifications_handler(toggleValue, spec.key)
					  }
					/>
  
					{filterOption && (
					  <div style={{ marginTop: "10px" }}>
						<ToggleGroupControl
						  value={filterOption.value || "dropdown"}
						  onChange={(newValue) => updateFilterValue(newValue, spec.key)}
						  isBlock
						  __nextHasNoMarginBottom
						  __next40pxDefaultSize
						>
						  <ToggleGroupControlOption
							label={__("Dropdown", "wp-job-openings")}
							value="dropdown"
						  />
						  <ToggleGroupControlOption
							label={__("Checkbox", "wp-job-openings")}
							value="checkbox"
						  />
						</ToggleGroupControl>
					  </div>
					)}
				  </div>
				);
			  })}
			</>
		  )}
		</PanelBody>
  
		{/* Job Listing Settings */}
		<PanelBody title={__("Job Listing", "wp-job-openings")}>
		  <ToggleGroupControl
			label="List Type"
			value={listType}
			onChange={(listType) => setAttributes({ listType })}
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
			onChange={(layout) => setAttributes({ layout })}
			isBlock
			__nextHasNoMarginBottom
			__next40pxDefaultSize
		  >
			<ToggleGroupControlOption value="list" label="List" />
			<ToggleGroupControlOption value="grid" label="Grid" />
			<ToggleGroupControlOption value="stack" label="Stack" />
		  </ToggleGroupControl>
  
		  <h2>{__("Available Filters", "wp-job-openings")}</h2>
		  {specifications.map((spec) => (
			<div key={spec.key} className="filter-item">
			  <ToggleControl
				label={spec.label}
				checked={filter_options.includes(spec.key)}
				onChange={(toggleValue) =>
				  specifications_handler(toggleValue, spec.key)
				}
			  />
  
			  <FormTokenField
				value={(selectedTermsState[spec.key] || []).map((id) => {
				  const term = spec.terms.find((t) => t.term_id === id);
				  return term ? term.name : "";
				})}
				onChange={(newTokens) =>
				  handleTermChange(newTokens, spec.key, spec)
				} 
				suggestions={spec.terms.map((term) => term.name)} // Suggestions are term names
				label=""
			  />
			</div>
		  ))}
  
		  <SelectControl
			label={__("Order By", "wp-job-openings")}
			value={orderBy}
			options={[
			  { label: __("Newest to oldest", "wp-job-openings"), value: "new" },
			  { label: __("Oldest to newest", "wp-job-openings"), value: "old" },
			]}
			onChange={(orderBy) => setAttributes({ orderBy })}
		  />
  
		  <ToggleControl
			label={__("Hide Expired Jobs", "wp-job-openings")}
			checked={hide_expired_jobs}
			onChange={(hide_expired_jobs) => setAttributes({ hide_expired_jobs })}
		  />
  
		  <RangeControl
			label={__("Jobs Per Page", "my-text-domain")}
			onChange={(sliderValue) => setAttributes({ jobsPerPage: sliderValue })}
			value={jobsPerPage}
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
			  { label: __("Modern", "wp-job-openings"), value: "modern" },
			]}
			onChange={(pagination) => setAttributes({ pagination })}
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
