import { __ } from '@wordpress/i18n';
import { useEffect, Fragment, useState } from '@wordpress/element';
import { InspectorControls,BlockEdit, __experimentalPanelColorGradientSettings as PanelColorGradientSettings,useBlockProps,PanelColorSettings } from '@wordpress/block-editor';
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
	BorderBoxControl,
	__experimentalSpacer as Spacer
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
			orderBy,
			listType,
			jobsPerPage,
			layout,
			selectedTerms,
			selected_terms_main,
			number_of_columns,
			hz_sf_border = {},
			hz_sf_border_radius = {},
			hz_sf_padding = {},
			hz_ls_border = {},
			hz_ls_border_radius = {},
			hz_jl_border = {},
			hz_jl_border_radius = {},
			hz_jl_padding = {},
			hz_button_color,
			hz_sidebar_width,
			blockId
		},
		setAttributes,
		clientId,
	} = props;

	// Local state for block settings
	const specifications = awsmJobsAdmin.awsm_filters_block;
	const [ isProEnabled, setIsProEnabled ] = useState( false );
	const [ toggleState, setToggleState ] = useState(
		selected_terms_main || {}
	);
	
	const [ selectedTermsState, setSelectedTermsState ] = useState(
		selectedTerms || {}
	);

	const block_appearance_list = [];
	const block_job_listing = [];

	useEffect(() => {
		if (typeof awsmJobsAdmin !== 'undefined' && awsmJobsAdmin.isProEnabled) {
			setIsProEnabled(true);
		}
	
		// Sync state with selectedTerms attribute
		const initialSelectedTerms = specifications.reduce((acc, spec) => {
			acc[spec.key] = selectedTerms[spec.key] || [];
			return acc;
		}, {});
	
		setSelectedTermsState(initialSelectedTerms);
	
		setToggleState((prevState) => {
			const initialState = Array.isArray(selected_terms_main)
				? selected_terms_main.reduce((acc, key) => {
					  acc[key] = true;
					  return acc;
				  }, {})
				: {};
			return initialState;
		});
	
		if (clientId) {
			setAttributes({ blockId: `job-block-${clientId}` });
		}
	
		// **Ensure default filters are initialized**
		if (!filter_options.length) {
			const defaultFilters = specifications.map((spec) => ({
				specKey: spec.key,
				value: 'dropdown',
			}));
	
			setAttributes({ filter_options: defaultFilters });
	
			// **Force Gutenberg to recognize the change immediately**
			wp.data.dispatch('core/block-editor').updateBlockAttributes(clientId, { 
				filter_options: defaultFilters,
			});
			// Ensure the block editor re-renders
			setTimeout(() => {
				wp.data.dispatch('core/block-editor').selectBlock(clientId);
				wp.data.dispatch('core/block-editor').clearSelectedBlock();
			}, 200);
		}
	
	}, [specifications, selectedTerms, selected_terms_main]);
	
	//console.log(specifications);
	
	const handleTermChange = ( newTokens, specKey, spec ) => {
		setSelectedTermsState( ( prevSelectedTerms ) => {
			const updatedSelectedTerms = { ...prevSelectedTerms };

			const newTermIds = newTokens
				.map( ( token ) => {
					const term = spec.terms.find( ( t ) => t.name === token );
					return term ? term.term_id : null;
				} )
				.filter( ( id ) => id !== null ); // Filter out invalid IDs

			updatedSelectedTerms[ specKey ] = newTermIds;
			setAttributes( { selectedTerms: updatedSelectedTerms } );

			return updatedSelectedTerms;
		} );
	};

	const handleToggleChange = ( specKey, isChecked ) => {
		let updatedTermsMain = [ ...( selected_terms_main || [] ) ];

		if ( isChecked ) {
			// Add the specKey if it's not already in the array
			if ( ! updatedTermsMain.includes( specKey ) ) {
				updatedTermsMain.push( specKey );
			}
		} else {
			// Remove the specKey if it exists
			updatedTermsMain = updatedTermsMain.filter(
				( key ) => key !== specKey
			);

			// Clear the selectedTerms for the specKey when toggled off
			setSelectedTermsState( ( prevSelectedTerms ) => {
				const updatedSelectedTerms = { ...prevSelectedTerms };
				delete updatedSelectedTerms[ specKey ];

				// Ensure attributes are updated and re-rendered
				setAttributes( {
					selectedTerms: updatedSelectedTerms,
					selected_terms_main: updatedTermsMain, // Keep this consistent
				} );

				return updatedSelectedTerms;
			} );
		}

		// Update the toggle state for the editor reactivity
		setToggleState( ( prevState ) => ( {
			...prevState,
			[ specKey ]: isChecked,
		} ) );

		// Sync the selected_terms_main attribute with the editor
		setAttributes( {
			selected_terms_main: updatedTermsMain,
		} );
	};

	const onchange_number_of_columns = ( value ) => {
		const columnsValue = parseInt( value, 10 );
		setAttributes( {
			number_of_columns: isNaN( columnsValue ) ? 0 : columnsValue,
		} );
	};

	const settingsIcon = () => (
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path fill-rule="evenodd" d="M10.289 4.836A1 1 0 0111.275 4h1.306a1 1 0 01.987.836l.244 1.466c.787.26 1.503.679 2.108 1.218l1.393-.522a1 1 0 011.216.437l.653 1.13a1 1 0 01-.23 1.273l-1.148.944a6.025 6.025 0 010 2.435l1.149.946a1 1 0 01.23 1.272l-.653 1.13a1 1 0 01-1.216.437l-1.394-.522c-.605.54-1.32.958-2.108 1.218l-.244 1.466a1 1 0 01-.987.836h-1.306a1 1 0 01-.986-.836l-.244-1.466a5.995 5.995 0 01-2.108-1.218l-1.394.522a1 1 0 01-1.217-.436l-.653-1.131a1 1 0 01.23-1.272l1.149-.946a6.026 6.026 0 010-2.435l-1.148-.944a1 1 0 01-.23-1.272l.653-1.131a1 1 0 011.217-.437l1.393.522a5.994 5.994 0 012.108-1.218l.244-1.466zM14.929 12a3 3 0 11-6 0 3 3 0 016 0z" clip-rule="evenodd"></path></svg>
	);
	// Custom SVG for contrast icon
	const stylesIcon = () => (
		<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" aria-hidden="true" focusable="false"><path fill-rule="evenodd" clip-rule="evenodd" d="M20 12a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-1.5 0a6.5 6.5 0 0 1-6.5 6.5v-13a6.5 6.5 0 0 1 6.5 6.5Z"></path></svg>
	);

    return (
        <InspectorControls>
            <TabPanel
                /* className="block-editor-tabs" */
                activeClass="is-active"
                tabs={[
                    {
                        name: 'settings',
                        title: (
                            <>
                                {settingsIcon()} 
                            </>
                        ),
                        className: 'tab-settings',
                    },
                    {
                        name: 'style',
                        title: (
                            <>
                                {stylesIcon()} 
                            </>
                        ),
                        className: 'tab-style',
                    }
                ]}
            >
                {(tab) =>
                    tab.name === 'settings' ? (
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
											onChange={ ( placement ) =>
												setAttributes( { placement } )
											}
											isBlock
											__nextHasNoMarginBottom
											__next40pxDefaultSize
										>
											<ToggleGroupControlOption value="top" label="Top" />
											<ToggleGroupControlOption
												value="slide"
												label="Side"
											/>
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

											// If multiple terms are selected for this specKey, update the filter option to "checkbox"
											if (
												hasMultipleSelectedTerms &&
												filterOption?.value !== 'checkbox'
											) {
												const updatedFilters = filter_options.map(
													( option ) =>
														option.specKey === spec.key
															? { ...option, value: 'checkbox' }
															: option
												);
												setAttributes( {
													filter_options: updatedFilters,
												} );
											}

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
																variant="secondary"
																style={ {
																	backgroundColor:
																		filterOption.value ===
																		'dropdown'
																			? 'black'
																			: 'initial',
																	color:
																		filterOption.value ===
																		'dropdown'
																			? 'white'
																			: 'black',
																	marginRight: '10px',
																} }
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
																	'Dropdown',
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
																	'Checkbox',
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
										setAttributes( { jobsPerPage: sliderValue } )
									}
									value={ jobsPerPage }
									min={ 1 }
									max={ 10 }
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
							</PanelBody>

							<PanelBody title={ __( 'Job Listing', 'wp-job-openings' ) }>
							<ToggleGroupControl
									label="List Type"
									value={ listType }
									onChange={ ( newListType ) => {
										setAttributes( { listType: newListType } );

										// Clear all items in selectedTerms if listType is set to "all"
										if ( newListType === 'all' ) {
											const clearedTerms = {};
											specifications.forEach( ( spec ) => {
												clearedTerms[ spec.key ] = [];
											} );
											setAttributes( {
												selectedTerms: clearedTerms,
												selected_terms_main: [],
											} );
										}
									} }
									isBlock
									__nextHasNoMarginBottom
									__next40pxDefaultSize
								>
									<ToggleGroupControlOption value="all" label="All Jobs" />
									<ToggleGroupControlOption
										value="filtered"
										label="Filtered List"
									/>
								</ToggleGroupControl>
								<p>
									{ __(
										' Display all jobs or filtered by job specifications',
										'wp-job-openings'
									) }
								</p>

								{ listType === 'filtered' && (
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
									value={ orderBy }
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
									onChange={ ( orderBy ) => setAttributes( { orderBy } ) }
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
                    ) : (
                        <Fragment>
							<div className="hz-inspector-controls">
							{placement === 'slide' && (
                            <PanelBody title={__('Search & Filters', 'wp-job-openings')} initialOpen={true}>
								<BorderBoxControl
									label={__('Border', 'wp-job-openings')}
									withSlider
									value={hz_sf_border} 
									onChange={(newBorder) => {
										setAttributes({ hz_sf_border: newBorder });
									}}
								/>
								<Spacer></Spacer>
								<div className="custom-box-control">
								<BorderBoxControl
									label={__('Radius', 'wp-job-openings')}
									withSlider
									value={hz_sf_border_radius || 0} // Ensure there is a fallback value
									onChange={(newRadius) => {
										setAttributes({ hz_sf_border_radius: newRadius });
									}}
								/>
								</div>
								<Spacer></Spacer>
								<BoxControl
									label={__('Padding', 'wp-job-openings')}
									values={hz_sf_padding} // Ensure there is a fallback value
									onChange={(Padding) => {
										setAttributes({ hz_sf_padding: Padding });
									}}
								/>
							
                            </PanelBody>
							)}

							<PanelBody title={__('Layout Settings', 'wp-job-openings')} initialOpen={true}>
							{placement === 'slide' && (
								<InputControl
								    label="Sidebar Width"
									name="hz_sidebar_width"
									value={hz_sidebar_width?.replace('%', '') || ''}
									onChange={(val) => setAttributes({ hz_sidebar_width: val })}
									suffix="%"
									style={{ width: '120px' }}
								/>
							)}
								<Spacer></Spacer>
								<BorderBoxControl
									label={__('Border', 'wp-job-openings')}
									withSlider
									value={hz_ls_border || '1px'} // Ensure there is a fallback value
									__experimentalIsRenderedInSidebar
									onChange={(newBorder) => {
										setAttributes({ hz_ls_border: newBorder });
									}}
								/>
									<Spacer></Spacer>
								<div className="custom-box-control">
								<BorderBoxControl
									label={__('Radius', 'wp-job-openings')}
									withSlider
									value={hz_ls_border_radius || 0} // Ensure there is a fallback value
									onChange={(newRadius) => {
										setAttributes({ hz_ls_border_radius: newRadius });
									}}
								/>
								</div>
							</PanelBody>

							<PanelBody title={__('Job Listing', 'wp-job-openings')} initialOpen={true}>
								<BorderBoxControl
									label={__('Border', 'wp-job-openings')}
									withSlider
									value={hz_jl_border || 0} // Ensure there is a fallback value
									__experimentalIsRenderedInSidebar  
									onChange={(newBorder) => {
										setAttributes({ hz_jl_border: newBorder });
									}}
								/>
									<Spacer></Spacer>
								<div className="custom-box-control">
								<BorderBoxControl
									label={__('Radius', 'wp-job-openings')}
									withSlider
									value={hz_jl_border_radius || 0} // Ensure there is a fallback value
									onChange={(newRadius) => {
										setAttributes({ hz_jl_border_radius: newRadius });
									}}
								/>
								</div>
								<Spacer></Spacer>
								<BoxControl
									label={__('Padding', 'wp-job-openings')}
									values={hz_jl_padding} // Ensure there is a fallback value
									onChange={(Padding) => {
										setAttributes({ hz_jl_padding: Padding });
									}}
								/>

							</PanelBody>
							
							<PanelColorGradientSettings
								title={__('Color', 'wp-job-openings')}
								settings={[
									{
										label: __('Button', 'wp-job-openings'),
										colorValue: hz_button_color,
										onColorChange: (color) => setAttributes({ hz_button_color: color }),
									}
								]}
							/>
							 </div>
                        </Fragment>
                    )
                }
            </TabPanel>
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
