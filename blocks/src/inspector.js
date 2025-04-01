import { __ } from '@wordpress/i18n';
import { useEffect, Fragment, useState } from '@wordpress/element';
import { InspectorControls,BlockEdit } from '@wordpress/block-editor';
import { addFilter } from '@wordpress/hooks';

import {
    PanelBody,
    TabPanel,
    Tooltip,
    Icon,
    RangeControl,
    ToggleControl,
	TextControl,
	SelectControl,
	Button,
	FormTokenField,
	__experimentalInputControl as InputControl,
	__experimentalInputControlSuffixWrapper as InputControllSuffixWrapper,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	__experimentalBoxControl as BoxControl,
	__experimentalBorderBoxControl as BorderBoxControl,
	__experimentalSpacer as Spacer,
	__experimentalPaletteEdit as PaletteEdit,
} from '@wordpress/components';

// Define Icons
const settingsIcon = 'admin-generic';
const styleIcon = 'admin-customizer';

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
			border_width,
			border_radius,
			button_styles,
			colors
		},
		setAttributes,
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

	// Sync selected terms with props on mount or when selectedTerm changes
	useEffect( () => {
		if (
			typeof awsmJobsAdmin !== 'undefined' &&
			awsmJobsAdmin.isProEnabled
		) {
			setIsProEnabled( true );
		}

		// Sync state with selectedTerms attribute
		const initialSelectedTerms = specifications.reduce( ( acc, spec ) => {
			acc[ spec.key ] = selectedTerms[ spec.key ] || []; // Initialize with existing selected terms or empty array
			return acc;
		}, {} );

		setSelectedTermsState( initialSelectedTerms );

		setToggleState( ( prevState ) => {
			const initialState = Array.isArray( selected_terms_main )
				? selected_terms_main.reduce( ( acc, key ) => {
						acc[ key ] = true;
						return acc;
				  }, {} )
				: {};
			return initialState;
		} );
		
	}, [ specifications, selectedTerms, selected_terms_main ] );

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

	/** this one need to be checked */
	const HookTrigger = () => {
		useEffect(() => {
			wp.hooks.doAction('after_awsm_block_job_listing', block_job_listing, props);
		}, [block_job_listing, props]);
	
		return null; // Prevents React errors
	};
	/** End */

	const [palette, setPalette] = useState(colors || [
        { name: 'Background', color: '#FF00000F' },
        { name: 'Text', color: '#FF9C00' },
        { name: 'Heading', color: '#2C2C2F' },
		{ name: 'Note', color: '#DFFF00' }
    ]);

	const updateColors = (newColors) => {
        setPalette(newColors);
        setAttributes({ colors: newColors });
    };

    return (
        <InspectorControls>
			{/** Tabs define */}
            <TabPanel
                className="awsm-job-tabs"
                activeClass="active-tab"
                initialTabName="settings"
                tabs={[
                    {
                        name: 'settings',
                        title: (
                            <Tooltip text={__('Settings', 'wp-job-openings')}>
                                <Icon icon={settingsIcon} />
                            </Tooltip>
                        ),
                        className: 'settings-tab',
                    },
                    {
                        name: 'styles',
                        title: (
                            <Tooltip text={__('Styles', 'wp-job-openings')}>
                                <Icon icon={styleIcon} />
                            </Tooltip>
                        ),
                        className: 'styles-tab',
                    },
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

								{ block_job_listing }

							</PanelBody>
                        </Fragment>
                    ) : (
                        <Fragment>
                            <PanelBody title={__('Search & Filters', 'wp-job-openings')} initialOpen={true}>
								<BorderBoxControl
									label={__('Border', 'wp-job-openings')}
									width='30'
									isCompact
									withSlider
									value={border_width || 0} // Ensure there is a fallback value
									__experimentalIsRenderedInSidebar
									onChange={(newBorder) => {
										setAttributes({ border_width: newBorder });
									}}
								/>
								<Spacer></Spacer>
								<BorderBoxControl
									label={__('Radius', 'wp-job-openings')}
									width='30'
									isCompact
									withSlider
									value={border_radius || 0} // Ensure there is a fallback value
									onChange={(newRadius) => {
										setAttributes({ border_radius: newRadius });
									}}
								/>
								<Spacer></Spacer>
								<BoxControl
									label="Padding"
									__next40pxDefaultSize
									onChange={() => {}}
								/>
								<Spacer></Spacer>
								<BoxControl
									label="Margin"
									__next40pxDefaultSize
									onChange={() => {}}
								/>
                            </PanelBody>

							<PanelBody title={__('Layout Settings', 'wp-job-openings')} initialOpen={true}>

								<InputControl
									suffix={<InputControllSuffixWrapper>%</InputControllSuffixWrapper>}
								/>

								<BorderBoxControl
									label={__('Border', 'wp-job-openings')}
									width='30'
									isCompact
									withSlider
									value={border_width || 0} // Ensure there is a fallback value
									__experimentalIsRenderedInSidebar
									onChange={(newBorder) => {
										setAttributes({ border_width: newBorder });
									}}
								/>
								<Spacer></Spacer>
								<BorderBoxControl
									label={__('Radius', 'wp-job-openings')}
									width='30'
									isCompact
									withSlider
									value={border_radius || 0} // Ensure there is a fallback value
									onChange={(newRadius) => {
										setAttributes({ border_radius: newRadius });
									}}
								/>
								<Spacer></Spacer>
								<BoxControl
									label="Padding"
									__next40pxDefaultSize
									onChange={() => {}}
								/>
								<Spacer></Spacer>
								<BoxControl
									label="Margin"
									__next40pxDefaultSize
									onChange={() => {}}
								/>
							</PanelBody>

							<PanelBody title={__('Job Listing', 'wp-job-openings')} initialOpen={true}>
								<BorderBoxControl
									label={__('Border', 'wp-job-openings')}
									width='30'
									isCompact
									withSlider
									value={border_width || 0} // Ensure there is a fallback value
									__experimentalIsRenderedInSidebar
									onChange={(newBorder) => {
										setAttributes({ border_width: newBorder });
									}}
								/>
								<Spacer></Spacer>
								<BorderBoxControl
									label={__('Radius', 'wp-job-openings')}
									width='30'
									isCompact
									withSlider
									value={border_radius || 0} // Ensure there is a fallback value
									onChange={(newRadius) => {
										setAttributes({ border_radius: newRadius });
									}}
								/>
								<Spacer></Spacer>
								<BoxControl
									label="Padding"
									__next40pxDefaultSize
									onChange={() => {}}
								/>
								<Spacer></Spacer>
								<BoxControl
									label="Margin"
									__next40pxDefaultSize
									onChange={() => {}}
								/>

								<ToggleGroupControl
									label="Button Style"
									value={ button_styles || "none"  }
									onChange={ ( button_styles ) => setAttributes( { button_styles } ) }
									isBlock
									__nextHasNoMarginBottom
									__next40pxDefaultSize
								>
									<ToggleGroupControlOption value="none" label="None" />
									<ToggleGroupControlOption value="filled" label="Filled" />
									<ToggleGroupControlOption value="outlined" label="Outlined" />
								</ToggleGroupControl>
							</PanelBody>

							<PanelBody title={__('Colors', 'wp-job-openings')} initialOpen={true}>
								<PaletteEdit
									colors={palette}
									onChange={updateColors}
									showAlpha={true} // Allows transparency editing
								/>
							</PanelBody>
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
