import { __ } from '@wordpress/i18n';
import { useEffect,useRef, Fragment, useState } from '@wordpress/element';
import { InspectorControls,BlockEdit,useBlockProps,PanelColorSettings, __experimentalBorderRadiusControl as BorderRadiusControl} from '@wordpress/block-editor';
import { addFilter } from '@wordpress/hooks';

import {
    PanelBody,
	TabPanel,
    RangeControl,
    ToggleControl,
	TextControl,
	SelectControl,
	Button,
	FormTokenField,
	__experimentalInputControl as InputControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	BoxControl,
	__experimentalSpacer as Spacer,
	BorderControl,
	PanelRow
} from '@wordpress/components';

const WidgetInspectorControls = (props) => {
    const {
		attributes: {
			search,
			placement,
			filter_options,
			pagination,
			search_placeholder,
			hide_expired_jobs,
			order_by,
			list_type,
			listing_per_page,
			layout,
			selected_terms,
			selected_terms_main,
			number_of_columns,
			other_options,
			show_spec_icon,
			hz_sf_border = {},
			hz_sf_border_radius = {},
			hz_sf_padding = {},
			hz_ls_border = {},
			hz_ls_border_radius = {},
			hz_jl_border = {},
			hz_jl_border_radius = {},
			hz_jl_padding = {},
			hz_bs_border = {},
			hz_bs_border_radius = {},
			hz_bs_padding = {},
			hz_button_background_color,
			hz_button_text_color,
			hz_sidebar_width,
			blockId,
		},
		setAttributes,
		clientId,
	} = props;

	// Local state for block settings
	const filtersInitRef = useRef(false);
	const specifications = window.awsmJobsAdmin?.awsm_filters_block || [];
	const [ toggleState, setToggleState ] = useState(
		selected_terms_main || {}
	);
	
	const [ selectedTermsState, setSelectedTermsState ] = useState(
		selected_terms || {}
	);

	const block_appearance_list = [];
	const block_job_listing = [];
	const block_styles_panel = [];

	useEffect(() => {
		if (clientId && !blockId) {
			setAttributes({ blockId: `job-block-${clientId}` });
		}
	}, [clientId, blockId]);

	useEffect(() => {
		if (!specifications?.length) return;

		const initialSelectedTerms = specifications.reduce((acc, spec) => {
			acc[spec.key] = selected_terms?.[spec.key] || [];
			return acc;
		}, {});

		setSelectedTermsState(initialSelectedTerms);
	}, [specifications, selected_terms]);

	useEffect(() => {
		const initialToggle = Array.isArray(selected_terms_main)
			? selected_terms_main.reduce((acc, key) => {
				acc[key] = true;
				return acc;
			}, {})
			: {};

		setToggleState(initialToggle);
	}, [selected_terms_main]);

	useEffect(() => {
		if (!filtersInitRef.current && specifications?.length > 0) {
			const normalizedFilters = filter_options?.length
				? filter_options.map(option =>
					typeof option === 'object' && option.specKey
						? option
						: { specKey: option, value: 'dropdown' }
				)
				: specifications.map(spec => ({ specKey: spec.key, value: 'dropdown' }));

			setAttributes({ filter_options: normalizedFilters });
			filtersInitRef.current = true;
		}
	}, [specifications, filter_options]);

	const handleTermChange = (newTokens, specKey, spec) => {
		const newTermIds = newTokens
			.map((token) => {
				const term = spec.terms.find((t) => t.name === token);
				return term ? term.term_id : null;
			})
			.filter((id) => id !== null);

		const updatedSelectedTerms = {
			...selectedTermsState,
			[specKey]: newTermIds,
		};

		// Update filter type if multiple terms selected
		const updatedFilters = filter_options.map((option) =>
			option.specKey === specKey
				? { ...option, value: newTermIds.length > 1 ? 'checkbox' : 'dropdown' }
				: option
		);

		setSelectedTermsState(updatedSelectedTerms);
		setAttributes({
			selected_terms: updatedSelectedTerms,
			filter_options: updatedFilters,
		});
	};

	const handleToggleChange = (specKey, isChecked) => {
		const updatedTermsMain = isChecked
			? [...new Set([...(selected_terms_main || []), specKey])]
			: (selected_terms_main || []).filter((key) => key !== specKey);

		const updatedSelectedTerms = { ...selectedTermsState };
		if (!isChecked) {
			delete updatedSelectedTerms[specKey];
		}

		setToggleState((prev) => ({ ...prev, [specKey]: isChecked }));
		setSelectedTermsState(updatedSelectedTerms);
		setAttributes({
			selected_terms_main: updatedTermsMain,
			selected_terms: updatedSelectedTerms,
		});
	};

	const onchange_number_of_columns = ( value ) => {
		const columnsValue = parseInt( value, 10 );
		setAttributes( {
			number_of_columns: isNaN( columnsValue ) ? 0 : columnsValue,
		} );
	};

	const other_options_handler = (toggleValue, specKey) => { 
		let updated = [...other_options];
		if (toggleValue) {
			if (!updated.includes(specKey)) {
				updated.push(specKey);
			}
		} else {
			updated = updated.filter((key) => key !== specKey);
		}
		setAttributes({ other_options: updated });
	};
	
    return (
		<>
		    {/* Control settings  */}
			<InspectorControls group="settings">
 				<Fragment>
					<PanelBody title={__('Search & Filters', 'wp-job-openings')} initialOpen={true}>
						<ToggleControl
							label={ __( 'Enable Search & Filters', 'wp-job-openings' ) }
							checked={ search }
							onChange={ ( search ) => setAttributes( { search } ) }
						/>
						
						{ search && (
							<>
								<ToggleGroupControl
									label="Placement"
									value={ placement }
									onChange={ ( newPlacement ) => setAttributes( { placement: newPlacement } ) }
									isBlock
									__nextHasNoMarginBottom
									__next40pxDefaultSize
								>
									<ToggleGroupControlOption value="top" label="Top" />
									<ToggleGroupControlOption value="side" label="Side" />
								</ToggleGroupControl>

								<TextControl
									label={ __(
										'Search Placeholder',
										'wp-job-openings'
									) }
									value={ search_placeholder }
									onChange={ ( search_placeholder ) =>
										setAttributes( { search_placeholder } )
									}
									placeholder={ __(
										'Search Jobs',
										'wp-job-openings'
									) }
								/>

								<h2>
									{ __( 'Available Filters', 'wp-job-openings' ) }
								</h2>
								{ specifications.map( ( spec ) => {
									const filterOption = filter_options.find(
										( option ) => option.specKey === spec.key
									);

									// Check if there are multiple selected terms for the specKey
									const hasMultipleSelectedTerms =
										( selectedTermsState[ spec.key ] || [] )
											.length > 1;

									return (
										<div key={ spec.key }> 
											{ /* Toggle Control */ }
											<ToggleControl
												label={ spec.label }
												checked={ filterOption !== undefined }
												onChange={ ( toggleValue ) => { 
													const updatedFilters = toggleValue
														? [
																...filter_options,
																{
																	specKey: spec.key,
																	value: hasMultipleSelectedTerms
																		? 'checkbox'
																		: 'dropdown',
																},
														] // Choose checkbox if multiple terms are selected
														: filter_options.filter(
																( option ) =>
																	option.specKey !==
																	spec.key
														); // Remove the filter

													// Update attributes to trigger re-render
													setAttributes( {
														filter_options: updatedFilters,
													} );
												} }
											/>

											{ /* If a filter option exists, show buttons */ }
											{ filterOption && (
												<div className="filters-button">
													{ /* Dropdown Button */ }
													<Button
														className={ `filter-btn ${ filterOption.value === 'dropdown' ? 'is-dropdown' : '' }` }
														variant="secondary"
														size="default"
														__next40pxDefaultSize
														onClick={ () => {
															const updatedFilters =
																filter_options.map(
																	( option ) =>
																		option.specKey ===
																		spec.key
																			? {
																					...option,
																					value: 'dropdown',
																			}
																			: option
																);
															setAttributes( {
																filter_options:
																	updatedFilters,
															} ); // Update attributes
														} }
													>
														{ __(
															'Single Select',
															'wp-job-openings'
														) }
													</Button>

													{ /* Checkbox Button */ }
													<Button
														variant="secondary"
														style={ {
															backgroundColor:
																filterOption.value ===
																'checkbox'
																	? 'black'
																	: 'initial',
															color:
																filterOption.value ===
																'checkbox'
																	? 'white'
																	: 'black',
														} }
														__next40pxDefaultSize
														onClick={ () => {
															const updatedFilters =
																filter_options.map(
																	( option ) =>
																		option.specKey ===
																		spec.key
																			? {
																					...option,
																					value: 'checkbox',
																			}
																			: option
																);
															setAttributes( {
																filter_options:
																	updatedFilters,
															} ); // Update attributes
														} }
													>
														{ __(
															'Multi Select',
															'wp-job-openings'
														) }
													</Button>
												</div>
											) }
										</div>
									);
								} ) }
							</>
						) }
					</PanelBody>
					
					<PanelBody title={ __( 'Layout Settings', 'wp-job-openings' ) } initialOpen={true}>
						<ToggleGroupControl
							label="Layout"
							value={ layout }
							onChange={ ( layout ) => setAttributes( { layout } ) }
							isBlock
							__nextHasNoMarginBottom
							__next40pxDefaultSize
						>
							<ToggleGroupControlOption value="list" label={ __( 'List', 'wp-job-openings' ) } />
							<ToggleGroupControlOption value="grid" label={ __( 'Grid', 'wp-job-openings' ) } />
							<ToggleGroupControlOption value="stack" label={ __( 'Stack', 'wp-job-openings' ) } />
						</ToggleGroupControl>

						{ typeof layout !== 'undefined' && layout == 'grid' && (
							<SelectControl
								label={ __( 'Columns', 'wp-job-openings' ) }
								value={ number_of_columns }
								options={ [
									{
										label: __( '1 Column', 'wp-job-openings' ),
										value: '1',
									},
									{
										label: __( '2 Columns', 'wp-job-openings' ),
										value: '2',
									},
									{
										label: __( '3 Columns', 'wp-job-openings' ),
										value: '3',
									},
									{
										label: __( '4 Columns', 'wp-job-openings' ),
										value: '4',
									},
								] }
								onChange={ ( number_of_columns ) =>
									onchange_number_of_columns( number_of_columns )
								}
							/>
						) }

						{ wp.hooks.doAction(
							'after_awsm_job_appearance',
							block_appearance_list,
							props
						) }
						{ block_appearance_list }

						<RangeControl
							label={ __( 'Jobs Per Page', 'wp-job-openings' ) }
							onChange={ ( sliderValue ) =>
								setAttributes( { listing_per_page: sliderValue } )
							}
							value={ listing_per_page }
							min={ 1 }
							max={ 100 }
							step={ 1 }
							withInputField={ true }
						/>

						<SelectControl
							label={ __( 'Pagination', 'wp-job-openings' ) }
							value={ pagination }
							options={ [
								{
									label: __( 'Classic', 'wp-job-openings' ),
									value: 'classic',
								},
								{
									label: __( 'Modern', 'wp-job-openings' ),
									value: 'modern',
								},
							] }
							onChange={ ( pagination ) =>
								setAttributes( { pagination } )
							}
						/>

						<h2>{__("Job Specs in the Listing", "wp-job-openings")}</h2>

						{specifications.map((spec) => (
							<ToggleControl
								key={spec.key}
								label={spec.label}
								checked={Array.isArray(other_options) && other_options.includes(spec.key)}
								onChange={(toggleValue) => other_options_handler(toggleValue, spec.key)}
							/>
						))}

						<h2>{__("Show Spec icon in the Listing", "wp-job-openings")}</h2>
						<ToggleControl
							label={ __( 'Show spec icon', 'wp-job-openings' ) }
							checked={ show_spec_icon }
							onChange={ ( show_spec_icon ) => setAttributes( { show_spec_icon } ) }
						/>

					</PanelBody>

					<PanelBody title={ __( 'Job Listing', 'wp-job-openings' ) }>
					<ToggleGroupControl
							label={ __( 'List Type', 'wp-job-openings' ) }
							value={ list_type }
							onChange={ ( newlist_type ) => {
								setAttributes( { list_type: newlist_type } );

								// Clear all items in selected_terms if list_type is set to "all"
								if ( newlist_type === 'all' ) {
									const clearedTerms = {};
									specifications.forEach( ( spec ) => {
										clearedTerms[ spec.key ] = [];
									} );
									setAttributes( {
										selected_terms: clearedTerms,
										selected_terms_main: [],
									} );
								}
							} }
							isBlock
							__nextHasNoMarginBottom
							__next40pxDefaultSize
						>
							<ToggleGroupControlOption value="all" label={ __( 'All Jobs', 'wp-job-openings' ) } />
							<ToggleGroupControlOption
								value="filtered"
								label={ __( 'Filtered List', 'wp-job-openings' ) }
							/>
					</ToggleGroupControl>
						<p>
							{ __(
								' Display all jobs or filtered by job specifications',
								'wp-job-openings'
							) }
						</p>

						{ list_type === 'filtered' && (
							<>
								<h2>{ __( 'Filters', 'wp-job-openings' ) }</h2>
								{ specifications.map( ( spec ) => (
									<div key={ spec.key } className="filter-item">
										<ToggleControl
											label={ spec.label }
											checked={ toggleState[ spec.key ] || false } // Check the toggle state for the spec
											onChange={ ( isChecked ) => {
												// Handle toggle change and update attributes
												handleToggleChange(
													spec.key,
													isChecked
												);
											} }
										/>

										{ /* Show FormTokenField only when toggle is on */ }
										{ toggleState[ spec.key ] && (
											<FormTokenField
												value={ (
													selectedTermsState[ spec.key ] || []
												).map( ( id ) => {
													const term = spec.terms.find(
														( t ) => t.term_id === id
													);
													return term ? term.name : '';
												} ) }
												onChange={ ( newTokens ) =>
													handleTermChange(
														newTokens,
														spec.key,
														spec
													)
												}
												suggestions={ spec.terms.map(
													( term ) => term.name
												) } // Suggestions are term names
												label=""
											/>
										) }
									</div>
								) ) }
							</>
						) }

						<SelectControl
							label={ __( 'Order By', 'wp-job-openings' ) }
							value={ order_by }
							options={ [
								{
									label: __( 'Newest to oldest', 'wp-job-openings' ),
									value: 'new_to_old',
								},
								{
									label: __( 'Oldest to newest', 'wp-job-openings' ),
									value: 'old_to_new',
								},
							] }
							onChange={ ( order_by ) => setAttributes( { order_by } ) }
						/>

						<ToggleControl
							label={ __( 'Hide Expired Jobs', 'wp-job-openings' ) }
							checked={ hide_expired_jobs }
							onChange={ ( hide_expired_jobs ) =>
								setAttributes( { hide_expired_jobs } )
							}
						/>

						{ wp.hooks.doAction(
							'after_awsm_block_job_listing',
							block_job_listing,
							props
						) }
						{ block_job_listing }

					</PanelBody>
                </Fragment>
			</InspectorControls>
			{/* End */}

			{/* Control Styles  */}
		    <InspectorControls group="styles">
				<Fragment>
					<div className="hz-inspector-controls">
						{search && (
							<>
								{placement === 'side' && (
									<PanelBody title={__('Sidebar', 'wp-job-openings')} initialOpen={true}>
										<RangeControl
											label={__('Sidebar Width', 'wp-job-openings')}
											__nextHasNoMarginBottom
											min={33.33}
											max={80.33}
											step={0.1}
											name="hz_sidebar_width"
											value={parseFloat(hz_sidebar_width) || 33.33}
											onChange={(val) => {
												setAttributes({ hz_sidebar_width: val });
											}}
											__next40pxDefaultSize
										/>

										<BorderControl
											label={__('Border', 'wp-job-openings')}
											withSlider
											isCompact={true}
											value={hz_sf_border}
											onChange={(newBorder) => {
												const width = newBorder?.width ?? hz_sf_border?.width;
												setAttributes({
													hz_sf_border: {
														...hz_sf_border, 
														...newBorder,    
														width: width,    
													},
												});
											}}
											enableStyle={false}
										/>

										<Spacer />

										<BorderRadiusControl
											values={hz_sf_border_radius}
											onChange={(newRadius) => {
												if (typeof newRadius === 'string') {
													const radiusObject = {
														topLeft: newRadius,
														topRight: newRadius,
														bottomRight: newRadius,
														bottomLeft: newRadius,
													};
													setAttributes({ hz_sf_border_radius: radiusObject });
												} else {
													setAttributes({ hz_sf_border_radius: newRadius });
												}
											}}
										/>

										<Spacer />

										<BoxControl
											label={__('Padding', 'wp-job-openings')}
											values={hz_sf_padding}
											onChange={(Padding) => {
												setAttributes({ hz_sf_padding: Padding });
											}}
										/>
									</PanelBody>
								)}

								<PanelBody title={__('Search and Filter Fields', 'wp-job-openings')} initialOpen={true}>
									<BorderControl
										label={__('Border', 'wp-job-openings')}
										withSlider
										isCompact={true}
										value={hz_ls_border}
										__experimentalIsRenderedInSidebar
										onChange={(newBorder) => {

											const width = newBorder?.width ?? hz_ls_border?.width;
											setAttributes({
												hz_ls_border: {
													...hz_ls_border, 
													...newBorder,   
													width: width,   
												},
											});

											if (width === '0px') {
												setTimeout(() => {
													setAttributes({
														hz_ls_border: {
															...hz_ls_border,
															...newBorder,
															width: '1px',
														},
													});
												}, 100);
											}
										}}
										enableStyle={false}
									/>
									<Spacer />

									<BorderRadiusControl
										values={hz_ls_border_radius}
										onChange={(newRadius) => {
											if (typeof newRadius === 'string') {
												const radiusObject = {
													topLeft: newRadius,
													topRight: newRadius,
													bottomRight: newRadius,
													bottomLeft: newRadius,
												};
												setAttributes({ hz_ls_border_radius: radiusObject });
											} else {
												setAttributes({ hz_ls_border_radius: newRadius });
											}
										}}
									/>
								</PanelBody>
							</>
						)}
						
						<PanelBody title={__('Job Listing', 'wp-job-openings')} initialOpen={true}>
							<BorderControl
								label={__('Border', 'wp-job-openings')}
								withSlider
								isCompact={true}
								value={hz_jl_border}
								__experimentalIsRenderedInSidebar
								onChange={(newBorder) => {
									const width = newBorder?.width ?? hz_jl_border?.width;

									setAttributes({
										hz_jl_border: {
											...hz_jl_border, 
											...newBorder,    
											width: width,    
										},
									});

									if (width === '0px') {
										setTimeout(() => {
											setAttributes({
												hz_jl_border: {
													...hz_jl_border,
													...newBorder,
													width: '1px',
												},
											});
										}, 100);
									}
								}}
								enableStyle={false}
							/>

							<Spacer></Spacer>

							<BorderRadiusControl
								values={hz_jl_border_radius}
								onChange={(newRadius) => {
									if (typeof newRadius === 'string') {
										const radiusObject = {
											topLeft: newRadius,
											topRight: newRadius,
											bottomRight: newRadius,
											bottomLeft: newRadius,
										}; 
											setAttributes({ hz_jl_border_radius: radiusObject });
									} else {
											setAttributes({ hz_jl_border_radius: newRadius });
									}
								}}
							/>

							<BoxControl
								label={__('Padding', 'wp-job-openings')}
								values={hz_jl_padding} // Ensure there is a fallback value
								onChange={(Padding) => {
									setAttributes({ hz_jl_padding: Padding });
								}}
							/>

							<PanelRow>
								<strong>{__('Button', 'wp-job-openings')}</strong>
							</PanelRow>
								<BorderControl
									label={__('Border', 'wp-job-openings')}
									withSlider
									isCompact={true} 
									value={hz_bs_border} // Use a valid default object
									__experimentalIsRenderedInSidebar
									onChange={(newBorder) => {
										var width = newBorder?.width;
										setAttributes({
											hz_bs_border: {
												...newBorder,
												width: width,
											},
										});
									}}
									enableStyle={false}
								/>

								<Spacer></Spacer>

								<BorderRadiusControl
									values={hz_bs_border_radius}
									onChange={(newRadius) => {
										if (typeof newRadius === 'string') {
											const radiusObject = {
												topLeft: newRadius,
												topRight: newRadius,
												bottomRight: newRadius,
												bottomLeft: newRadius,
											}; 
												setAttributes({ hz_bs_border_radius: radiusObject });
										} else {
												setAttributes({ hz_bs_border_radius: newRadius });
										}
									}}
								/>

								<BoxControl
									label={__('Padding', 'wp-job-openings')}
									values={hz_bs_padding} // Ensure there is a fallback value
									onChange={(Padding) => {
										setAttributes({ hz_bs_padding: Padding });
									}}
								/>
						
						</PanelBody>

						<PanelColorSettings
							title="Button Color Settings"
							initialOpen={true}
							colorSettings={[
								{
									value: hz_button_background_color,
									onChange: (color) => setAttributes({ hz_button_background_color: color }),
									label: 'Background Color',
								},
								{
									value: hz_button_text_color,
									onChange: (color) => setAttributes({ hz_button_text_color: color }),
									label: 'Text Color',
								},
							]}
						/> 

						{ wp.hooks.doAction(
							'after_awsm_block_styles_panel',
							block_styles_panel,
							props
						) }

						{ block_styles_panel }

					</div>
				</Fragment>
			</InspectorControls>
			{/* End */}
	  	</>
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
