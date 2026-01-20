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
			parsedListingsAttrs
		] );

		const dataAttrs = $wrapper.data();
		$.each( dataAttrs, function( dataAttr, value ) {
			if ( $.inArray( dataAttr, parsedListingsAttrs ) !== -1 ) {
				data.push({
					name: dataAttr,
					value
				});
			}
		});

		return data;
	}

	function awsmJobFilters( $rootWrapper ) {
		const $wrapper = $rootWrapper.find( wrapperSelector );
		const $rowWrapper = $wrapper.find( sectionSelector );
		const $filterForm = $rootWrapper.find( filterSelector + ' form' );
		let formData = [];

		let formMethod = 'POST';

		if ( $filterForm.length > 0 ) {
			formData = $filterForm.serializeArray();
			formMethod = $filterForm.attr( 'method' )
				? $filterForm.attr( 'method' ).toUpperCase()
				: 'POST';
		} else {
			formData.push({ name: 'action', value: 'block_jobfilter' });
		}

		/* ========================
		Wrapper data
		======================== */
		const listings = $wrapper.data('listings');
		const specs = $wrapper.data('specs');
		const layout = $wrapper.data('awsm-layout');
		const hide_expired_jobs = $wrapper.data('awsm-hide-expired-jobs');
		let selected_terms = $wrapper.data('awsm-selected-terms');
		const other_options = $wrapper.data('awsm-other-options');
		const listings_total = $wrapper.data('awsm-listings-total');
		const spec_icons = $wrapper.data('awsm-spec-icons');

		/* ========================
		Style variables
		======================== */
		const styleFields = {
			hz_sf_border_color: $wrapper.data('hz_sf_border_color'),
			hz_sf_border_width: $wrapper.data('hz_sf_border_width'),
			hz_sf_padding: $wrapper.data('hz_sf_padding'),
			hz_sf_border_radius: $wrapper.data('hz_sf_border_radius'),
			hz_sidebar_width: $wrapper.data('hz_sidebar_width'),
			block_id: $wrapper.data('block_id'),
			hz_ls_border_color: $wrapper.data('hz_ls_border_color'),
			hz_ls_border_width: $wrapper.data('hz_ls_border_width'),
			hz_ls_border_radius: $wrapper.data('hz_ls_border_radius'),
			hz_jl_border_color: $wrapper.data('hz_jl_border_color'),
			hz_jl_border_width: $wrapper.data('hz_jl_border_width'),
			hz_jl_border_radius: $wrapper.data('hz_jl_border_radius'),
			hz_jl_padding: $wrapper.data('hz_jl_padding'),
			hz_bs_border_color: $wrapper.data('hz_bs_border_color'),
			hz_bs_border_width: $wrapper.data('hz_bs_border_width'),
			hz_bs_border_radius: $wrapper.data('hz_bs_border_radius'),
			hz_bs_padding: $wrapper.data('hz_bs_padding'),
			hz_button_background_color: $wrapper.data('hz_button_background_color'),
			hz_button_text_color: $wrapper.data('hz_button_text_color')
		};

		/* ========================
		Handle empty filters from URL
		======================== */
		$rootWrapper.find('.awsm-b-filter-item').each(function () {
			const spec = $(this).data('filter');
			const searchParams = new URLSearchParams(document.location.search);
			const queryVal = searchParams.get(spec);
			const $option = $(this).find('.awsm-b-filter-option');

			if (!$option.val() && queryVal) {
				formData.forEach(item => {
					if (item.name === $option.attr('name')) {
						item.value = '-1';
					}
				});
			}
		});

		/* ========================
		Core data
		======================== */
		formData.push({ name: 'listings_per_page', value: listings });

		if ( specs !== undefined ) {
			formData.push({ name: 'shortcode_specs', value: specs });
		}

		if ( layout !== undefined ) {
			formData.push({ name: 'awsm-layout', value: layout });
		}

		if ( selected_terms ) {
			if ( typeof selected_terms === 'string' ) {
				try {
					selected_terms = JSON.parse(selected_terms);
				} catch (e) {
					selected_terms = {};
				}
			}
			formData.push({
				name: 'awsm-selected-terms',
				value: JSON.stringify(selected_terms)
			});
		}

		if ( hide_expired_jobs !== undefined ) {
			formData.push({
				name: 'awsm-hide-expired-jobs',
				value: hide_expired_jobs
			});
		}

		if ( other_options !== undefined ) {
			formData.push({
				name: 'awsm-other-options',
				value: other_options
			});
		}

		if ( listings_total !== undefined ) {
			formData.push({
				name: 'awsm-listings-total',
				value: listings_total
			});
		}

		if ( spec_icons !== undefined ) {
			formData.push({
				name: 'awsm-spec-icons',
				value: spec_icons
			});
		}

		/* ========================
		Style data (FIXED)
		======================== */
		Object.keys(styleFields).forEach(key => {
			const val = styleFields[key];
			if ( val === undefined ) return;

			// stringify objects safely
			const value =
				typeof val === 'object'
					? JSON.stringify(val)
					: val;

			formData.push({ name: key, value });
		});

		/* ========================
		REMOVE EMPTY VALUES (IMPORTANT)
		======================== */
		formData = formData.filter(item => item.value !== '');

		/* ========================
		External hook
		======================== */
		$( document ).trigger('awsmJobBlockFiltersFormData', [
			$wrapper,
			formData
		]);

		if ( !triggerFilter ) return;
		triggerFilter = false;

		const actionUrl =
			$filterForm.length > 0
				? $filterForm.attr('action')
				: awsmJobsPublic.ajaxurl;

		$.ajax({
			url: actionUrl,
			type: formMethod,
			data: formData,
			beforeSend() {
				$wrapper.addClass('awsm-b-jobs-loading');
			}
		})
		.done(response => {
			$rowWrapper.html(response.data.html);
			$( document ).trigger('awsmjobs_filtered_listings', [
				$rootWrapper,
				response.data.html
			]);
		})
		.fail(xhr => {
			console.error(xhr);
		})
		.always(() => {
			$wrapper.removeClass('awsm-b-jobs-loading');
			triggerFilter = true;
		});
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
	
	var updateQuery = function( key, value, url ) {
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

	var setPaginationBase = function( $rootWrapper, key, value ) {
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

		const isMultiple = $elem.prop('multiple');
		const allOptions = $elem.find('option');
		const firstOption = allOptions.eq(0); // "All"
		const selectedOptions = allOptions.filter(':selected');

		const allLiItems = $elem
			.closest('.awsm-b-filter-item')
			.find('ul li');

		let slugs = [];

		/* ----------------------------------------------------
		Track previous "All" state
		---------------------------------------------------- */
		const wasAllSelected = $elem.data('was-all-selected') === true;
		const isAllSelected = firstOption.is(':selected');

		// Store current state for next change
		$elem.data('was-all-selected', isAllSelected);

		/* ----------------------------------------------------
		MULTI SELECT LOGIC
		---------------------------------------------------- */
		if (isMultiple) {

			/* CASE 1: "All" was JUST UNCHECKED */
			if (wasAllSelected && !isAllSelected) {

				// HARD RESET — clears everything
				allOptions.prop('selected', false).removeClass('selected');
				allLiItems.removeClass('selected');

				slugs = [];

			/* CASE 2: "All" is checked */
			} else if (isAllSelected) {

				allOptions.prop('selected', true).addClass('selected');
				allLiItems.addClass('selected');

				slugs = allOptions.slice(1).map(function () {
					return $(this).data('slug');
				}).get().filter(Boolean);

			/* CASE 3: Individual selection */
			} else {

				allLiItems.removeClass('selected');

				selectedOptions.each(function () {
					const index = $(this).index();
					allLiItems.eq(index).addClass('selected');
				});

				slugs = selectedOptions.map(function () {
					return $(this).data('slug');
				}).get().filter(Boolean);
			}

		} else {
			// SINGLE SELECT
			slugs = selectedOptions.data('slug')
				? [selectedOptions.data('slug')]
				: [];
		}

		const slugString = slugs.length ? slugs.join(',') : '';

		/* ----------------------------------------------------
		Pagination + Filters
		---------------------------------------------------- */
		if ($('.awsm-job-listings').length > 0) {
			$rootWrapper.find('.awsm-b-job-no-more-jobs-get').hide();
		}

		setPaginationBase($rootWrapper, currentSpec, slugString);

		if (awsmJobsPublic.deep_linking.spec) {
			const $paginationBase = $rootWrapper.find(
				'input[name="awsm_pagination_base"]'
			);
			updateQuery(currentSpec, slugString, $paginationBase.val());
		}

		awsmJobFilters($rootWrapper);
	});

	$( filterSelector + ' .awsm-filter-checkbox' ).on(
		'change',
		function( e ) { 
			const selectedFilters = {};
			const slugs = []; // Initialize an array to collect slugs
			const $elem = $( this );
			const $rootWrapper = $elem.parents( rootWrapperSelector );
			const currentSpec = $elem
				.parents( '.awsm-filter-list-item' )
				.data( 'filter' );

			// Loop through checked checkboxes and build selectedFilters and slugs array
			$( '.awsm-filter-checkbox:checked' ).each( function() {
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

	/* =========================
	* Helpers (ADD ONCE)
	* ========================= */

	function addToRequest(data, name, value, stringify = false) {
		if (typeof value === 'undefined') return;

		if (stringify && typeof value === 'object') {
			try {
				value = JSON.stringify(value);
			} catch (e) {
				return;
			}
		}

		data.push({ name, value });
	}

	function normalizeRequestData(data) {
		const map = {};

		data.forEach(item => {
			let value = item.value;

			if (typeof value === 'object') {
				try {
					value = JSON.stringify(value);
				} catch (e) {
					value = '';
				}
			}

			map[item.name] = value; // last value wins
		});

		return Object.keys(map).map(key => ({
			name: key,
			value: map[key]
		}));
	}

	/* =========================
	* Pagination / Load More
	* ========================= */

	$(wrapperSelector).on(
		'click',
		'.awsm-b-jobs-pagination .awsm-b-load-more-btn, .awsm-b-jobs-pagination a.page-numbers',
		function (e) {
			e.preventDefault();

			const $triggerElem = $(this);
			const isDefaultPagination = $triggerElem.hasClass('awsm-b-load-more-btn');
			let paged = 1;
			let wpData = [];

			const $mainContainer = $triggerElem.parents(rootWrapperSelector);
			const $listingsContainer = $mainContainer.find(wrapperSelector);
			const $listingsrowContainer = $listingsContainer.find(sectionSelector);
			const $paginationWrapper = $triggerElem.parents('.awsm-b-jobs-pagination');

			const listings = $listingsContainer.data('listings');
			const totalPosts = $listingsContainer.data('total-posts');
			const specs = $listingsContainer.data('specs');
			const lang = $listingsContainer.data('lang');
			const searchQuery = $listingsContainer.data('search');

			/* block data */
			const layout = $listingsContainer.data('awsm-layout');
			const hide_expired_jobs = $listingsContainer.data('awsm-hide-expired-jobs');
			let selected_terms = $listingsContainer.data('awsm-selected-terms');
			const other_options = $listingsContainer.data('awsm-other-options');
			const spec_icons = $listingsContainer.data('awsm-spec-icons');
			/* style data */
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

			if (isDefaultPagination) {
				$triggerElem.prop('disabled', true);
				paged = $triggerElem.data('page') || 1;
			} else {
				$triggerElem
					.parents('.page-numbers')
					.find('.page-numbers')
					.removeClass('current')
					.removeAttr('aria-current');

				$triggerElem.addClass('current').attr('aria-current', 'page');
			}

			$paginationWrapper.addClass('awsm-b-jobs-pagination-loading');

			/* =========================
			* Filters
			* ========================= */

			const $filterForm = $mainContainer.find(filterSelector + ' form');

			if (filterCheck($filterForm)) {
				wpData = $filterForm.find('.awsm-b-filter-option').serializeArray();
			}

			const specsList = {};

			$filterForm.find('.awsm-filter-checkbox:checked').each(function () {
				const $checkbox = $(this);
				const taxonomy = $checkbox.data('taxonomy');
				const termId = $checkbox.data('term-id');

				if (taxonomy && termId) {
					if (!specsList[taxonomy]) {
						specsList[taxonomy] = [];
					}
					specsList[taxonomy].push(termId);
				}
			});

			for (const taxonomy in specsList) {
				specsList[taxonomy].forEach(termId => {
					wpData.push({
						name: `awsm_job_specs_list[${taxonomy}][]`,
						value: termId
					});
				});
			}

			/* =========================
			* Pagination Base
			* ========================= */

			if (!isDefaultPagination) {
				let paginationBaseURL = $triggerElem.attr('href');
				const parts = paginationBaseURL.split('?');
				let queryString = '';

				if (parts[1]) {
					const params = new URLSearchParams(parts[1]);
					paged = params.get('paged');
					params.delete('paged');
					if (params.toString()) {
						queryString = '?' + params.toString();
					}
				}

				addToRequest(wpData, 'awsm_pagination_base', parts[0] + queryString);

				if (awsmJobsPublic.deep_linking.pagination) {
					updateQuery('paged', paged, parts[0] + queryString);
				}
			}

			/* =========================
			* Base Required Params
			* ========================= */

			addToRequest(wpData, 'action', 'block_loadmore');
			addToRequest(wpData, 'paged', paged);
			addToRequest(wpData, 'listings_per_page', listings);
			addToRequest(wpData, 'shortcode_specs', specs);
			addToRequest(wpData, 'lang', lang);
			addToRequest(wpData, 'jq', searchQuery);

			/* =========================
			* Block + Style Params
			* ========================= */

			addToRequest(wpData, 'awsm-layout', layout);
			addToRequest(wpData, 'awsm-hide-expired-jobs', hide_expired_jobs);
			addToRequest(wpData, 'awsm-other-options', other_options);
			addToRequest(wpData, 'block_id', block_id);
			addToRequest(wpData, 'awsm-selected-terms', selected_terms, true);
			addToRequest(wpData, 'awsm-spec-icons', spec_icons);

			addToRequest(wpData, 'hz_sf_border_color', hz_sf_border_color);
			addToRequest(wpData, 'hz_sf_border_width', hz_sf_border_width);
			addToRequest(wpData, 'hz_sf_padding', hz_sf_padding, true);
			addToRequest(wpData, 'hz_sf_border_radius', hz_sf_border_radius, true);
			addToRequest(wpData, 'hz_sidebar_width', hz_sidebar_width);

			addToRequest(wpData, 'hz_ls_border_color', hz_ls_border_color);
			addToRequest(wpData, 'hz_ls_border_width', hz_ls_border_width);
			addToRequest(wpData, 'hz_ls_border_radius', hz_ls_border_radius, true);

			addToRequest(wpData, 'hz_jl_border_color', hz_jl_border_color);
			addToRequest(wpData, 'hz_jl_border_width', hz_jl_border_width);
			addToRequest(wpData, 'hz_jl_border_radius', hz_jl_border_radius, true);
			addToRequest(wpData, 'hz_jl_padding', hz_jl_padding, true);

			addToRequest(wpData, 'hz_bs_border_color', hz_bs_border_color);
			addToRequest(wpData, 'hz_bs_border_width', hz_bs_border_width);
			addToRequest(wpData, 'hz_bs_border_radius', hz_bs_border_radius, true);
			addToRequest(wpData, 'hz_bs_padding', hz_bs_padding, true);

			addToRequest(wpData, 'hz_button_background_color', hz_button_background_color);
			addToRequest(wpData, 'hz_button_text_color', hz_button_text_color);

			/* =========================
			* External Listings Data
			* ========================= */

			const listingsData = getListingsData($listingsContainer);
			if (listingsData.length) {
				wpData = wpData.concat(listingsData);
			}

			/* FINAL */
			wpData = normalizeRequestData(wpData);

			/* =========================
			* AJAX
			* ========================= */

			$.ajax({
				url: awsmJobsPublic.ajaxurl,
				type: 'POST',
				data: $.param(wpData),
				beforeSend() {
					if (isDefaultPagination) {
						$triggerElem.text(awsmJobsPublic.i18n.loading_text);
					} else {
						$listingsContainer.addClass('awsm-b-jobs-loading');
					}
				}
			})
				.done(function (response) {
					if (response.data && response.data.html) {
						$paginationWrapper.remove();

						if (isDefaultPagination) {
							$listingsrowContainer.append(response.data.html);
						} else {
							$listingsrowContainer.html(response.data.html);
							$listingsContainer.removeClass('awsm-b-jobs-loading');
						}
					} else {
						$triggerElem.remove();
					}
				})
				.fail(function (xhr) {
					console.log(xhr);
				});
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
