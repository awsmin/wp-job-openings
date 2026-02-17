'use strict';

jQuery( function( $ ) { 
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
			'termId'
		];

		/* added for block */
		parsedListingsAttrs.push( 'awsm-layout' );
		parsedListingsAttrs.push( 'awsm-hide-expired-jobs' );
		parsedListingsAttrs.push( 'awsm-other-options' );
		parsedListingsAttrs.push( 'awsm-listings-total' );
		parsedListingsAttrs.push( 'awsm-selected-terms' );
		parsedListingsAttrs.push( 'awsm-spec-icons' );
		/* end */

		$( document ).trigger( 'awsmJobBlockListingsData', [
			parsedListingsAttrs
		] );

		const dataAttrs = $wrapper.data();
		$.each( dataAttrs, function( dataAttr, value ) {
			if ( $.inArray( dataAttr, parsedListingsAttrs ) === -1 ) {
				data.push( {
					name: dataAttr,
					value
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

			formData = $filterForm.serializeArray();
			var formMethod = $filterForm.attr( 'method' ) ?
				$filterForm.attr( 'method' ).toUpperCase() :
				'POST';

		} else {

			formData.push( { name: 'action', value: 'block_jobfilter' } );
			var formMethod = 'POST';
		}

		const listings 			= $wrapper.data( 'listings' );
		const specs 			= $wrapper.data( 'specs' );
		const layout 			= $wrapper.data( 'awsm-layout' );
		const hide_expired_jobs = $wrapper.data( 'awsm-hide-expired-jobs' );
		let selected_terms 		= $wrapper.data( 'awsm-selected-terms' );
		const other_options 	= $wrapper.data( 'awsm-other-options' );
		const listings_total 	= $wrapper.data( 'awsm-listings-total' );
		const show_spec_icon 	= $wrapper.data( 'awsm-spec-icons' );

		/* Filter URL sync logic */
		$rootWrapper.find('.awsm-b-filter-item').each(function() {
			var currentLoopSpec = $(this).data('filter');
			var searchParams = new URLSearchParams(document.location.search);
			var currentSpecQueryVal = searchParams.get(currentLoopSpec);
			var $currentOption = $(this).find('.awsm-b-filter-option');

			if ($currentOption.val().length === 0 && currentSpecQueryVal && currentSpecQueryVal.length > 0) {
				formData.forEach(function(item) {
					if (item.name === $currentOption.attr('name')) {
						item.value = '-1';
					}
				});
			}
		});

		/* Core parameters */
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
					console.error('Failed to parse selected_terms JSON:', error);
					selected_terms = {};
				}
			}
			formData.push( {
				name: 'awsm-selected-terms',
				value: JSON.stringify( selected_terms )
			} );
		}

		if ( typeof hide_expired_jobs !== 'undefined' ) {
			formData.push({
				name: 'awsm-hide-expired-jobs',
				value: hide_expired_jobs
			});
		}

		if ( typeof other_options !== 'undefined' ) {
			formData.push({
				name: 'awsm-other-options',
				value: other_options
			});
		}

		if ( typeof listings_total !== 'undefined' ) {
			formData.push({
				name: 'awsm-listings-total',
				value: listings_total
			});
		}

		if ( typeof show_spec_icon !== 'undefined' ) {
			formData.push({
				name: 'awsm-spec-icons',
				value: show_spec_icon
			});
		}

		const listingsData = getListingsData( $wrapper );
		if ( listingsData.length > 0 ) {
			// optional merge
		}

		$( document ).trigger( 'awsmJobBlockFiltersFormData', [
			$wrapper,
			formData
		] );

		if ( triggerFilter ) {

			triggerFilter = false;

			const actionUrl =
				$filterForm.length > 0 ?
					$filterForm.attr( 'action' ) :
					awsmJobsPublic.ajaxurl;

			$.ajax({
				url: actionUrl,
				beforeSend() { 
					$wrapper.addClass( 'awsm-b-jobs-loading' );
				},
				data: formData,
				type: formMethod
			})
			.done(function( response ) {

				$rowWrapper.html( response.data.html );

				const $searchControl = $rootWrapper.find( '.awsm-b-job-search' );

				if ( $searchControl.length > 0 ) {
					if ( $searchControl.val().length > 0 ) {
						$rootWrapper.find( '.awsm-b-job-search-btn' )
							.addClass( 'awsm-b-job-hide' );
						$rootWrapper.find( '.awsm-b-job-search-close-btn' )
							.removeClass( 'awsm-b-job-hide' );
					} else {
						$rootWrapper.find( '.awsm-b-job-search-btn' )
							.removeClass( 'awsm-b-job-hide' );
						$rootWrapper.find( '.awsm-b-job-search-close-btn' )
							.addClass( 'awsm-job-hide' );
					}
				}

				$( document ).trigger( 'awsmjobs_filtered_listings', [
					$rootWrapper,
					response.data.html
				]);

			})
			.fail(function( xhr ) {
				console.log( xhr );
			})
			.always(function() {
				$wrapper.removeClass( 'awsm-b-jobs-loading' );
				triggerFilter = true;
			});
		}
	}

	function filterCheck( $filterForm ) {
		let check = false;
		if ( $filterForm.length > 0 ) {
			const $filterOption = $filterForm.find( '.awsm-b-filter-option' );
			$filterOption.each( function() {
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

	var updateQuery = function(key, value, url) {
		url = typeof url !== 'undefined' ? url : currentUrl;
		url = url.split('?')[0];
		var searchParams = new URLSearchParams(document.location.search);
		if (searchParams.has('paged')) {
			searchParams.delete('paged');
		}
		if (searchParams.has('page')) {
			searchParams.delete('page');
		}
		value = value !== undefined && value !== null ? String(value) : '';

		if (value !== '') {
			searchParams.set(key, value);
		} else {
			searchParams.delete(key);
		}
		var modQueryString = searchParams.toString();
		if (modQueryString.length > 0) {
			modQueryString = '?' + modQueryString;
		}
		window.history.replaceState({}, '', url + modQueryString);
	};

	var setPaginationBase = function($rootWrapper, key, value) {
		var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
		if ($paginationBase.length > 0) {
			var splittedURL = $paginationBase.val().split('?');
			var queryString = '';
			if (splittedURL.length > 1) {
				queryString = splittedURL[1];
			}
			var searchParams = new URLSearchParams(queryString);
			if (value.length > 0) {
				searchParams.set(key, value);
			} else {
				searchParams.delete(key);
			}
			$paginationBase.val(splittedURL[0] + '?' + searchParams.toString());
			$rootWrapper.find('input[name="paged"]').val(1);
		}
	};

	if ( $( '.awsm-b-job-no-more-jobs-get' ).length > 0 ) {
		$( '.awsm-b-job-listings' ).hide();
		$( '.awsm-b-job-no-more-jobs-get' ).slice( 1 ).hide();
	}

	$(filterSelector + ' .awsm-b-filter-option').selectric({
		multiple: { keepMenuOpen: true },
		onInit: function(select, selectric) { 
			const $select = $(select);
			const id = select.id;

			if (selectric && selectric.elements && selectric.elements.input) {
				const $input = $(selectric.elements.input);
				$(select).attr('id', 'selectric-' + id);
				$input.attr('id', id);
			}

			const $rootWrapper = $select.closest(rootWrapperSelector);
			const currentSpec  = $select.closest('.awsm-b-filter-item').data('filter');

			setTimeout(function() {
				// Sync "All" checkbox for UI only
				syncAllOptionFromUrl($select);
				forceAllLabel($select);

				// Get page and existing URL filter
				const urlParams = new URLSearchParams(window.location.search);
				const currentPage = parseInt(urlParams.get('paged') || urlParams.get('page') || 1);
				const existingFilter = urlParams.get(currentSpec);

				const selectedValues = $select.val(); // array for multi-select, string for single
				if (selectedValues && selectedValues.length > 0) {
					let slugString = Array.isArray(selectedValues) ? selectedValues.join(',') : selectedValues;

					// Only write to URL if we are on first page and no existing filter in URL
					if (slugString !== 'All' && !existingFilter && currentPage <= 1) {
						setPaginationBase($rootWrapper, currentSpec, slugString);
						updateAwsmQuery($rootWrapper, currentSpec, slugString);
					}
				}

				// Only trigger AJAX if not paged and no filter in URL
				if (!existingFilter && currentPage <= 1) {
					handleAwsmMultiFilter($select); // triggers AJAX
				}
			}, 0);
		},
		arrowButtonMarkup: '<span class="awsm-selectric-arrow-drop">&#x25be;</span>',
		customClass: { prefix: 'awsm-selectric', camelCase: false },
		onChange: function(element) {
			const $select = $(element);
			handleAwsmMultiFilter($select);

			setTimeout(function() {
				forceAllLabel($select);
			}, 0);

			if ($select.prop('multiple')) $select.selectric('open');
		}
	});

	function handleAwsmMultiFilter($select) {
		const $options = $select.find('option');
		const $all     = $options.eq(0);        // "All"
		const $others  = $options.slice(1);     // Individual options

		const $rootWrapper = $select.closest(rootWrapperSelector);
		const currentSpec  = $select.closest('.awsm-b-filter-item').data('filter');

		let slugs = [];

		// CURRENT state
		const isAllSelected       = $all.is(':selected');
		const selectedOthersCount = $others.filter(':selected').length;
		const totalOthersCount    = $others.length;

		// PREVIOUS state
		const wasAllSelected = $select.data('wasAllSelected') === true;

		/* =================================================
		SINGLE SELECT DROPDOWN
		================================================= */
		if (! $select.prop('multiple')) {

			if (isAllSelected) {
				$options.prop('selected', false);
				$all.prop('selected', true);

				setPaginationBase($rootWrapper, currentSpec, '');
				updateAwsmQuery($rootWrapper, currentSpec, '');
				awsmJobFilters($rootWrapper);

				$select.data('wasAllSelected', true);
				$select.selectric('refresh');
				return;
			}

			// Single selection (not All)
			const selectedSlug = $options.filter(':selected').data('slug') || '';
			setPaginationBase($rootWrapper, currentSpec, selectedSlug);
			updateAwsmQuery($rootWrapper, currentSpec, selectedSlug);
			awsmJobFilters($rootWrapper);

			$select.data('wasAllSelected', false);
			$select.selectric('refresh');
			return;
		}

		/* =================================================
		MULTI SELECT DROPDOWN
		================================================= */
		// CASE 1: User UNCHECKED "All" → Clear everything
		if (wasAllSelected && ! isAllSelected) {
			$options.prop('selected', false);
			$select.selectric('refresh');

			setPaginationBase($rootWrapper, currentSpec, '');
			updateAwsmQuery($rootWrapper, currentSpec, '');
			awsmJobFilters($rootWrapper);

			$select.data('wasAllSelected', false);
			return;
		}

		// CASE 2: User CLICKED "All" → Select everything
		if (isAllSelected && ! wasAllSelected) {
			$options.prop('selected', true);
			slugs = $others.map(function() {
				return $(this).data('slug');
			}).get();
		}

		// CASE 3: User selected all individuals manually → Auto-check All
		else if (! isAllSelected && selectedOthersCount === totalOthersCount) {
			$all.prop('selected', true);
			slugs = $others.map(function() {
				return $(this).data('slug');
			}).get();
		}

		// CASE 4: Normal individual selection
		else if (selectedOthersCount > 0) {
			$all.prop('selected', false);
			slugs = $others.filter(':selected').map(function() {
				return $(this).data('slug');
			}).get();
		}

		// CASE 5: Nothing selected → Reset
		else {
			$options.prop('selected', false);
			$select.selectric('refresh');

			setPaginationBase($rootWrapper, currentSpec, '');
			updateAwsmQuery($rootWrapper, currentSpec, '');
			awsmJobFilters($rootWrapper);

			$select.data('wasAllSelected', false);
			return;
		}

		// Save state
		$select.data('wasAllSelected', $all.is(':selected'));

		// Sync Selectric UI
		$select.selectric('refresh');

		// Apply filters
		const slugString = slugs.join(',');
		setPaginationBase($rootWrapper, currentSpec, slugString);
		updateAwsmQuery($rootWrapper, currentSpec, slugString);
		awsmJobFilters($rootWrapper);
	}

	function syncAllOptionFromUrl($select) {
		const $options = $select.find('option');
		const $all     = $options.eq(0);
		const $others  = $options.slice(1);

		const totalOthers = $others.length;
		const selectedOthers = $others.filter(':selected').length;

		if (totalOthers > 0 && selectedOthers === totalOthers) {
			$all.prop('selected', true);
		} else {
			$all.prop('selected', false);
		}

		$select.selectric('refresh');
		$select.data('wasAllSelected', $all.is(':selected'));
	}

	function forceAllLabel($select) {
		const selectric = $select.data('selectric');
		const $allOption = $select.find('option').first();

		if (selectric && $allOption.is(':selected')) {
			selectric.elements.label.text($allOption.text());
		}
	}

	function updateAwsmQuery($rootWrapper, spec, value) {
		if (! awsmJobsPublic.deep_linking.spec) {
			return;
		}
		const $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
		updateQuery(spec, value, $paginationBase.val());
	}

	$( filterSelector + ' .awsm-b-job-search-btn' ).on( 'click', function() {
		searchJobs( $( this ) );
	} );

	$( filterSelector + ' .awsm-b-job-search-close-btn' ).on(
		'click',
		function() {
			const $elem = $( this );
			$elem
				.parents( rootWrapperSelector )
				.find( '.awsm-b-job-search' )
				.val( '' );
			searchJobs( $elem );
		}
	);

	$( filterSelector + ' .awsm-b-job-search' ).on( 'keypress', function( e ) {
		if ( e.which == 13 ) {
			e.preventDefault();
			searchJobs( $( this ) );
		}
	} );

	/* ========== Job Listings Load More ========== */
	$( wrapperSelector ).on(
		'click',
		'.awsm-b-jobs-pagination .awsm-b-load-more-btn, .awsm-b-jobs-pagination a.page-numbers',
		function( e ) {
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
				.each( function() {
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
					specsList[ taxonomy ].forEach( function( termId ) {
						wpData.push( {
							name: `awsm_job_specs_list[${ taxonomy }][]`, // Add taxonomy as part of the key
							value: termId
						} );
					} );
				}
			}

			if (! isDefaultPagination) {
				var paginationBaseURL = $triggerElem.attr('href');
				var splittedURL = paginationBaseURL.split('?');
				var queryString = '';
				var isHomepage = window.awsmJobsPublic && awsmJobsPublic.is_homepage;
				var pageKey = isHomepage ? 'page' : 'paged';

				if (splittedURL.length > 1) {
					var searchParams = new URLSearchParams(splittedURL[1]);
					paged = searchParams.get(pageKey) || searchParams.get(pageKey === 'page' ? 'paged' : 'page');

					if (!paged) {
						paged = 1;
					}
					
					searchParams.delete('page');
					searchParams.delete('paged');
					
					if (searchParams.toString().length > 0) {
						queryString = '?' + searchParams.toString();
					}
				}else {
					var pageMatch = paginationBaseURL.match(/\/page\/(\d+)\/?/);
					if (pageMatch) {
						paged = pageMatch[1];
					} else {
						paged = 1;
					}
				}

				paginationBaseURL = splittedURL[0] + queryString;
				wpData.push({
					name: 'awsm_pagination_base',
					value: splittedURL[0] + queryString
				});
				if (awsmJobsPublic.deep_linking.pagination) {
					updateQuery(pageKey, paged, paginationBaseURL);
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
						value: termId
					} );
				}
			}

			wpData.push(
				{
					name: 'action',
					value: 'block_loadmore'
				},
				{
					name: 'paged',
					value: paged
				}
			);
			if ( typeof listings !== 'undefined' ) {
				wpData.push( {
					name: 'listings_per_page',
					value: listings
				} );
			}
			if ( typeof specs !== 'undefined' ) {
				wpData.push( {
					name: 'shortcode_specs',
					value: specs
				} );
			}

			/* added for block */
			if ( typeof layout !== 'undefined' ) {
				wpData.push( {
					name: 'awsm-layout',
					value: layout
				} );
			}
			if ( typeof hide_expired_jobs !== 'undefined' ) {
				wpData.push( {
					name: 'awsm-hide-expired-jobs',
					value: hide_expired_jobs
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
					value: JSON.stringify( selected_terms ) // Send as JSON string
				} );
			}

			if ( typeof other_options !== 'undefined' ) {
				wpData.push( {
					name: 'awsm-other-options',
					value: other_options
				} );
			}
			if ( typeof listings_total !== 'undefined' ) {
				wpData.push( {
					name: 'awsm-listings-total',
					value: listings_total
				} );
			}

			if ( typeof show_spec_icon !== 'undefined' ) {
				wpData.push( {
					name: 'awsm-spec-icons',
					value: show_spec_icon
				} );
			}

			if ( typeof lang !== 'undefined' ) {
				wpData.push( {
					name: 'lang',
					value: lang
				} );
			}

			if ( typeof searchQuery !== 'undefined' ) {
				wpData.push( {
					name: 'jq',
					value: searchQuery
				} );
			}
			
			$( document ).trigger( 'awsmjobs_block_load_more', [
				$listingsContainer,
				wpData
			] );
			const listingsData = getListingsData( $listingsContainer );
			if ( listingsData.length > 0 ) {
				//wpData = wpData.concat( listingsData );
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
						$listingsContainer.addClass( 'awsm-b-jobs-loading' );
					}
				}
			} )
				.done( function( response ) {
					if ( response.data.html ) {
						let effectDuration =
							$paginationWrapper.data( 'effectDuration' );
						$paginationWrapper.remove();
						if ( isDefaultPagination ) {
							$listingsrowContainer.append( response.data.html );
						} else {
							$listingsrowContainer.html( response.data.html );
							$listingsContainer.removeClass(
								'awsm-b-jobs-loading'
							);
							if ( typeof effectDuration !== 'undefined' ) {
								effectDuration = isNaN( effectDuration ) ?
									effectDuration :
									Number( effectDuration );
								$( 'html, body' ).animate(
									{
										scrollTop:
											$mainContainer.offset().top - 25
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
						response.data.html
					] );
				} )
				.fail( function( xhr ) {
					// eslint-disable-next-line no-console
					console.log( xhr );
				} );
		}
	);

	/**
	 * Handle the filters toggle button in the job listing.
	 */
	$( document ).on( 'click', '.awsm-b-filter-toggle', function( e ) {
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
		$filtersWrap.each( function() {
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
