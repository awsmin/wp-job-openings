import {__} from "@wordpress/i18n";
import {useEffect, useRef, Fragment} from "@wordpress/element";
import {
	InspectorControls,
	PanelColorSettings,
	__experimentalBorderRadiusControl as BorderRadiusControl
} from "@wordpress/block-editor";
import {addFilter} from "@wordpress/hooks";
import {useSelect} from "@wordpress/data";

import {
	PanelBody,
	RangeControl,
	ToggleControl,
	TextControl,
	SelectControl,
	Button,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	BoxControl,
	__experimentalSpacer as Spacer,
	BorderControl,
	PanelRow
} from "@wordpress/components";

const enforceMinBorderWidth = ( newBorder, prevBorder ) => {
	const rawWidth = newBorder?.width ?? prevBorder?.width;
	if ( ! rawWidth || parseFloat( rawWidth ) !== 0 ) {
		const color = newBorder && 'color' in newBorder ? newBorder.color : prevBorder?.color;
		return { ...prevBorder, ...newBorder, color };
	}
	// Width hit 0 — enforce minimum and always preserve previous color
	const unit = rawWidth.replace( /[\d.]/g, '' ) || 'px';
	return {
		...prevBorder,
		...newBorder,
		width: '1' + unit,
		color: prevBorder?.color,
	};
};

const WidgetInspectorControls = props => {
	const {
		clientId,
		attributes: {
			blockId,
			search,
			placement,
			filter_options,
			filter_types = {},
			pagination,
			enable_job_filter,
			filter_items_order = 'custom',
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
			hz_button_style = 'none',
			hz_button_text = 'More Details →',
			hz_button_background_color,
			hz_button_text_color,
			hz_pagination_background_color,
			hz_pagination_text_color,
			hz_pagination_border = {},
			hz_pagination_border_radius,
			hz_pagination_padding,
			hz_sf_background_color,
			hz_sf_text_color,
			hz_jl_background_color,
			hz_jl_text_color,
			hz_sidebar_bg_color,
			hz_sidebar_tx_color,
			hz_sidebar_width
		},
		setAttributes
	} = props;

	const isProFeature = ( feature ) => wp.hooks.applyFilters( 'awsm_block_is_pro_feature', false, feature );

	// Local state for block settings
	const filtersInitRef = useRef( false );
	const specifications = window.awsmJobsAdmin?.awsm_filters_block || [];

	const block_appearance_list = [];
	const block_job_listing = [];
	const block_styles_panel = [];

	const { wasJustInserted } = useSelect(
		select => {
			const editor = select( "core/block-editor" );
			return {
				wasJustInserted: editor?.wasBlockJustInserted
					? editor.wasBlockJustInserted( clientId )
					: false,
			};
		},
		[ clientId ]
	);

	useEffect( () => {
		const expectedId = "block-" + clientId;
		if ( ! blockId || blockId !== expectedId ) {
			// Persist a stable, unique id so CSS variables can be scoped per-block on the frontend.
			// Also regenerate on duplication (blockId won't match the new clientId).
			setAttributes( {blockId: expectedId} );
		}
	}, [] );

	useEffect( () => {
		if ( filter_options?.length && filter_options.some( option => typeof option === "object" && option?.specKey ) ) {
			// Migrate old object format [{specKey, value}] → string array + filter_types object
			const newFilterOptions = filter_options.map( option =>
				typeof option === "object" && option.specKey ? option.specKey : option
			);
			const newFilterTypes = { ...filter_types };
			filter_options.forEach( option => {
				if ( typeof option === "object" && option.specKey ) {
					newFilterTypes[ option.specKey ] = option.value || "dropdown";
				}
			} );
			setAttributes( { filter_options: newFilterOptions, filter_types: newFilterTypes } );
		}
	}, [] );

	useEffect( () => {
		if ( ! wasJustInserted && blockId ) return;
		if ( filtersInitRef.current ) return;
		if ( ! enable_job_filter ) return;
		if ( ! specifications?.length ) return;
		if ( filter_options?.length ) return;

		const newFilterOptions = specifications.map( spec => spec.key );
		const newFilterTypes = {};
		specifications.forEach( spec => {
			newFilterTypes[ spec.key ] = "dropdown";
		} );

		setAttributes( { filter_options: newFilterOptions, filter_types: newFilterTypes } );
		filtersInitRef.current = true;
	}, [ wasJustInserted, specifications, enable_job_filter ] );

	useEffect( () => {
		const updates = {};

		if ( layout === "stack" && ! isProFeature( "stack_layout" ) ) {
			updates.layout = "list";
		}

		if ( list_type === "filtered" && ! isProFeature( "filtered_list" ) ) {
			updates.list_type = "all";
		}

		if ( ! isProFeature( "multi_select_filter" ) && filter_types && Object.keys( filter_types ).length ) {
			const resetTypes = {};
			let hasCheckbox = false;
			Object.entries( filter_types ).forEach( ( [ key, val ] ) => {
				if ( val === "checkbox" ) {
					resetTypes[ key ] = "dropdown";
					hasCheckbox = true;
				} else {
					resetTypes[ key ] = val;
				}
			} );
			if ( hasCheckbox ) {
				updates.filter_types = resetTypes;
			}
		}

		if ( Object.keys( updates ).length ) {
			setAttributes( updates );
		}
	}, [] );

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
					<div className="hz-awsm-ic-settings">
					<PanelBody
						title={ __( "Search & Filters", "wp-job-openings" ) }
						initialOpen={ true }
					>
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

						<ToggleControl
							label={ __( "Enable Search", "wp-job-openings" ) }
							checked={ search }
							onChange={ newSearch => {
								setAttributes( { search: newSearch } );
							} }
						__nextHasNoMarginBottom
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
								__nextHasNoMarginBottom
								__next40pxDefaultSize
								/>
							</>
						) }

						<ToggleControl
							label={ __( "Enable Filters", "wp-job-openings" ) }
							checked={ enable_job_filter }
							onChange={ newFilter => {
								const updates = { enable_job_filter: newFilter };
								if ( newFilter && ! filter_options?.length && specifications?.length ) {
									const newFilterOptions = specifications.map( spec => spec.key );
									const newFilterTypes = {};
									specifications.forEach( spec => {
										newFilterTypes[ spec.key ] = "dropdown";
									} );
									updates.filter_options = newFilterOptions;
									updates.filter_types = newFilterTypes;
								}
								setAttributes( updates );
							} }
						__nextHasNoMarginBottom
						/>
						{ enable_job_filter && (
							<>
								<h2>{ __( "Available Filters", "wp-job-openings" ) }</h2>
								{ specifications.map( spec => {
									const isActive = filter_options.includes( spec.key );
									const filterType = filter_types[ spec.key ] || "dropdown";
									const hasMultipleSelectedTerms =
										( selected_terms?.[ spec.key ] || [] ).length > 1;

									return (
										<div key={ spec.key } style={{ marginBottom: '8px' }}>
											<ToggleControl
												label={ spec.label }
												checked={ isActive }
												onChange={ toggleValue => {
													const updatedFilterOptions = toggleValue
														? [ ...filter_options, spec.key ]
														: filter_options.filter( key => key !== spec.key );

													const updatedFilterTypes = { ...filter_types };
													if ( toggleValue && ! updatedFilterTypes[ spec.key ] ) {
														updatedFilterTypes[ spec.key ] = hasMultipleSelectedTerms ? "checkbox" : "dropdown";
													}

													const updates = { filter_options: updatedFilterOptions, filter_types: updatedFilterTypes };
													if ( updatedFilterOptions.length === 0 ) {
														updates.enable_job_filter = false;
													}
													setAttributes( updates );
												} }
											__nextHasNoMarginBottom
											/>

											{ isActive && (
												<div className="filters-button">
													<Button
														className={ `filter-btn ${ filterType === "dropdown" ? "is-active" : "" }` }
														__next40pxDefaultSize
														onClick={ () => setAttributes( { filter_types: { ...filter_types, [ spec.key ]: "dropdown" } } ) }
													>
														{ __( "Single", "wp-job-openings" ) }
													</Button>

													<Button
														className={ `filter-btn ${ filterType === "checkbox" ? "is-active" : "" }${ ! isProFeature( "multi_select_filter" ) ? " is-pro-locked" : "" }` }
														__next40pxDefaultSize
														onClick={ () => {
															if ( ! isProFeature( "multi_select_filter" ) ) return;
															setAttributes( { filter_types: { ...filter_types, [ spec.key ]: "checkbox" } } );
														} }
													>
														{ __( "Multiple", "wp-job-openings" ) }
														{ ! isProFeature( "multi_select_filter" ) && <span className="awsm-pro-badge">Pro</span> }
													</Button>
												</div>
											) }
										</div>
									);
								} ) }
							<SelectControl
								label={ __( "Order Filter Items By", "wp-job-openings" ) }
								value={ filter_items_order }
								options={ [
									{ value: 'custom',     label: __( 'Custom Ordering', 'wp-job-openings' ) },
									{ value: 'alpha_asc',  label: __( 'Alphabetical (A → Z)', 'wp-job-openings' ) },
									{ value: 'alpha_desc', label: __( 'Alphabetical (Z → A)', 'wp-job-openings' ) },
								] }
								onChange={ value =>
									setAttributes( {filter_items_order: value} )
							}
								__nextHasNoMarginBottom
								__next40pxDefaultSize
							/>
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
							onChange={ newLayout => {
								if ( newLayout === "stack" && ! isProFeature( "stack_layout" ) ) return;
								setAttributes( { layout: newLayout } );
							} }
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
								className={ ! isProFeature( "stack_layout" ) ? "is-pro-locked" : "" }
								label={
									! isProFeature( "stack_layout" )
										? <span>{ __( "Stack", "wp-job-openings" ) }<span className="awsm-pro-badge">Pro</span></span>
										: __( "Stack", "wp-job-openings" )
								}
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
							__nextHasNoMarginBottom
							__next40pxDefaultSize
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
						__nextHasNoMarginBottom
						__next40pxDefaultSize
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
						__nextHasNoMarginBottom
						__next40pxDefaultSize
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
							__nextHasNoMarginBottom
							/>
						) ) }

						<h2>
							{ __( "Show Spec Icon in the Listing", "wp-job-openings" ) }
						</h2>
						<ToggleControl
							label={ __( "Show Spec Icon", "wp-job-openings" ) }
							checked={ show_spec_icon }
							onChange={ show_spec_icon => setAttributes( {show_spec_icon} ) }
						__nextHasNoMarginBottom
						/>
					</PanelBody>

					<PanelBody title={ __( "Job Listing", "wp-job-openings" ) }>
						<ToggleGroupControl
							label={ __( "List Type", "wp-job-openings" ) }
							value={ list_type }
							onChange={ newlist_type => {
								if ( newlist_type === "filtered" && ! isProFeature( "filtered_list" ) ) return;
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
								className={ ! isProFeature( "filtered_list" ) ? "is-pro-locked" : "" }
								label={
									! isProFeature( "filtered_list" )
										? <span>{ __( "Filtered", "wp-job-openings" ) }<span className="awsm-pro-badge">Pro</span></span>
										: __( "Filtered", "wp-job-openings" )
								}
							/>
						</ToggleGroupControl>
						<p>
							{ __(
								" Display all jobs or filtered by job specifications",
								"wp-job-openings"
							) }
						</p>

						{ wp.hooks.applyFilters( 'awsm_jobs_block_filtered_list_ui', null, { list_type, selected_terms, selected_terms_main, specifications, setAttributes, filter_types } ) }

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
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						/>

						<ToggleControl
							label={ __( "Hide Expired Jobs", "wp-job-openings" ) }
							checked={ hide_expired_jobs }
							onChange={ hide_expired_jobs =>
								setAttributes( {hide_expired_jobs} )
							}
						__nextHasNoMarginBottom
						/>

						{ wp.hooks.doAction(
							"after_awsm_block_job_listing",
							block_job_listing,
							props
						) }
						{ block_job_listing }
					</PanelBody>
					</div>
				</Fragment>
			</InspectorControls>
			{ /* End */ }

			{ /* Control Styles  */ }
			<InspectorControls group="styles">
				<Fragment>
					<div className="hz-awsm-ic-styles">
						{ placement === "side" && ( search || enable_job_filter ) && (
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
										const color = newBorder && 'color' in newBorder ? newBorder.color : hz_sf_border?.color;
										setAttributes( {
											hz_sf_border: {
												...hz_sf_border,
												...newBorder,
												width,
												color: parseFloat( width ) > 0 ? color : undefined
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
									values={ hz_sf_padding?.top ? hz_sf_padding : { top: "15px", right: "15px", bottom: "15px", left: "15px" } }
									onChange={ Padding => {
										setAttributes( {hz_sf_padding: Padding} );
									} }
								/>
								<Spacer marginBottom={ 4 } />
								<PanelColorSettings
									className="hz-color-settings"
									title={ __( "Sidebar Colors", "wp-job-openings" ) }
									initialOpen={ true }
									colorSettings={ [
										{
											value: hz_sidebar_bg_color,
											onChange: color =>
												setAttributes( {hz_sidebar_bg_color: color} ),
											label: __( "Background Color", "wp-job-openings" )
										},
										{
											value: hz_sidebar_tx_color,
											onChange: color =>
												setAttributes( {hz_sidebar_tx_color: color} ),
											label: __( "Text Color", "wp-job-openings" )
										}
									] }
								/>
							</PanelBody>
						) }

						{ ( search || enable_job_filter ) && (
							<PanelBody
								title={ __( "Search and Filters", "wp-job-openings" ) }
								initialOpen={ true }
							>
								<BorderControl
									label={ __( "Border", "wp-job-openings" ) }
									withSlider
									isCompact={ true }
									value={ hz_ls_border }
									__experimentalIsRenderedInSidebar
									onChange={ newBorder => {
										setAttributes( {
											hz_ls_border: enforceMinBorderWidth( newBorder, hz_ls_border )
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
								<Spacer marginBottom={ 4 } />
								<PanelColorSettings
									className="hz-color-settings"
									title={ __( "Search & Filters Colors", "wp-job-openings" ) }
									initialOpen={ true }
									colorSettings={ [
										{
											value: hz_sf_background_color,
											onChange: color =>
												setAttributes( {hz_sf_background_color: color} ),
											label: __( "Background Color", "wp-job-openings" )
										},
										{
											value: hz_sf_text_color,
											onChange: color =>
												setAttributes( {hz_sf_text_color: color} ),
											label: __( "Text Color", "wp-job-openings" )
										}
									] }
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
									setAttributes( {
										hz_jl_border: enforceMinBorderWidth( newBorder, hz_jl_border )
									} );
								} }
								enableStyle={ false }
							/>

							<Spacer marginBottom={ 4 } />

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

							<Spacer marginBottom={ 4 } />

							<BoxControl
								label={ __( "Padding", "wp-job-openings" ) }
								values={ hz_jl_padding?.top ? hz_jl_padding : { top: "15px", right: "15px", bottom: "15px", left: "15px" } }
								onChange={ Padding => {
									setAttributes( {hz_jl_padding: Padding} );
								} }
							/>
							<Spacer marginBottom={ 4 } />

							<PanelRow>
								<strong>{ __( "Button", "wp-job-openings" ) }</strong>
							</PanelRow>
							<TextControl
								label={ __( "Button Text", "wp-job-openings" ) }
								value={ hz_button_text ?? '' }
								placeholder={ __( "More Details →", "wp-job-openings" ) }
								onChange={ value => setAttributes( { hz_button_text: value } ) }
								__nextHasNoMarginBottom
							/>
							<Spacer marginBottom={ 4 } />
							<ToggleGroupControl
								label={ __( "Button Style", "wp-job-openings" ) }
								value={ hz_button_style }
								onChange={ value => setAttributes( { hz_button_style: value } ) }
								isBlock
								__nextHasNoMarginBottom
								__next40pxDefaultSize
							>
								<ToggleGroupControlOption value="none" label={ __( "None", "wp-job-openings" ) } />
								<ToggleGroupControlOption value="filled" label={ __( "Fill", "wp-job-openings" ) } />
								<ToggleGroupControlOption value="outlined" label={ __( "Outline", "wp-job-openings" ) } />
							</ToggleGroupControl>
							{ hz_button_style !== 'none' && (
								<>
									<Spacer marginBottom={ 4 } />
									<BorderControl
										label={ __( "Border", "wp-job-openings" ) }
										withSlider
										isCompact={ true }
										value={ hz_bs_border }
										__experimentalIsRenderedInSidebar
										onChange={ newBorder => {
											const width = newBorder?.width ?? hz_bs_border?.width;
											const color = newBorder && 'color' in newBorder ? newBorder.color : hz_bs_border?.color;
											setAttributes( {
												hz_bs_border: {
													...hz_bs_border,
													...newBorder,
													width,
													color: parseFloat( width ) > 0 ? color : undefined
												}
											} );
										} }
										enableStyle={ false }
									/>

									<Spacer marginBottom={ 4 } />

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

									<Spacer marginBottom={ 4 } />

									<BoxControl
										label={ __( "Padding", "wp-job-openings" ) }
										values={ hz_bs_padding?.top ? hz_bs_padding : { top: "13px", right: "13px", bottom: "13px", left: "13px" } }
										onChange={ Padding => {
											setAttributes( {hz_bs_padding: Padding} );
										} }
									/>
								</>
							) }

							<Spacer marginBottom={ 4 } />

							<PanelColorSettings
								className="hz-color-settings"
								title={ __( "Job Listing Colors", "wp-job-openings" ) }
								initialOpen={ true }
								colorSettings={ [
									{
										value: hz_jl_background_color,
										onChange: color =>
											setAttributes( {hz_jl_background_color: color} ),
										label: __( "Listing Background", "wp-job-openings" )
									},
									{
										value: hz_jl_text_color,
										onChange: color =>
											setAttributes( {hz_jl_text_color: color} ),
										label: __( "Listing Text", "wp-job-openings" )
									},
									...( hz_button_style !== 'none' ? [ {
										value: hz_button_background_color,
										onChange: color =>
											setAttributes( {hz_button_background_color: color} ),
										label: __( "Button Background", "wp-job-openings" )
									} ] : [] ),
									{
										value: hz_button_text_color,
										onChange: color =>
											setAttributes( {hz_button_text_color: color} ),
										label: __( "Button Text", "wp-job-openings" )
									}
								] }
							/>

						</PanelBody>

						<PanelBody title={ __( "Pagination", "wp-job-openings" ) } initialOpen={ true }>
							<BorderControl
								label={ __( "Border", "wp-job-openings" ) }
								withSlider
								isCompact={ true }
								value={ hz_pagination_border }
								__experimentalIsRenderedInSidebar
								onChange={ newBorder => {
									setAttributes( {
										hz_pagination_border: enforceMinBorderWidth( newBorder, hz_pagination_border )
									} );
								} }
								enableStyle={ false }
							/>

							<Spacer marginBottom={ 4 } />

							<BorderRadiusControl
								values={ hz_pagination_border_radius }
								onChange={ newRadius => {
									if ( typeof newRadius === "string" ) {
										const radiusObject = {
											topLeft: newRadius,
											topRight: newRadius,
											bottomRight: newRadius,
											bottomLeft: newRadius
										};
										setAttributes( {hz_pagination_border_radius: radiusObject} );
									} else {
										setAttributes( {hz_pagination_border_radius: newRadius} );
									}
								} }
							/>

							<Spacer marginBottom={ 4 } />

							<BoxControl
								label={ __( "Padding", "wp-job-openings" ) }
								values={ hz_pagination_padding?.top ? hz_pagination_padding : { top: pagination === 'classic' ? '5px' : '20px', right: pagination === 'classic' ? '5px' : '20px', bottom: pagination === 'classic' ? '5px' : '20px', left: pagination === 'classic' ? '5px' : '20px' } }
								onChange={ Padding => {
									setAttributes( {hz_pagination_padding: Padding} );
								} }
							/>

							<Spacer marginBottom={ 4 } />

							<PanelColorSettings
								className="hz-color-settings"
								title={ __( "Pagination Colors", "wp-job-openings" ) }
								initialOpen={ true }
								colorSettings={ [
									{
										value: hz_pagination_background_color,
										onChange: color =>
											setAttributes( {hz_pagination_background_color: color} ),
										label: __( "Background Color", "wp-job-openings" )
									},
									{
										value: hz_pagination_text_color,
										onChange: color =>
											setAttributes( {hz_pagination_text_color: color} ),
										label: __( "Text Color", "wp-job-openings" )
									}
								] }
							/>
						</PanelBody>

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
