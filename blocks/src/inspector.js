import {__} from "@wordpress/i18n";
import {useEffect, useRef, Fragment, useState} from "@wordpress/element";
import {
	InspectorControls,
	BlockEdit,
	useBlockProps,
	PanelColorSettings,
	__experimentalBorderRadiusControl as BorderRadiusControl
} from "@wordpress/block-editor";
import {addFilter} from "@wordpress/hooks";
import {useSelect} from "@wordpress/data";

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
} from "@wordpress/components";

const WidgetInspectorControls = props => {
	const {
		clientId,
		attributes: {
			blockId,
			search,
			placement,
			filter_options,
			pagination,
			enable_job_filter,
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
			hz_sidebar_width
		},
		setAttributes
	} = props;

	// Local state for block settings
	const filtersInitRef = useRef( false );
	const specifications = window.awsmJobsAdmin?.awsm_filters_block || [];
	const [ toggleState, setToggleState ] = useState( selected_terms_main || {} );

	const [ selectedTermsState, setSelectedTermsState ] = useState(
		selected_terms || {}
	);

	const block_appearance_list = [];
	const block_job_listing = [];
	const block_styles_panel = [];

	const {wasJustInserted, hasOriginalContent} = useSelect(
		select => {
			const editor = select( "core/block-editor" );
			const block = editor?.getBlock ? editor.getBlock( clientId ) : null;

			return {
				wasJustInserted: editor?.wasBlockJustInserted
					? editor.wasBlockJustInserted( clientId )
					: false,
				hasOriginalContent: !! block?.originalContent
			};
		},
		[ clientId ]
	);

	useEffect( () => {
		if ( ! specifications?.length ) {
			return;
		}

		const initialSelectedTerms = specifications.reduce( ( acc, spec ) => {
			acc[ spec.key ] = selected_terms?.[ spec.key ] || [];
			return acc;
		}, {} );

		setSelectedTermsState( initialSelectedTerms );
	}, [ specifications, selected_terms ] );

	useEffect( () => {
		const initialToggle = Array.isArray( selected_terms_main )
			? selected_terms_main.reduce( ( acc, key ) => {
					acc[ key ] = true;
					return acc;
			  }, {} )
			: {};

		setToggleState( initialToggle );
	}, [ selected_terms_main ] );

	useEffect( () => {
		const expectedId = "block-" + clientId;
		if ( ! blockId || blockId !== expectedId ) {
			// Persist a stable, unique id so CSS variables can be scoped per-block on the frontend.
			// Also regenerate on duplication (blockId won't match the new clientId).
			setAttributes( {blockId: expectedId} );
		}
	}, [] );

	useEffect( () => {
		// Only auto-populate default filters for newly inserted blocks.
		// This preserves legacy blocks where filters were enabled but no specs were chosen.
		if ( ! wasJustInserted ) {
			return;
		}

		if ( ! filtersInitRef.current && specifications?.length > 0 ) {
			const normalizedFilters = filter_options?.length
				? filter_options.map( option =>
						typeof option === "object" && option.specKey
							? option
							: {specKey: option, value: "dropdown"}
				  )
				: specifications.map( spec => ( {
						specKey: spec.key,
						value: "dropdown"
				  } ) );

			setAttributes( {filter_options: normalizedFilters} );
			filtersInitRef.current = true;
		}
	}, [ wasJustInserted, specifications, filter_options ] );

	const handleTermChange = ( newTokens, specKey, spec ) => {
		const newTermIds = newTokens
			.map( token => {
				// Normalize to string so purely-numeric term names work in FormTokenField.
				const term = spec.terms.find(
					t => String( t.name ) === String( token )
				);
				return term ? term.term_id : null;
			} )
			.filter( id => id !== null );

		const updatedSelectedTerms = {
			...selectedTermsState,
			[ specKey ]: newTermIds
		};

		// Update filter type if multiple terms selected
		const updatedFilters = filter_options.map( option =>
			option.specKey === specKey
				? {...option, value: newTermIds.length > 1 ? "checkbox" : "dropdown"}
				: option
		);

		setSelectedTermsState( updatedSelectedTerms );
		setAttributes( {
			selected_terms: updatedSelectedTerms,
			filter_options: updatedFilters
		} );
	};

	const handleToggleChange = ( specKey, isChecked ) => {
		const updatedTermsMain = isChecked
			? [ ...new Set( [ ...( selected_terms_main || [] ), specKey ] ) ]
			: ( selected_terms_main || [] ).filter( key => key !== specKey );

		const updatedSelectedTerms = {...selectedTermsState};
		if ( ! isChecked ) {
			delete updatedSelectedTerms[ specKey ];
		}

		setToggleState( prev => ( {...prev, [ specKey ]: isChecked} ) );
		setSelectedTermsState( updatedSelectedTerms );
		setAttributes( {
			selected_terms_main: updatedTermsMain,
			selected_terms: updatedSelectedTerms
		} );
	};

	const onChangeNumberOfColumns = value => {
		const columnsValue = parseInt( value, 10 );
		setAttributes( {
			number_of_columns: isNaN( columnsValue ) ? 0 : columnsValue
		} );
	};

	const otherOptionsHandler = ( toggleValue, specKey ) => {
		let updated = [ ...other_options ];
		if ( toggleValue ) {
			if ( ! updated.includes( specKey ) ) {
				updated.push( specKey );
			}
		} else {
			updated = updated.filter( key => key !== specKey );
		}
		setAttributes( {other_options: updated} );
	};

	return (
		<>
			{ /* Control settings  */ }
			<InspectorControls group="settings">
				<Fragment>
					<PanelBody
						title={ __( "Search & Filters", "wp-job-openings" ) }
						initialOpen={ true }
					>
						{ ( search || enable_job_filter ) && (
							<ToggleGroupControl
								label={ __( "Placement", "wp-job-openings" ) }
								value={ placement }
								onChange={ newPlacement =>
									setAttributes( {placement: newPlacement} )
								}
								isBlock
								__nextHasNoMarginBottom
								__next40pxDefaultSize
							>
								<ToggleGroupControlOption
									value="top"
									label={ __( "Top", "wp-job-openings" ) }
								/>
								<ToggleGroupControlOption
									value="side"
									label={ __( "Side", "wp-job-openings" ) }
								/>
							</ToggleGroupControl>
						) }

						<ToggleControl
							label={ __( "Enable Search", "wp-job-openings" ) }
							checked={ search }
							onChange={ newSearch => {
								const updates = {search: newSearch};
								if ( ! newSearch && ! enable_job_filter ) {
									updates.placement = "top";
								}
								setAttributes( updates );
							} }
						/>

						{ search && (
							<>
								<TextControl
									label={ __( "Search Placeholder", "wp-job-openings" ) }
									value={ search_placeholder }
									onChange={ search_placeholder =>
										setAttributes( {search_placeholder} )
									}
									placeholder={ __( "Search Jobs", "wp-job-openings" ) }
								/>
							</>
						) }

						<ToggleControl
							label={ __( "Enable Filters", "wp-job-openings" ) }
							checked={ enable_job_filter }
							onChange={ newFilter => {
								const updates = {enable_job_filter: newFilter};
								if ( ! newFilter && ! search ) {
									updates.placement = "top";
								}
								setAttributes( updates );
							} }
						/>
						{ enable_job_filter && (
							<>
								<h2>{ __( "Available Filters", "wp-job-openings" ) }</h2>
								{ specifications.map( spec => {
									const filterOption = filter_options.find(
										option => option.specKey === spec.key
									);

									// Check if there are multiple selected terms for the specKey
									const hasMultipleSelectedTerms =
										( selectedTermsState[ spec.key ] || [] ).length > 1;

									return (
										<div key={ spec.key }>
											{ /* Toggle Control */ }
											<ToggleControl
												label={ spec.label }
												checked={ filterOption !== undefined }
												onChange={ toggleValue => {
													const updatedFilters = toggleValue
														? [
																...filter_options,
																{
																	specKey: spec.key,
																	value: hasMultipleSelectedTerms
																		? "checkbox"
																		: "dropdown"
																}
														  ] // Choose checkbox if multiple terms are selected
														: filter_options.filter(
																option => option.specKey !== spec.key
														  ); // Remove the filter

													const updates = {filter_options: updatedFilters};

													// If all filters are now off, disable the Enable Filters toggle too
													if ( updatedFilters.length === 0 ) {
														updates.enable_job_filter = false;
													}

													setAttributes( updates );
												} }
											/>

											{ filterOption && (
												<div className="filters-button">
													{ /* Single Select */ }
													<Button
														className={ `filter-btn ${
															filterOption.value === "dropdown"
																? "is-active"
																: ""
														}` }
														__next40pxDefaultSize
														onClick={ () => {
															const updatedFilters = filter_options.map(
																option =>
																	option.specKey === spec.key
																		? {...option, value: "dropdown"}
																		: option
															);

															setAttributes( {
																filter_options: updatedFilters
															} );
														} }
													>
														{ __( "Single Select", "wp-job-openings" ) }
													</Button>

													{ /* Multi Select */ }
													<Button
														className={ `filter-btn ${
															filterOption.value === "checkbox"
																? "is-active"
																: ""
														}` }
														__next40pxDefaultSize
														onClick={ () => {
															const updatedFilters = filter_options.map(
																option =>
																	option.specKey === spec.key
																		? {...option, value: "checkbox"}
																		: option
															);

															setAttributes( {
																filter_options: updatedFilters
															} );
														} }
													>
														{ __( "Multi Select", "wp-job-openings" ) }
													</Button>
												</div>
											) }
										</div>
									);
								} ) }
							</>
						) }

						{ wp.hooks.doAction(
							"after_awsm_job_appearance",
							block_appearance_list,
							props
						) }
						{ block_appearance_list }
					</PanelBody>

					<PanelBody
						title={ __( "Layout Settings", "wp-job-openings" ) }
						initialOpen={ true }
					>
						<ToggleGroupControl
							label={ __( "Layout", "wp-job-openings" ) }
							value={ layout }
							onChange={ layout => setAttributes( {layout} ) }
							isBlock
							__nextHasNoMarginBottom
							__next40pxDefaultSize
						>
							<ToggleGroupControlOption
								value="list"
								label={ __( "List", "wp-job-openings" ) }
							/>
							<ToggleGroupControlOption
								value="grid"
								label={ __( "Grid", "wp-job-openings" ) }
							/>
							<ToggleGroupControlOption
								value="stack"
								label={ __( "Stack", "wp-job-openings" ) }
							/>
						</ToggleGroupControl>

						{ typeof layout !== "undefined" && layout == "grid" && (
							<SelectControl
								label={ __( "Columns", "wp-job-openings" ) }
								value={ number_of_columns }
								options={ [
									{
										label: __( "1 Column", "wp-job-openings" ),
										value: "1"
									},
									{
										label: __( "2 Columns", "wp-job-openings" ),
										value: "2"
									},
									{
										label: __( "3 Columns", "wp-job-openings" ),
										value: "3"
									},
									{
										label: __( "4 Columns", "wp-job-openings" ),
										value: "4"
									}
								] }
								onChange={ number_of_columns =>
									onChangeNumberOfColumns( number_of_columns )
								}
							/>
						) }

						<RangeControl
							label={ __( "Jobs Per Page", "wp-job-openings" ) }
							onChange={ sliderValue =>
								setAttributes( {listing_per_page: sliderValue} )
							}
							value={ listing_per_page }
							min={ 1 }
							max={ 100 }
							step={ 1 }
							withInputField={ true }
						/>

						<SelectControl
							label={ __( "Pagination", "wp-job-openings" ) }
							value={ pagination }
							options={ [
								{
									label: __( "Classic", "wp-job-openings" ),
									value: "classic"
								},
								{
									label: __( "Modern", "wp-job-openings" ),
									value: "modern"
								}
							] }
							onChange={ pagination => setAttributes( {pagination} ) }
						/>

						<h2>{ __( "Job Specs in the Listing", "wp-job-openings" ) }</h2>

						{ specifications.map( spec => (
							<ToggleControl
								key={ spec.key }
								label={ spec.label }
								checked={
									Array.isArray( other_options ) &&
									other_options.includes( spec.key )
								}
								onChange={ toggleValue =>
									otherOptionsHandler( toggleValue, spec.key )
								}
							/>
						) ) }

						<h2>
							{ __( "Show Spec Icon in the Listing", "wp-job-openings" ) }
						</h2>
						<ToggleControl
							label={ __( "Show Spec Icon", "wp-job-openings" ) }
							checked={ show_spec_icon }
							onChange={ show_spec_icon => setAttributes( {show_spec_icon} ) }
						/>
					</PanelBody>

					<PanelBody title={ __( "Job Listing", "wp-job-openings" ) }>
						<ToggleGroupControl
							label={ __( "List Type", "wp-job-openings" ) }
							value={ list_type }
							onChange={ newlist_type => {
								setAttributes( {list_type: newlist_type} );

								// Clear all items in selected_terms if list_type is set to "all"
								if ( newlist_type === "all" ) {
									const clearedTerms = {};
									specifications.forEach( spec => {
										clearedTerms[ spec.key ] = [];
									} );
									setAttributes( {
										selected_terms: clearedTerms,
										selected_terms_main: []
									} );
								}
							} }
							isBlock
							__nextHasNoMarginBottom
							__next40pxDefaultSize
						>
							<ToggleGroupControlOption
								value="all"
								label={ __( "All Jobs", "wp-job-openings" ) }
							/>
							<ToggleGroupControlOption
								value="filtered"
								label={ __( "Filtered List", "wp-job-openings" ) }
							/>
						</ToggleGroupControl>
						<p>
							{ __(
								" Display all jobs or filtered by job specifications",
								"wp-job-openings"
							) }
						</p>

						{ list_type === "filtered" && (
							<>
								<h2>{ __( "Filters", "wp-job-openings" ) }</h2>
								{ specifications.map( spec => (
									<div key={ spec.key } className="filter-item">
										<ToggleControl
											label={ spec.label }
											checked={ toggleState[ spec.key ] || false } // Check the toggle state for the spec
											onChange={ isChecked => {
												// Handle toggle change and update attributes
												handleToggleChange( spec.key, isChecked );
											} }
										/>

										{ /* Show FormTokenField only when toggle is on */ }
										{ toggleState[ spec.key ] &&
											( () => {
												const expiredIds = hide_expired_jobs
													? spec.expired_term_ids || []
													: [];
												const visibleTerms = spec.terms.filter(
													t => ! expiredIds.includes( t.term_id )
												);
												return (
													<FormTokenField
														value={ (
															selectedTermsState[ spec.key ] || []
														).map( id => {
															const term = visibleTerms.find(
																t => t.term_id === id
															);
															return term ? String( term.name ) : "";
														} ) }
														onChange={ newTokens =>
															handleTermChange( newTokens, spec.key, {
																...spec,
																terms: visibleTerms
															} )
														}
														suggestions={ visibleTerms.map( term =>
															String( term.name )
														) }
														__experimentalExpandOnFocus={ true }
														label=""
													/>
												);
											} )() }
									</div>
								) ) }
							</>
						) }

						<SelectControl
							label={ __( "Order By", "wp-job-openings" ) }
							value={ order_by }
							options={ [
								{
									label: __( "Newest to Oldest", "wp-job-openings" ),
									value: "new_to_old"
								},
								{
									label: __( "Oldest to Newest", "wp-job-openings" ),
									value: "old_to_new"
								}
							] }
							onChange={ order_by => setAttributes( {order_by} ) }
						/>

						<ToggleControl
							label={ __( "Hide Expired Jobs", "wp-job-openings" ) }
							checked={ hide_expired_jobs }
							onChange={ hide_expired_jobs =>
								setAttributes( {hide_expired_jobs} )
							}
						/>

						{ wp.hooks.doAction(
							"after_awsm_block_job_listing",
							block_job_listing,
							props
						) }
						{ block_job_listing }
					</PanelBody>
				</Fragment>
			</InspectorControls>
			{ /* End */ }

			{ /* Control Styles  */ }
			<InspectorControls group="styles">
				<Fragment>
					<div className="hz-inspector-controls">
						{ placement === "side" && (
							<PanelBody
								title={ __( "Sidebar", "wp-job-openings" ) }
								initialOpen={ true }
							>
								<RangeControl
									label={ __( "Sidebar Width", "wp-job-openings" ) }
									__nextHasNoMarginBottom
									min={ 33.33 }
									max={ 80.33 }
									step={ 0.1 }
									name="hz_sidebar_width"
									value={ parseFloat( hz_sidebar_width ) || 33.33 }
									onChange={ val => {
										setAttributes( {hz_sidebar_width: val} );
									} }
									__next40pxDefaultSize
								/>

								<BorderControl
									label={ __( "Border", "wp-job-openings" ) }
									withSlider
									isCompact={ true }
									value={ hz_sf_border }
									__experimentalIsRenderedInSidebar
									onChange={ newBorder => {
										const width = newBorder?.width ?? hz_sf_border?.width;
										setAttributes( {
											hz_sf_border: {
												...hz_sf_border,
												...newBorder,
												width
											}
										} );
									} }
									enableStyle={ false }
								/>

								<Spacer marginBottom={ 4 } />

								<BorderRadiusControl
									values={ hz_sf_border_radius }
									onChange={ newRadius => {
										if ( typeof newRadius === "string" ) {
											const radiusObject = {
												topLeft: newRadius,
												topRight: newRadius,
												bottomRight: newRadius,
												bottomLeft: newRadius
											};
											setAttributes( {hz_sf_border_radius: radiusObject} );
										} else {
											setAttributes( {hz_sf_border_radius: newRadius} );
										}
									} }
								/>

								<Spacer marginBottom={ 4 } />

								<BoxControl
									label={ __( "Padding", "wp-job-openings" ) }
									values={ hz_sf_padding }
									onChange={ Padding => {
										setAttributes( {hz_sf_padding: Padding} );
									} }
								/>
							</PanelBody>
						) }

						{ ( search || enable_job_filter ) && (
							<PanelBody
								title={ __( "Search and Filter Fields", "wp-job-openings" ) }
								initialOpen={ true }
							>
								<BorderControl
									label={ __( "Border", "wp-job-openings" ) }
									withSlider
									isCompact={ true }
									value={ hz_ls_border }
									__experimentalIsRenderedInSidebar
									onChange={ newBorder => {
										const rawWidth = newBorder?.width ?? hz_ls_border?.width;
										const width = rawWidth === "0px" ? "1px" : rawWidth;
										setAttributes( {
											hz_ls_border: {
												...hz_ls_border,
												...newBorder,
												width
											}
										} );
									} }
									enableStyle={ false }
								/>
								<Spacer marginBottom={ 4 } />

								<BorderRadiusControl
									values={ hz_ls_border_radius }
									onChange={ newRadius => {
										if ( typeof newRadius === "string" ) {
											const radiusObject = {
												topLeft: newRadius,
												topRight: newRadius,
												bottomRight: newRadius,
												bottomLeft: newRadius
											};
											setAttributes( {hz_ls_border_radius: radiusObject} );
										} else {
											setAttributes( {hz_ls_border_radius: newRadius} );
										}
									} }
								/>
							</PanelBody>
						) }

						<PanelBody
							title={ __( "Job Listing", "wp-job-openings" ) }
							initialOpen={ true }
						>
							<BorderControl
								label={ __( "Border", "wp-job-openings" ) }
								withSlider
								isCompact={ true }
								value={ hz_jl_border }
								__experimentalIsRenderedInSidebar
								onChange={ newBorder => {
									const rawWidth = newBorder?.width ?? hz_jl_border?.width;
									const width = rawWidth === "0px" ? "1px" : rawWidth;
									setAttributes( {
										hz_jl_border: {
											...hz_jl_border,
											...newBorder,
											width
										}
									} );
								} }
								enableStyle={ false }
							/>

							<Spacer marginBottom={ 4 }></Spacer>

							<BorderRadiusControl
								values={ hz_jl_border_radius }
								onChange={ newRadius => {
									if ( typeof newRadius === "string" ) {
										const radiusObject = {
											topLeft: newRadius,
											topRight: newRadius,
											bottomRight: newRadius,
											bottomLeft: newRadius
										};
										setAttributes( {hz_jl_border_radius: radiusObject} );
									} else {
										setAttributes( {hz_jl_border_radius: newRadius} );
									}
								} }
							/>

							<BoxControl
								label={ __( "Padding", "wp-job-openings" ) }
								values={ hz_jl_padding } // Ensure there is a fallback value
								onChange={ Padding => {
									setAttributes( {hz_jl_padding: Padding} );
								} }
							/>

							<PanelRow>
								<strong>{ __( "Button", "wp-job-openings" ) }</strong>
							</PanelRow>
							<BorderControl
								label={ __( "Border", "wp-job-openings" ) }
								withSlider
								isCompact={ true }
								value={ hz_bs_border } // Use a valid default object
								__experimentalIsRenderedInSidebar
								onChange={ newBorder => {
									const width = newBorder?.width ?? hz_bs_border?.width;
									setAttributes( {
										hz_bs_border: {
											...hz_bs_border,
											...newBorder,
											width
										}
									} );
								} }
								enableStyle={ false }
							/>

							<Spacer marginBottom={ 4 }></Spacer>

							<BorderRadiusControl
								values={ hz_bs_border_radius }
								onChange={ newRadius => {
									if ( typeof newRadius === "string" ) {
										const radiusObject = {
											topLeft: newRadius,
											topRight: newRadius,
											bottomRight: newRadius,
											bottomLeft: newRadius
										};
										setAttributes( {hz_bs_border_radius: radiusObject} );
									} else {
										setAttributes( {hz_bs_border_radius: newRadius} );
									}
								} }
							/>

							<BoxControl
								label={ __( "Padding", "wp-job-openings" ) }
								values={ hz_bs_padding } // Ensure there is a fallback value
								onChange={ Padding => {
									setAttributes( {hz_bs_padding: Padding} );
								} }
							/>
						</PanelBody>

						<PanelColorSettings
							title={ __( "Button Color Settings", "wp-job-openings" ) }
							initialOpen={ true }
							colorSettings={ [
								{
									value: hz_button_background_color,
									onChange: color =>
										setAttributes( {hz_button_background_color: color} ),
									label: __( "Background Color", "wp-job-openings" )
								},
								{
									value: hz_button_text_color,
									onChange: color =>
										setAttributes( {hz_button_text_color: color} ),
									label: __( "Text Color", "wp-job-openings" )
								}
							] }
						/>

						{ wp.hooks.doAction(
							"after_awsm_block_styles_panel",
							block_styles_panel,
							props
						) }

						{ block_styles_panel }
					</div>
				</Fragment>
			</InspectorControls>
			{ /* End */ }
		</>
	);
};

// Define the HOC to add custom inspector controls
const withCustomInspectorControls = BlockEdit => props => {
	if ( props.name !== "wp-job-openings/blocks" ) {
		return <BlockEdit { ...props } />;
	}

	return (
		<Fragment>
			<BlockEdit { ...props } />
			<WidgetInspectorControls { ...props } />
		</Fragment>
	);
};

// Add the filter to extend the block's inspector controls
addFilter(
	"editor.BlockEdit",
	"awsm-job-block-settings/awsm-block-inspector-controls",
	withCustomInspectorControls
);

export default withCustomInspectorControls;
