'use strict';

jQuery( function ( $ ) {
	const rootWrapperSelector = '.awsm-b-job-wrap';
	const wrapperSelector = '.awsm-b-job-listings';
	const sectionSelector = '.awsm-b-job-listing-items';

	/* ========== Job Search and Filtering ========== */

	const filterSelector = '.awsm-b-filter-wrap';
	const currentUrl =
		window.location.protocol +
		'//' +
		window.location.host +
		window.location.pathname;
	let triggerFilter = true; 

	function getListingsData( $wrapper ) {
		const data = [];
		const parsedListingsAttrs = [
			'listings',
			'specs',
			'search',
			'lang',
			'taxonomy',
			'termId',
		];

		/* added for block */
		parsedListingsAttrs.push( 'awsm-layout' );
		parsedListingsAttrs.push( 'awsm-hide-expired-jobs' );
		parsedListingsAttrs.push( 'awsm-other-options' );
		parsedListingsAttrs.push( 'awsm-listings-total' );
		parsedListingsAttrs.push( 'awsm-selected-terms' );
		/* end */

		/* added for block styles tab */
		parsedListingsAttrs.push('hz_sf_border_color');
		parsedListingsAttrs.push('hz_sf_border_width');
		parsedListingsAttrs.push('hz_sf_padding');
		parsedListingsAttrs.push('hz_sf_border_radius');
		parsedListingsAttrs.push('hz_sidebar_width');
		parsedListingsAttrs.push('block_id');
		parsedListingsAttrs.push('hz_ls_border_color');
		parsedListingsAttrs.push('hz_ls_border_width');
		parsedListingsAttrs.push('hz_ls_border_radius');
		parsedListingsAttrs.push('hz_jl_border_color');
		parsedListingsAttrs.push('hz_jl_border_width');
		parsedListingsAttrs.push('hz_jl_border_radius');
		parsedListingsAttrs.push('hz_jl_padding');
		parsedListingsAttrs.push('hz_bs_border_color');
		parsedListingsAttrs.push('hz_bs_border_width');
		parsedListingsAttrs.push('hz_bs_border_radius');
		parsedListingsAttrs.push('hz_bs_padding');
		parsedListingsAttrs.push('hz_button_background_color');
		parsedListingsAttrs.push('hz_button_text_color');
		/* end */

		$( document ).trigger( 'awsmJobBlockListingsData', [
			parsedListingsAttrs,
		] );

		const dataAttrs = $wrapper.data();
		$.each( dataAttrs, function ( dataAttr, value ) {
			if ( $.inArray( dataAttr, parsedListingsAttrs ) === -1 ) {
				data.push( {
					name: dataAttr,
					value,
				} );
			}
		} );
		return data;
	}

	function awsmJobFilters( $rootWrapper ) {
		const $wrapper = $rootWrapper.find( wrapperSelector );
		const $rowWrapper = $wrapper.find( sectionSelector );
		const $filterForm = $rootWrapper.find( filterSelector + ' form' );
		let formData = [];

		if ( $filterForm.length > 0 ) {
			// Form exists → Serialize form data
			formData = $filterForm.serializeArray();
			var formMethod = $filterForm.attr( 'method' )
				? $filterForm.attr( 'method' ).toUpperCase()
				: 'POST';
		} else {
			// Form is missing → Manually construct data
			formData.push( { name: 'action', value: 'block_jobfilter' } ); // Ensure action is included
			var formMethod = 'POST';
		}

		const listings = $wrapper.data( 'listings' );
		const specs = $wrapper.data( 'specs' );
		const layout = $wrapper.data( 'awsm-layout' );
		const hide_expired_jobs = $wrapper.data( 'awsm-hide-expired-jobs' );
		let selected_terms = $wrapper.data( 'awsm-selected-terms' );
		const other_options = $wrapper.data( 'awsm-other-options' );
		const listings_total = $wrapper.data( 'awsm-listings-total' );

		/* variables for style tabs */
		const hz_sf_border_color = $wrapper.data('hz_sf_border_color');
		const hz_sf_border_width = $wrapper.data('hz_sf_border_width');
		const hz_sf_padding = $wrapper.data('hz_sf_padding');
		const hz_sf_border_radius = $wrapper.data('hz_sf_border_radius');
		const hz_sidebar_width = $wrapper.data('hz_sidebar_width');
		const block_id = $wrapper.data('block_id');
		const hz_ls_border_color = $wrapper.data('hz_ls_border_color');
		const hz_ls_border_width = $wrapper.data('hz_ls_border_width');
		const hz_ls_border_radius = $wrapper.data('hz_ls_border_radius');
		const hz_jl_border_color = $wrapper.data('hz_jl_border_color');
		const hz_jl_border_width = $wrapper.data('hz_jl_border_width');
		const hz_jl_border_radius = $wrapper.data('hz_jl_border_radius');
		const hz_jl_padding = $wrapper.data('hz_jl_padding');
		const hz_bs_border_color = $wrapper.data('hz_bs_border_color');
		const hz_bs_border_width = $wrapper.data('hz_bs_border_width');
		const hz_bs_border_radius = $wrapper.data('hz_bs_border_radius');
		const hz_bs_padding = $wrapper.data('hz_bs_padding');
		const hz_button_background_color = $wrapper.data('hz_button_background_color');
		const hz_button_text_color = $wrapper.data('hz_button_text_color');
		/* End */

		formData.push( { name: 'listings_per_page', value: listings } );

		if ( typeof specs !== 'undefined' ) {
			formData.push( { name: 'shortcode_specs', value: specs } );
		}

		if ( typeof layout !== 'undefined' ) {
			formData.push( { name: 'awsm-layout', value: layout } );
		}

		if ( selected_terms ) {
			if ( typeof selected_terms === 'string' ) {
				try {
					selected_terms = JSON.parse( selected_terms );
				} catch ( error ) {
					console.error(
						'Failed to parse selected_terms JSON:',
						error
					);
					selected_terms = {};
				}
			}
			formData.push( {
				name: 'awsm-selected-terms',
				value: JSON.stringify( selected_terms ),
			} );
		}

		if ( typeof hide_expired_jobs !== 'undefined' ) {
			formData.push( {
				name: 'awsm-hide-expired-jobs',
				value: hide_expired_jobs,
			} );
		}

		if ( typeof other_options !== 'undefined' ) {
			formData.push( {
				name: 'awsm-other-options',
				value: other_options,
			} );
		}

		if ( typeof listings_total !== 'undefined' ) {
			formData.push( {
				name: 'awsm-listings-total',
				value: listings_total,
			} );
		}

		/* variables for style */
		if (typeof hz_sf_border_color !== 'undefined') {
			formData.push({ name: 'hz_sf_border_color', value: hz_sf_border_color });
		}
		if (typeof hz_sf_border_width !== 'undefined') {
			formData.push({ name: 'hz_sf_border_width', value: hz_sf_border_width });
		}
		if (typeof hz_sf_padding !== 'undefined') {
			formData.push({ name: 'hz_sf_padding', value: JSON.stringify(hz_sf_padding) });
		}
		if (typeof hz_sf_border_radius !== 'undefined') {
			formData.push({ name: 'hz_sf_border_radius', value: hz_sf_border_radius });
		}
		if (typeof hz_sidebar_width !== 'undefined') {
			formData.push({ name: 'hz_sidebar_width', value: hz_sidebar_width });
		}
		if (typeof block_id !== 'undefined') {
			formData.push({ name: 'block_id', value: block_id });
		}
		if (typeof hz_ls_border_color !== 'undefined') {
			formData.push({ name: 'hz_ls_border_color', value: hz_ls_border_color });
		}
		if (typeof hz_ls_border_width !== 'undefined') {
			formData.push({ name: 'hz_ls_border_width', value: hz_ls_border_width });
		}
		if (typeof hz_ls_border_radius !== 'undefined') {
			formData.push({ name: 'hz_ls_border_radius', value: hz_ls_border_radius });
		}
		if (typeof hz_jl_border_color !== 'undefined') {
			formData.push({ name: 'hz_jl_border_color', value: hz_jl_border_color });
		}
		if (typeof hz_jl_border_width !== 'undefined') {
			formData.push({ name: 'hz_jl_border_width', value: hz_jl_border_width });
		}
		if (typeof hz_jl_border_radius !== 'undefined') {
			formData.push({ name: 'hz_jl_border_radius', value: hz_jl_border_radius });
		}
		if (typeof hz_jl_padding !== 'undefined') {
			formData.push({ name: 'hz_jl_padding', value: JSON.stringify(hz_jl_padding) });
		}
		if (typeof hz_bs_border_color !== 'undefined') {
			formData.push({ name: 'hz_bs_border_color', value: hz_bs_border_color });
		}
		if (typeof hz_bs_border_width !== 'undefined') {
			formData.push({ name: 'hz_bs_border_width', value: hz_bs_border_width });
		}
		if (typeof hz_bs_border_radius !== 'undefined') {
			formData.push({ name: 'hz_bs_border_radius', value: hz_bs_border_radius });
		}
		if (typeof hz_bs_padding !== 'undefined') {
			formData.push({ name: 'hz_bs_padding', value: JSON.stringify(hz_bs_padding) });
		}
		if (typeof hz_button_background_color !== 'undefined') {
			formData.push({ name: 'hz_button_background_color', value: hz_button_background_color });
		}
		if (typeof hz_button_text_color !== 'undefined') {
			formData.push({ name: 'hz_button_text_color', value: hz_button_text_color });
		}
		/* End */

		const listingsData = getListingsData( $wrapper );
		if ( listingsData.length > 0 ) {
			formData = formData.concat( listingsData );
		}

		// Trigger custom event to provide formData
		$( document ).trigger( 'awsmJobBlockFiltersFormData', [
			$wrapper,
			formData,
		] );

		if ( triggerFilter ) {
			triggerFilter = false;

			// Determine action URL (fallback if form is missing)
			const actionUrl =
				$filterForm.length > 0
					? $filterForm.attr( 'action' )
					: awsmJobsPublic.ajaxurl;

			$.ajax( {
				url: actionUrl,
				beforeSend() {
					$wrapper.addClass( 'awsm-jobs-loading' );
				},
				data: formData,
				type: formMethod,
			} )
				.done( function ( response ) { 
					$rowWrapper.html( response.data.html );  

					if (response.data.style) {
						// Append new style tag
						jQuery('head').append(response.data.style);
					}

					const $searchControl =
						$rootWrapper.find( '.awsm-b-job-search' );
					if ( $searchControl.length > 0 ) {
						if ( $searchControl.val().length > 0 ) {
							$rootWrapper
								.find( '.awsm-b-job-search-btn' )
								.addClass( 'awsm-job-hide' );
							$rootWrapper
								.find( '.awsm-b-job-search-close-btn' )
								.removeClass( 'awsm-job-hide' );
						} else {
							$rootWrapper
								.find( '.awsm-b-job-search-btn' )
								.removeClass( 'awsm-job-hide' );
							$rootWrapper
								.find( '.awsm-b-job-search-close-btn' )
								.addClass( 'awsm-job-hide' );
						}
					}
					$( document ).trigger( 'awsmjobs_filtered_listings', [
						$rootWrapper,
						response.data.html,
					] );
				} )
				.fail( function ( xhr ) {
					console.log( xhr );
				} )
				.always( function () {
					$wrapper.removeClass( 'awsm-jobs-loading' );
					triggerFilter = true;
				} );
				
		}
	}

	function filterCheck( $filterForm ) {
		let check = false;
		if ( $filterForm.length > 0 ) {
			const $filterOption = $filterForm.find( '.awsm-b-filter-option' );
			$filterOption.each( function () {
				if ( $( this ).val().length > 0 ) {
					check = true;
				}
			} );
		}
		return check;
	}

	function searchJobs( $elem ) {
		const $rootWrapper = $elem.parents( rootWrapperSelector );
		const searchQuery = $rootWrapper.find( '.awsm-b-job-search' ).val();
		$rootWrapper.find( wrapperSelector ).data( 'search', searchQuery );
		if ( searchQuery.length === 0 ) {
			//$rootWrapper.find('.awsm-b-job-search-icon-wrapper').addClass('awsm-b-job-hide');
		}
		setPaginationBase( $rootWrapper, 'jq', searchQuery );
		if ( awsmJobsPublic.deep_linking.search ) {
			const $paginationBase = $rootWrapper.find(
				'input[name="awsm_pagination_base"]'
			);
			updateQuery( 'jq', searchQuery, $paginationBase.val() );
		}
		awsmJobFilters( $rootWrapper );
	} 

	/* if ( $( rootWrapperSelector ).length > 0 ) {
		$( rootWrapperSelector ).each( function () {
			const $currentWrapper = $( this );
			const $filterForm = $currentWrapper.find(
				filterSelector + ' form'
			);
			if (
				awsmJobsPublic.is_search.length > 0 ||
				filterCheck( $filterForm )
			) {
				triggerFilter = true;
				awsmJobFilters( $currentWrapper );
			}
		} );
	} */

	/* if ( $( rootWrapperSelector ).length > 0 ) {
		$( rootWrapperSelector ).each( function () {
			const $currentWrapper = $( this );
			const $filterForm = $currentWrapper.find(
				filterSelector + ' form'
			);

			const searchParams = new URLSearchParams( window.location.search );
			let hasFiltersInURL = false;

			if ( searchParams.toString().length > 0 ) {
				hasFiltersInURL = true;
			}

			if ( hasFiltersInURL || filterCheck( $filterForm ) ) {
				triggerFilter = true;
				awsmJobFilters( $currentWrapper );
			}
		} );
	}
 */
	var updateQuery = function ( key, value, url ) {
		url = typeof url !== 'undefined' ? url : currentUrl;
		url = url.split( '?' )[ 0 ];
		const searchParams = new URLSearchParams( document.location.search );
		if ( searchParams.has( 'paged' ) ) {
			searchParams.delete( 'paged' );
		}
		if ( value.length > 0 ) {
			searchParams.set( key, value );
		} else {
			searchParams.delete( key );
		}
		let modQueryString = searchParams.toString();
		if ( modQueryString.length > 0 ) {
			modQueryString = '?' + modQueryString;
		}
		window.history.replaceState( {}, '', url + modQueryString );
	};

	var setPaginationBase = function ( $rootWrapper, key, value ) {
		const $paginationBase = $rootWrapper.find(
			'input[name="awsm_pagination_base"]'
		);
		if ( $paginationBase.length > 0 ) {
			const splittedURL = $paginationBase.val().split( '?' );
			let queryString = '';
			if ( splittedURL.length > 1 ) {
				queryString = splittedURL[ 1 ];
			}
			const searchParams = new URLSearchParams( queryString );
			if ( value.length > 0 ) {
				searchParams.set( key, value );
			} else {
				searchParams.delete( key );
			}
			$paginationBase.val(
				splittedURL[ 0 ] + '?' + searchParams.toString()
			);
			$rootWrapper.find( 'input[name="paged"]' ).val( 1 );
		}
	};

	if ( $( '.awsm-b-job-no-more-jobs-get' ).length > 0 ) {
		$( '.awsm-b-job-listings' ).hide();
		$( '.awsm-b-job-no-more-jobs-get' ).slice( 1 ).hide();
	}

	$(filterSelector + ' .awsm-b-filter-option').on('change', function (e) {
		e.preventDefault();
		$('.awsm-b-job-listings').show();
	
		const $elem = $(this);
		const $rootWrapper = $elem.closest(rootWrapperSelector);
		const currentSpec = $elem.closest('.awsm-b-filter-item').data('filter');
	
		const isMultiple = $elem.prop('multiple'); // Check if it's a multiple select
		const allOptions = $elem.find('option');
		const firstOption = allOptions.eq(0); // "All Job Type"
		const selectedOptions = $elem.find('option:selected');
		const isAllSelected = firstOption.prop('selected');
	
		// **Fix: Restrict list item selection to current dropdown only**
		const allLiItems = $elem.closest('.awsm-b-filter-item').find('ul li');
		const firstLiItem = allLiItems.eq(0); // "All Job Type" in <ul>
		const selectedLiItems = allLiItems.filter('.selected');
	
		const isCheckboxFilter = $elem.closest('.awsm-b-filter-item').find('input[type="checkbox"]').length > 0;
		let slugs = [];
	
		if (isMultiple) { 
			if (isAllSelected) {
				// **Select all options within this dropdown only**
				allOptions.prop('selected', true).addClass('selected');
				allLiItems.addClass('selected'); // **Fix: Only apply to current dropdown**
				slugs = allOptions.slice(1).map(function () {
					return $(this).data('slug');
				}).get().filter(Boolean);
			} else if (selectedOptions.length === 0) {
				// **Deselect all in the current dropdown only**
				allOptions.prop('selected', false).removeClass('selected');
				allLiItems.removeClass('selected'); // **Fix: Only affect current dropdown**
				slugs = [];
			} else {
				// **Handle individual selection within the current dropdown**
				selectedOptions.each(function () {
					$(this).prop('selected', true).addClass('selected');
					const index = $(this).index();
					allLiItems.eq(index).addClass('selected'); // **Fix: Apply changes to corresponding <li>**
				});
	
				slugs = selectedOptions.map(function () {
					return $(this).data('slug');
				}).get().filter(Boolean);
			}
		} else if (isCheckboxFilter) {
			// **Handle checkboxes**
			const $checkboxes = $elem.closest('.awsm-b-filter-item').find('input[type="checkbox"]');
			const $allCheckbox = $checkboxes.eq(0); // First checkbox is "All"
	
			if ($allCheckbox.prop('checked')) {
				// **Select all checkboxes in this filter group only**
				$checkboxes.prop('checked', true).addClass('selected').trigger('change');
				slugs = $checkboxes.slice(1).map(function () {
					return $(this).data('slug');
				}).get().filter(Boolean);
			} else { 
				// **Handle individual checkbox selection**
				slugs = $checkboxes.filter(':checked').map(function () {
					return $(this).data('slug');
				}).get().filter(Boolean);  
			}
		} else {
			// **Single select logic**
			slugs = selectedOptions.data('slug') ? [selectedOptions.data('slug')] : [];
		}
	
		const slugString = slugs.length > 0 ? slugs.join(',') : ''; 
	
		// **Update pagination and filters only for the affected dropdown**
		if ($('.awsm-job-listings').length > 0) {
			$rootWrapper.find('.awsm-b-job-no-more-jobs-get').hide();
		}
	
		setPaginationBase($rootWrapper, currentSpec, slugString);
	
		// **Update the URL without affecting other dropdowns**
		if (awsmJobsPublic.deep_linking.spec) {
			const $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
			updateQuery(currentSpec, slugString, $paginationBase.val());
		}
	
		awsmJobFilters($rootWrapper);
	});

	$( filterSelector + ' .awsm-filter-checkbox' ).on(
		'change',
		function ( e ) {
			const selectedFilters = {};
			const slugs = []; // Initialize an array to collect slugs
			const $elem = $( this );
			const $rootWrapper = $elem.parents( rootWrapperSelector );
			const currentSpec = $elem
				.parents( '.awsm-filter-list-item' )
				.data( 'filter' );

			// Loop through checked checkboxes and build selectedFilters and slugs array
			$( '.awsm-filter-checkbox:checked' ).each( function () {
				const taxonomy = $( this ).data( 'taxonomy' );
				const termId = $( this ).data( 'term-id' );
				const slug = $( this ).data( 'slug' ); // Get the slug from the checkbox

				// Add the slug to the slugs array if it exists
				if ( slug ) {
					slugs.push( slug );
				}

				// Populate the selectedFilters object
				if ( ! selectedFilters[ taxonomy ] ) {
					selectedFilters[ taxonomy ] = [];
				}
				selectedFilters[ taxonomy ].push( termId );
			} );

			// Convert slugs array to a comma-separated string
			const slugString = slugs.length > 0 ? slugs.join( ',' ) : '';

			// Handle deep linking
			if ( awsmJobsPublic.deep_linking.spec ) {
				const $paginationBase = $rootWrapper.find(
					'input[name="awsm_pagination_base"]'
				);
				updateQuery( currentSpec, slugString, $paginationBase.val() ); // Use the comma-separated slugString
			}

			// Apply the job filters
			awsmJobFilters( $rootWrapper );
		}
	);

	$( filterSelector + ' .awsm-b-job-search-btn' ).on( 'click', function () {
		searchJobs( $( this ) );
	} );

	$( filterSelector + ' .awsm-b-job-search-close-btn' ).on(
		'click',
		function () {
			const $elem = $( this );
			$elem
				.parents( rootWrapperSelector )
				.find( '.awsm-b-job-search' )
				.val( '' );
			searchJobs( $elem );
		}
	);

	$( filterSelector + ' .awsm-b-job-search' ).on( 'keypress', function ( e ) {
		if ( e.which == 13 ) {
			e.preventDefault();
			searchJobs( $( this ) );
		}
	} );

	/* ========== Job Listings Load More ========== */
	$( wrapperSelector ).on(
		'click',
		'.awsm-b-jobs-pagination .awsm-b-load-more-btn, .awsm-b-jobs-pagination a.page-numbers',
		function ( e ) {
			e.preventDefault();
			const $triggerElem = $( this );
			const isDefaultPagination = $triggerElem.hasClass(
				'awsm-b-load-more-btn'
			);
			let paged = 1;
			let wpData = [];

			const $mainContainer = $triggerElem.parents( rootWrapperSelector );
			const $listingsContainer = $mainContainer.find( wrapperSelector );
			const $listingsrowContainer =
				$listingsContainer.find( sectionSelector );

			const $paginationWrapper = $triggerElem.parents(
				'.awsm-b-jobs-pagination'
			);
			const listings = $listingsContainer.data( 'listings' );
			const totalPosts = $listingsContainer.data( 'total-posts' ); // Assuming this is passed via data
			const specs = $listingsContainer.data( 'specs' );
			const lang = $listingsContainer.data( 'lang' );
			const searchQuery = $listingsContainer.data( 'search' );

			/* added for block */
			const layout = $listingsContainer.data( 'awsm-layout' );
			const hide_expired_jobs = $listingsContainer.data(
				'awsm-hide-expired-jobs'
			);
			let selected_terms = $listingsContainer.data(
				'awsm-selected-terms'
			);
			const other_options =
				$listingsContainer.data( 'awsm-other-options' );
			/* end */

			/* variables for style tabs */
			const hz_sf_border_color = $listingsContainer.data('hz_sf_border_color');
			const hz_sf_border_width = $listingsContainer.data('hz_sf_border_width');
			const hz_sf_padding = $listingsContainer.data('hz_sf_padding');
			const hz_sf_border_radius = $listingsContainer.data('hz_sf_border_radius');
			const hz_sidebar_width = $listingsContainer.data('hz_sidebar_width');
			const block_id = $listingsContainer.data('block_id');
			const hz_ls_border_color = $listingsContainer.data('hz_ls_border_color');
			const hz_ls_border_width = $listingsContainer.data('hz_ls_border_width');
			const hz_ls_border_radius = $listingsContainer.data('hz_ls_border_radius');
			const hz_jl_border_color = $listingsContainer.data('hz_jl_border_color');
			const hz_jl_border_width = $listingsContainer.data('hz_jl_border_width');
			const hz_jl_border_radius = $listingsContainer.data('hz_jl_border_radius');
			const hz_jl_padding = $listingsContainer.data('hz_jl_padding');
			const hz_bs_border_color = $listingsContainer.data('hz_bs_border_color');
			const hz_bs_border_width = $listingsContainer.data('hz_bs_border_width');
			const hz_bs_border_radius = $listingsContainer.data('hz_bs_border_radius');
			const hz_bs_padding = $listingsContainer.data('hz_bs_padding');
			const hz_button_background_color = $listingsContainer.data('hz_button_background_color');
			const hz_button_text_color = $listingsContainer.data('hz_button_text_color');
			/* End */

			if ( isDefaultPagination ) {
				$triggerElem.prop( 'disabled', true );
				paged = $triggerElem.data( 'page' );
				paged = typeof paged === 'undefined' ? 1 : paged;
			} else {
				$triggerElem
					.parents( '.page-numbers' )
					.find( '.page-numbers' )
					.removeClass( 'current' )
					.removeAttr( 'aria-current' );
				$triggerElem
					.addClass( 'current' )
					.attr( 'aria-current', 'page' );
			}
			$paginationWrapper.addClass( 'awsm-b-jobs-pagination-loading' );

			// filters
			const $filterForm = $mainContainer.find( filterSelector + ' form' );
			if ( filterCheck( $filterForm ) ) {
				const $filterOption = $filterForm.find(
					'.awsm-b-filter-option'
				);
				wpData = $filterOption.serializeArray();
			}

			const specsList = {};
			$filterForm
				.find( '.awsm-filter-checkbox:checked' )
				.each( function () {
					const $checkbox = $( this );
					const taxonomy = $checkbox.data( 'taxonomy' ); // Get taxonomy from data attribute
					const termId = $checkbox.data( 'term-id' ); // Get term ID from data attribute

					if ( taxonomy && termId ) {
						if ( ! specsList[ taxonomy ] ) {
							specsList[ taxonomy ] = []; // Initialize array for this taxonomy
						}
						specsList[ taxonomy ].push( termId ); // Add term ID to the array
					}
				} );

			for ( var taxonomy in specsList ) {
				if ( specsList.hasOwnProperty( taxonomy ) ) {
					specsList[ taxonomy ].forEach( function ( termId ) {
						wpData.push( {
							name: `awsm_job_specs_list[${ taxonomy }][]`, // Add taxonomy as part of the key
							value: termId,
						} );
					} );
				}
			}

			if ( ! isDefaultPagination ) {
				let paginationBaseURL = $triggerElem.attr( 'href' );
				const splittedURL = paginationBaseURL.split( '?' );
				let queryString = '';
				if ( splittedURL.length > 1 ) {
					const searchParams = new URLSearchParams(
						splittedURL[ 1 ]
					);
					paged = searchParams.get( 'paged' );
					searchParams.delete( 'paged' );
					if ( searchParams.toString().length > 0 ) {
						queryString = '?' + searchParams.toString();
					}
				}
				paginationBaseURL = splittedURL[ 0 ] + queryString;
				wpData.push( {
					name: 'awsm_pagination_base',
					value: splittedURL[ 0 ] + queryString,
				} );
				if ( awsmJobsPublic.deep_linking.pagination ) {
					updateQuery( 'paged', paged, paginationBaseURL );
				}
			}

			// taxonomy archives
			if ( awsmJobsPublic.is_tax_archive ) {
				var taxonomy = $listingsContainer.data( 'taxonomy' );
				const termId = $listingsContainer.data( 'termId' );
				if (
					typeof taxonomy !== 'undefined' &&
					typeof termId !== 'undefined'
				) {
					wpData.push( {
						name: 'awsm_job_spec[' + taxonomy + ']',
						value: termId,
					} );
				}
			}

			wpData.push(
				{
					name: 'action',
					value: 'block_loadmore',
				},
				{
					name: 'paged',
					value: paged,
				}
			);
			if ( typeof listings !== 'undefined' ) {
				wpData.push( {
					name: 'listings_per_page',
					value: listings,
				} );
			}
			if ( typeof specs !== 'undefined' ) {
				wpData.push( {
					name: 'shortcode_specs',
					value: specs,
				} );
			}

			/* added for block */
			if ( typeof layout !== 'undefined' ) {
				wpData.push( {
					name: 'awsm-layout',
					value: layout,
				} );
			}
			if ( typeof hide_expired_jobs !== 'undefined' ) {
				wpData.push( {
					name: 'awsm-hide-expired-jobs',
					value: hide_expired_jobs,
				} );
			}

			if ( selected_terms ) {
				if ( typeof selected_terms === 'string' ) {
					try {
						// Parse the JSON string into an object
						selected_terms = JSON.parse( selected_terms );
					} catch ( error ) {
						console.error(
							'Failed to parse selected_terms JSON:',
							error
						);
						selected_terms = {}; // Fallback to an empty object
					}
				}

				// Push to wpData
				wpData.push( {
					name: 'awsm-selected-terms',
					value: JSON.stringify( selected_terms ), // Send as JSON string
				} );
			}

			if ( typeof other_options !== 'undefined' ) {
				wpData.push( {
					name: 'awsm-other-options',
					value: other_options,
				} );
			}
			if ( typeof listings_total !== 'undefined' ) {
				wpData.push( {
					name: 'awsm-listings-total',
					value: listings_total,
				} );
			}

			if ( typeof lang !== 'undefined' ) {
				wpData.push( {
					name: 'lang',
					value: lang,
				} );
			}

			if ( typeof searchQuery !== 'undefined' ) {
				wpData.push( {
					name: 'jq',
					value: searchQuery,
				} );
			}

			/* variables for style */
			if (typeof hz_sf_border_color !== 'undefined') {
				wpData.push({ name: 'hz_sf_border_color', value: hz_sf_border_color });
			}
			if (typeof hz_sf_border_width !== 'undefined') {
				wpData.push({ name: 'hz_sf_border_width', value: hz_sf_border_width });
			}
			if (typeof hz_sf_padding !== 'undefined') {
				wpData.push({ name: 'hz_sf_padding', value: JSON.stringify(hz_sf_padding) });
			}
			if (typeof hz_sf_border_radius !== 'undefined') {
				wpData.push({ name: 'hz_sf_border_radius', value: hz_sf_border_radius });
			}
			if (typeof hz_sidebar_width !== 'undefined') {
				wpData.push({ name: 'hz_sidebar_width', value: hz_sidebar_width });
			}
			if (typeof block_id !== 'undefined') {
				wpData.push({ name: 'block_id', value: block_id });
			}
			if (typeof hz_ls_border_color !== 'undefined') {
				wpData.push({ name: 'hz_ls_border_color', value: hz_ls_border_color });
			}
			if (typeof hz_ls_border_width !== 'undefined') {
				wpData.push({ name: 'hz_ls_border_width', value: hz_ls_border_width });
			}
			if (typeof hz_ls_border_radius !== 'undefined') {
				wpData.push({ name: 'hz_ls_border_radius', value: hz_ls_border_radius });
			}
			if (typeof hz_jl_border_color !== 'undefined') {
				wpData.push({ name: 'hz_jl_border_color', value: hz_jl_border_color });
			}
			if (typeof hz_jl_border_width !== 'undefined') {
				wpData.push({ name: 'hz_jl_border_width', value: hz_jl_border_width });
			}
			if (typeof hz_jl_border_radius !== 'undefined') {
				wpData.push({ name: 'hz_jl_border_radius', value: hz_jl_border_radius });
			}
			if (typeof hz_jl_padding !== 'undefined') {
				wpData.push({ name: 'hz_jl_padding', value: JSON.stringify(hz_jl_padding) });
			}
			if (typeof hz_bs_border_color !== 'undefined') {
				wpData.push({ name: 'hz_bs_border_color', value: hz_bs_border_color });
			}
			if (typeof hz_bs_border_width !== 'undefined') {
				wpData.push({ name: 'hz_bs_border_width', value: hz_bs_border_width });
			}
			if (typeof hz_bs_border_radius !== 'undefined') {
				wpData.push({ name: 'hz_bs_border_radius', value: hz_bs_border_radius });
			}
			if (typeof hz_bs_padding !== 'undefined') {
				wpData.push({ name: 'hz_bs_padding', value: JSON.stringify(hz_bs_padding) });
			}
			if (typeof hz_button_background_color !== 'undefined') {
				wpData.push({ name: 'hz_button_background_color', value: hz_button_background_color });
			}
			if (typeof hz_button_text_color !== 'undefined') {
				wpData.push({ name: 'hz_button_text_color', value: hz_button_text_color });
			}
			/* End */

			$( document ).trigger( 'awsmjobs_block_load_more', [
				$listingsContainer,
				wpData,
			] );
			const listingsData = getListingsData( $listingsContainer );
			if ( listingsData.length > 0 ) {
				wpData = wpData.concat( listingsData );
			}

			// now, handle ajax
			$.ajax( {
				url: awsmJobsPublic.ajaxurl,
				data: $.param( wpData ),
				type: 'POST',
				beforeSend() {
					if ( isDefaultPagination ) {
						$triggerElem.text( awsmJobsPublic.i18n.loading_text );
					} else {
						$listingsContainer.addClass( 'awsm-jobs-loading' );
					}
				},
			} )
				.done( function ( response ) {
					if ( response.data.html ) {
						let effectDuration =
							$paginationWrapper.data( 'effectDuration' );
						$paginationWrapper.remove();
						if ( isDefaultPagination ) {
							$listingsrowContainer.append( response.data.html );
						} else {
							$listingsrowContainer.html( response.data.html );
							$listingsContainer.removeClass(
								'awsm-jobs-loading'
							);
							if ( typeof effectDuration !== 'undefined' ) {
								effectDuration = isNaN( effectDuration )
									? effectDuration
									: Number( effectDuration );
								$( 'html, body' ).animate(
									{
										scrollTop:
											$mainContainer.offset().top - 25,
									},
									effectDuration
								);
							}
						}
					} else {
						$triggerElem.remove();
					}

					$( document ).trigger( 'awsmjobs_load_more', [
						$triggerElem,
						response.data.html,
					] );
				} )
				.fail( function ( xhr ) {
					// eslint-disable-next-line no-console
					console.log( xhr );
				} );
		}
	);

	/**
	 * Handle the filters toggle button in the job listing.
	 */
	$( document ).on( 'click', '.awsm-b-filter-toggle', function ( e ) {
		e.preventDefault();
		const $elem = $( this );
		$elem.toggleClass( 'awsm-on' );
		if ( $elem.hasClass( 'awsm-on' ) ) {
			$elem.attr( 'aria-pressed', 'true' );
		} else {
			$elem.attr( 'aria-pressed', 'false' );
		}
		const $parent = $elem.parent();
		$parent.find( '.awsm-b-filter-items' ).slideToggle();
	} );

	/**
	 * Handle the responsive styles for filters in the job listing when search is enabled.
	 */
	function filtersResponsiveStylesHandler() {
		const $filtersWrap = $( '.awsm-b-filter-wrap' ).not(
			'.awsm-b-no-search-filter-wrap'
		);
		$filtersWrap.each( function () {
			const $wrapper = $( this );
			const filterFirstTop = $wrapper
				.find( '.awsm-b-filter-item' )
				.first()
				.offset().top;
			const filterLastTop = $wrapper
				.find( '.awsm-b-filter-item' )
				.last()
				.offset().top;
			if ( window.innerWidth < 768 ) {
				$wrapper.removeClass( 'awsm-b-full-width-search-filter-wrap' );
				return;
			}
			if ( filterLastTop > filterFirstTop ) {
				$wrapper.addClass( 'awsm-b-full-width-search-filter-wrap' );
			}
		} );
	}

	if (
		$( '.awsm-b-filter-wrap' ).not( '.awsm-b-no-search-filter-wrap' )
			.length > 0
	) {
		filtersResponsiveStylesHandler();
		$( window ).on( 'resize', filtersResponsiveStylesHandler );
	}
} );
