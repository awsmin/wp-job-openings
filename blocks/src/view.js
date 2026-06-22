import $ from "jquery";

( "use strict" );

jQuery( function ( $ ) {
	const rootWrapperSelector = ".awsm-b-job-wrap";
	const wrapperSelector = ".awsm-b-job-listings";
	const sectionSelector = ".awsm-b-job-listing-items";

	/* ========== Job Search and Filtering ========== */

	const filterSelector = ".awsm-b-filter-wrap";
	const currentUrl =
		window.location.protocol +
		"//" +
		window.location.host +
		window.location.pathname;

	function getListingsData( $wrapper ) {
		const data = [];
		const seen = {};

		const normalizeKey = key => {
			const strKey = String( key );

			// Only normalize AWSM block keys; keep everything else as-is (e.g. `termId`).
			if ( strKey.indexOf( "awsm" ) !== 0 ) {
				return strKey;
			}

			// jQuery `.data()` turns `data-awsm-layout` into `awsmLayout`; convert back.
			if ( /[A-Z]/.test( strKey ) ) {
				return strKey.replace( /([a-z0-9])([A-Z])/g, "$1-$2" ).toLowerCase();
			}

			return strKey;
		};

		const parsedListingsAttrs = [
			"listings",
			"search",
			"lang",
			"taxonomy",
			"termId",
			// Prevent wrapper state from overriding computed pagination.
			"page",
			"paged",

			// block attrs (ONLY dashed)
			"awsm-layout",
			"awsm-hide-expired-jobs",
			"awsm-other-options",
			"awsm-selected-terms",
			"awsm-spec-icons",
			"awsm-order-by",
			"awsm-button-style"  // handled via explicit push below
		];

		// Allow Pro to extend
		$( document ).trigger( "awsmJobBlockListingsData", [
			parsedListingsAttrs
		] );

		// Compare on normalized keys so `awsmLayout` and `awsm-layout` behave the same.
		const normalizedParsedKeys = parsedListingsAttrs.map( normalizeKey );

		const dataAttrs = $wrapper.data();

		$.each( dataAttrs, function ( dataAttr, value ) {
			const normalizedKey = normalizeKey( dataAttr );

			if (
				$.inArray( normalizedKey, normalizedParsedKeys ) === -1 &&
				! seen[ normalizedKey ]
			) {
				seen[ normalizedKey ] = true;
				data.push( {
					name: normalizedKey,
					value
				} );
			}
		} );

		return data;
	}

	// Normalize AWSM camelCase params to dashed, then de-dupe scalar keys.
	// Keeps bracket params (e.g. `awsm_job_spec[tax][]`) untouched to preserve multi-values.
	function normalizeAndDedupeParams( params ) {
		const out = [];
		const indexByName = {};

		( params || [] ).forEach( function ( item ) {
			if ( ! item || typeof item.name === "undefined" ) {
				return;
			}

			const origName = String( item.name );
			const isBracket = origName.indexOf( "[" ) !== -1;

			let name = origName;
			// Only normalize AWSM keys; keep others as-is (e.g. `termId`).
			if (
				! isBracket &&
				name.indexOf( "awsm" ) === 0 &&
				/[A-Z]/.test( name )
			) {
				name = name.replace( /([a-z0-9])([A-Z])/g, "$1-$2" ).toLowerCase();
			}

			const normalizedItem =
				name === origName ? item : {name, value: item.value};

			if ( isBracket ) {
				out.push( normalizedItem );
				return;
			}

			if ( typeof indexByName[ name ] !== "undefined" ) {
				out[ indexByName[ name ] ] = normalizedItem; // keep last
				return;
			}

			indexByName[ name ] = out.length;
			out.push( normalizedItem );
		} );

		return out;
	}

	function awsmJobFilters( $rootWrapper ) {
		const $wrapper = $rootWrapper.find( wrapperSelector );
		const $rowWrapper = $wrapper.find( sectionSelector );
		const $filterForm = $rootWrapper.find( filterSelector + " form" );
		let formData = [];

		if ( $filterForm.length > 0 ) {
			formData = $filterForm.serializeArray();
			var formMethod = $filterForm.attr( "method" )
				? $filterForm.attr( "method" ).toUpperCase()
				: "POST";
		} else {
			formData.push( {name: "action", value: "block_jobfilter"} );
			var formMethod = "POST";
		}

		const listings = $wrapper.data( "listings" );
		const layout = $wrapper.data( "awsm-layout" );
		const hide_expired_jobs = $wrapper.data( "awsm-hide-expired-jobs" );
		const other_options = $wrapper.data( "awsm-other-options" );
		const show_spec_icon = $wrapper.data( "awsm-spec-icons" );
		const order_by = $wrapper.data( "awsm-order-by" );
		const filter_items_order = $wrapper.data( "awsm-filter-items-order" );
		const button_style = $wrapper.data( "awsm-button-style" );
		const button_text = $wrapper.data( "awsm-button-text" );

		/* Filter URL sync logic */
		$rootWrapper.find( ".awsm-b-filter-item" ).each( function () {
			const currentLoopSpec = $( this ).data( "filter" );
			const searchParams = new URLSearchParams( document.location.search );
			const currentSpecQueryVal = searchParams.get( currentLoopSpec );
			const $currentOption = $( this ).find( ".awsm-b-filter-option" );

			if (
				$currentOption.val().length === 0 &&
				currentSpecQueryVal &&
				currentSpecQueryVal.length > 0
			) {
				formData.forEach( function ( item ) {
					if ( item.name === $currentOption.attr( "name" ) ) {
						item.value = "-1";
					}
				} );
			}
		} );

		/* Core parameters */
		formData.push( {name: "listings_per_page", value: listings} );

		if ( typeof layout !== "undefined" ) {
			formData.push( {name: "awsm-layout", value: layout} );
		}

		if ( typeof hide_expired_jobs !== "undefined" ) {
			formData.push( {
				name: "awsm-hide-expired-jobs",
				value: hide_expired_jobs
			} );
		}

		if ( typeof other_options !== "undefined" ) {
			formData.push( {
				name: "awsm-other-options",
				value: other_options
			} );
		}

		if ( typeof show_spec_icon !== "undefined" ) {
			formData.push( {
				name: "awsm-spec-icons",
				value: show_spec_icon
			} );
		}

		if ( typeof order_by !== "undefined" ) {
			formData.push( {
				name: "awsm-order-by",
				value: order_by
			} );
		}

		if ( typeof filter_items_order !== "undefined" ) {
			formData.push( {
				name: "awsm-filter-items-order",
				value: filter_items_order
			} );
		}

		if ( typeof button_style !== "undefined" ) {
			formData.push( {
				name: "awsm-button-style",
				value: button_style
			} );
		}

		if ( button_text ) {
			formData.push( {
				name: "awsm-button-text",
				value: button_text
			} );
		}

		const listingsData = getListingsData( $wrapper );
		if ( listingsData.length > 0 ) {
			formData = formData.concat( listingsData );
		}

		if ( awsmJobsPublic.block_nonce ) {
			formData.push( {
				name: "awsm_block_nonce",
				value: awsmJobsPublic.block_nonce
			} );
		}

		$( document ).trigger( "awsmJobBlockFiltersFormData", [
			$wrapper,
			formData
		] );
		formData = normalizeAndDedupeParams( formData );

		if ( ! $wrapper.data( "awsmFilterBusy" ) ) {
			$wrapper.data( "awsmFilterBusy", true );

			const actionUrl =
				$filterForm.length > 0
					? $filterForm.attr( "action" )
					: awsmJobsPublic.ajaxurl;

			$.ajax( {
				url: actionUrl,
				beforeSend() {
					$wrapper.addClass( "awsm-b-jobs-loading" );
				},
				data: formData,
				type: formMethod
			} )
				.done( function ( response ) {
					$rowWrapper.html( response.data.html );

					$wrapper.find( ".awsm-b-jobs-pagination" ).remove();
					if ( response.data.pagination_html ) {
						$rowWrapper.after( response.data.pagination_html );
					}

					const $searchControl = $rootWrapper.find( ".awsm-b-job-search" );

					if ( $searchControl.length > 0 ) {
						if ( $searchControl.val().length > 0 ) {
							$rootWrapper
								.find( ".awsm-b-job-search-btn" )
								.addClass( "awsm-b-job-hide" );
							$rootWrapper
								.find( ".awsm-b-job-search-close-btn" )
								.removeClass( "awsm-b-job-hide" );
						} else {
							$rootWrapper
								.find( ".awsm-b-job-search-btn" )
								.removeClass( "awsm-b-job-hide" );
							$rootWrapper
								.find( ".awsm-b-job-search-close-btn" )
								.addClass( "awsm-b-job-hide" );
						}
					}

					$( document ).trigger( "awsmjobs_filtered_listings", [
						$rootWrapper,
						response.data.html
					] );
				} )
				.fail( function ( xhr ) {
					console.log( xhr );
				} )
				.always( function () {
					$wrapper.removeClass( "awsm-b-jobs-loading" );
					$wrapper.data( "awsmFilterBusy", false );
				} );
		}
	}

	function filterCheck( $filterForm ) {
		let check = false;
		if ( $filterForm.length > 0 ) {
			const $filterOption = $filterForm.find( ".awsm-b-filter-option" );
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
		const searchQuery = $rootWrapper.find( ".awsm-b-job-search" ).val();
		$rootWrapper.find( wrapperSelector ).data( "search", searchQuery );
		setPaginationBase( $rootWrapper, "jq", searchQuery );
		if ( awsmJobsPublic.deep_linking.search ) {
			const $paginationBase = $rootWrapper.find(
				'input[name="awsm_pagination_base"]'
			);
			updateQuery( "jq", searchQuery, $paginationBase.val() );
		}
		awsmJobFilters( $rootWrapper );
	}

	var updateQuery = function ( key, value, url, preservePagination ) {
		// When preserving pagination (e.g. on init sync), use the current URL path
		// so that WordPress canonical-redirected paths like /page/2/ are not lost.
		if ( preservePagination ) {
			url = currentUrl;
		} else {
			url = typeof url !== "undefined" ? url : currentUrl;
			url = url.split( "?" )[ 0 ];
		}
		const searchParams = new URLSearchParams( document.location.search );
		if ( ! preservePagination ) {
			if ( searchParams.has( "paged" ) ) {
				searchParams.delete( "paged" );
			}
			if ( searchParams.has( "page" ) ) {
				searchParams.delete( "page" );
			}
		}
		value = value !== undefined && value !== null ? String( value ) : "";

		if ( value !== "" ) {
			searchParams.set( key, value );
		} else {
			searchParams.delete( key );
		}
		let modQueryString = searchParams.toString();
		if ( modQueryString.length > 0 ) {
			modQueryString = "?" + modQueryString;
		}
		window.history.replaceState( {}, "", url + modQueryString );
	};

	var setPaginationBase = function ( $rootWrapper, key, value ) {
		const $paginationBase = $rootWrapper.find(
			'input[name="awsm_pagination_base"]'
		);
		if ( $paginationBase.length > 0 ) {
			const splittedURL = $paginationBase.val().split( "?" );
			let queryString = "";
			if ( splittedURL.length > 1 ) {
				queryString = splittedURL[ 1 ];
			}
			const searchParams = new URLSearchParams( queryString );
			if ( value.length > 0 ) {
				searchParams.set( key, value );
			} else {
				searchParams.delete( key );
			}
			$paginationBase.val( splittedURL[ 0 ] + "?" + searchParams.toString() );
			$rootWrapper.find( 'input[name="paged"]' ).val( 1 );
		}
	};

	if ( $( ".awsm-b-job-no-more-jobs-get" ).length > 0 ) {
		$( ".awsm-b-job-listings" ).hide();
		$( ".awsm-b-job-no-more-jobs-get" ).slice( 1 ).hide();
	}

	// Markup may include `awsm-selectric-loading` to prevent the native <select> flash on refresh.
	// Remove the class once all Selectric instances inside the wrap are initialized.
	$( filterSelector + ".awsm-selectric-loading" ).each( function () {
		const $wrap = $( this );
		const count = $wrap.find( "select.awsm-b-filter-option" ).length;

		$wrap.data( "awsmSelectricPending", count );

		if ( count === 0 ) {
			$wrap.removeClass( "awsm-selectric-loading" );
			return;
		}

		// Fallback: don't keep the UI hidden forever if Selectric fails to init for any reason.
		setTimeout( function () {
			$wrap.removeClass( "awsm-selectric-loading" );
		}, 2000 );
	} );

	// Init Selectric for each filter select.
	$( filterSelector + " .awsm-b-filter-option" ).each( function () {
		const $selectEl = $( this );
		const placement = $selectEl.closest( filterSelector ).data( "placement" ) || "top";
		const isTopPlacement = placement !== "side";

		$selectEl.selectric( {
			// Use Selectric UI on mobile too; native <select multiple> is not usable on many devices.
			nativeOnMobile: false,
			disableOnMobile: false,
			multiple: {
				keepMenuOpen: true,
				separator: ", ",
				maxLabelEntries: isTopPlacement ? 2 : false
			},
			onInit( select, selectric ) {
				const id = select.id;

				if ( selectric && selectric.elements && selectric.elements.input ) {
					const $input = $( selectric.elements.input );
					$( select ).attr( "id", "selectric-" + id );
					$input.attr( "id", id );
				}

				const $select = $( select );
				const $filterWrap = $select.closest( filterSelector );

				if ( $filterWrap.hasClass( "awsm-selectric-loading" ) ) {
					let pending = parseInt(
						$filterWrap.data( "awsmSelectricPending" ),
						10
					);
					if ( isNaN( pending ) ) {
						pending = 0;
					}
					pending = Math.max( pending - 1, 0 );
					$filterWrap.data( "awsmSelectricPending", pending );
					if ( pending === 0 ) {
						$filterWrap.removeClass( "awsm-selectric-loading" );
					}
				}

				setTimeout( function () {
					// Only multi-select dropdowns use the "All" sync behavior.
					if ( $select.prop( "multiple" ) ) {
						syncAllOptionFromUrl( $select );
						forceAllLabel( $select );
					}

					// Sync URL for pre-selected values on page load (from URL params).
					const $rootWrapper = $select.closest( rootWrapperSelector );
					const currentSpec = $select.closest( ".awsm-b-filter-item" ).data( "filter" );
					if ( currentSpec ) {
						let slugString = ""; 
						if ( $select.prop( "multiple" ) ) {
							const $allOption = $select.find( "option" ).eq( 0 );
							const slugs = $select.find( "option:selected" ).not( $allOption ).map( function () {
								return $( this ).data( "slug" );
							} ).get().filter( Boolean );
							slugString = slugs.join( "," );
						} else {
							const $selected = $select.find( "option:selected" );
							if ( $selected.index() > 0 ) {
								slugString = $selected.data( "slug" ) || "";
							}
						}
						if ( slugString ) {
							setPaginationBase( $rootWrapper, currentSpec, slugString );
							updateAwsmQuery( $rootWrapper, currentSpec, slugString, true );
						}
					}
				}, 0 );
			},

			arrowButtonMarkup:
				'<span class="awsm-selectric-arrow-drop">&#x25be;</span>',
			customClass: {
				prefix: "awsm-selectric",
				camelCase: false
			}
		} );
	} );

	$( document ).on(
		"change",
		filterSelector + " .awsm-b-filter-option",
		function () {
			const $select = $( this );
			handleAwsmMultiFilter( $select );

			setTimeout( function () {
				forceAllLabel( $select );
			}, 0 );

			// Keep dropdown open only if Selectric exists (desktop)
			if ( $select.prop( "multiple" ) && $select.data( "selectric" ) ) {
				$select.selectric( "open" );
			}
		}
	);

	function handleAwsmMultiFilter( $select ) {
		const $options = $select.find( "option" );
		const $all = $options.eq( 0 ); // "All"
		const $others = $options.slice( 1 ); // Individual options

		const $rootWrapper = $select.closest( rootWrapperSelector );
		const currentSpec = $select
			.closest( ".awsm-b-filter-item" )
			.data( "filter" );

		let slugs = [];

		// CURRENT state
		const isAllSelected = $all.is( ":selected" );
		const selectedOthersCount = $others.filter( ":selected" ).length;
		const totalOthersCount = $others.length;

		// PREVIOUS state
		const wasAllSelected = $select.data( "wasAllSelected" ) === true;

		/* =================================================
		SINGLE SELECT DROPDOWN
		================================================= */
		if ( ! $select.prop( "multiple" ) ) {
			if ( isAllSelected ) {
				$options.prop( "selected", false );
				$all.prop( "selected", true );

				setPaginationBase( $rootWrapper, currentSpec, "" );
				updateAwsmQuery( $rootWrapper, currentSpec, "" );
				awsmJobFilters( $rootWrapper );

				$select.data( "wasAllSelected", true );
				$select.selectric( "refresh" );
				return;
			}

			// Single selection (not All)
			const selectedSlug = $options.filter( ":selected" ).data( "slug" ) || "";
			setPaginationBase( $rootWrapper, currentSpec, selectedSlug );
			updateAwsmQuery( $rootWrapper, currentSpec, selectedSlug );
			awsmJobFilters( $rootWrapper );

			$select.data( "wasAllSelected", false );
			$select.selectric( "refresh" );
			return;
		}

		/* =================================================
		MULTI SELECT DROPDOWN
		================================================= */
		// CASE 1: User UNCHECKED "All" → Clear everything
		if ( wasAllSelected && ! isAllSelected ) {
			$options.prop( "selected", false );
			$select.selectric( "refresh" );

			setPaginationBase( $rootWrapper, currentSpec, "" );
			updateAwsmQuery( $rootWrapper, currentSpec, "" );
			awsmJobFilters( $rootWrapper );

			$select.data( "wasAllSelected", false );
			return;
		}

		// CASE 2: User CLICKED "All" → Select everything
		if ( isAllSelected && ! wasAllSelected ) {
			$options.prop( "selected", true );
			slugs = $others
				.map( function () {
					return $( this ).data( "slug" );
				} )
				.get();
		}

		// CASE 3: User selected all individuals manually → Auto-check All
		else if ( ! isAllSelected && selectedOthersCount === totalOthersCount ) {
			$all.prop( "selected", true );
			slugs = $others
				.map( function () {
					return $( this ).data( "slug" );
				} )
				.get();
		}

		// CASE 4: Normal individual selection
		else if ( selectedOthersCount > 0 ) {
			$all.prop( "selected", false );
			slugs = $others
				.filter( ":selected" )
				.map( function () {
					return $( this ).data( "slug" );
				} )
				.get();
		}

		// CASE 5: Nothing selected → Reset
		else {
			$options.prop( "selected", false );
			$select.selectric( "refresh" );

			setPaginationBase( $rootWrapper, currentSpec, "" );
			updateAwsmQuery( $rootWrapper, currentSpec, "" );
			awsmJobFilters( $rootWrapper );

			$select.data( "wasAllSelected", false );
			return;
		}

		// Save state
		$select.data( "wasAllSelected", $all.is( ":selected" ) );

		// Sync Selectric UI
		$select.selectric( "refresh" );

		// Apply filters
		const slugString = slugs.join( "," );
		setPaginationBase( $rootWrapper, currentSpec, slugString );
		updateAwsmQuery( $rootWrapper, currentSpec, slugString );
		awsmJobFilters( $rootWrapper );
	}

	function syncAllOptionFromUrl( $select ) {
		// Single-select dropdowns should not sync the "All" option.
		if ( ! $select.prop( "multiple" ) ) {
			return;
		}
		const $options = $select.find( "option" );
		const $all = $options.eq( 0 );
		const $others = $options.slice( 1 );

		const totalOthers = $others.length;
		const selectedOthers = $others.filter( ":selected" ).length;

		if ( totalOthers > 0 && selectedOthers === totalOthers ) {
			$all.prop( "selected", true );
		} else {
			$all.prop( "selected", false );
		}

		$select.selectric( "refresh" );
		$select.data( "wasAllSelected", $all.is( ":selected" ) );
	}

	function forceAllLabel( $select ) {
		const selectric = $select.data( "selectric" );
		const $allOption = $select.find( "option" ).first();

		if ( selectric && $allOption.is( ":selected" ) ) {
			// Selectric omits empty-value options in multi-select label; ensure "All" shows.
			selectric.elements.label.text( $allOption.text() );
		}
	}

	function updateAwsmQuery( $rootWrapper, spec, value, preservePagination ) {
		if ( ! awsmJobsPublic.deep_linking.spec ) {
			return;
		}
		const $paginationBase = $rootWrapper.find(
			'input[name="awsm_pagination_base"]'
		);
		updateQuery( spec, value, $paginationBase.val(), preservePagination );
	}

	$( filterSelector + " .awsm-b-job-search-btn" ).on( "click", function () {
		searchJobs( $( this ) );
	} );

	$( filterSelector + " .awsm-b-job-search-close-btn" ).on(
		"click",
		function () {
			const $elem = $( this );
			$elem
				.parents( rootWrapperSelector )
				.find( ".awsm-b-job-search" )
				.val( "" );
			searchJobs( $elem );
		}
	);

	$( filterSelector + " .awsm-b-job-search" ).on( "keypress", function ( e ) {
		if ( e.which == 13 ) {
			e.preventDefault();
			searchJobs( $( this ) );
		}
	} );

	/* ========== Job Listings Load More ========== */
	$( wrapperSelector ).on(
		"click",
		".awsm-b-jobs-pagination .awsm-b-load-more-btn, .awsm-b-jobs-pagination a.page-numbers",
		function ( e ) {
			e.preventDefault();
			const $triggerElem = $( this );
			const isDefaultPagination = $triggerElem.hasClass(
				"awsm-b-load-more-btn"
			);
			let paged = 1;
			let wpData = [];

			const $mainContainer = $triggerElem.parents( rootWrapperSelector );
			const $listingsContainer = $mainContainer.find( wrapperSelector );
			const $listingsrowContainer = $listingsContainer.find( sectionSelector );

			const $paginationWrapper = $triggerElem.parents(
				".awsm-b-jobs-pagination"
			);
			const listings = $listingsContainer.data( "listings" );
			const lang = $listingsContainer.data( "lang" );
			const searchQuery = $listingsContainer.data( "search" );
			const order_by = $listingsContainer.data( "awsm-order-by" );
			const filter_items_order = $listingsContainer.data( "awsm-filter-items-order" );

			/* added for block */
			const layout = $listingsContainer.data( "awsm-layout" );
			const hide_expired_jobs = $listingsContainer.data(
				"awsm-hide-expired-jobs"
			);
		
			const other_options = $listingsContainer.data( "awsm-other-options" );
			const show_spec_icon = $listingsContainer.data( "awsm-spec-icons" );
			const button_style = $listingsContainer.data( "awsm-button-style" );
			const button_text = $listingsContainer.data( "awsm-button-text" );
			/* end */

			if ( isDefaultPagination ) {
				$triggerElem.prop( "disabled", true );
				paged = $triggerElem.data( "page" );
				paged = typeof paged === "undefined" ? 1 : paged;
			} else {
				$triggerElem
					.parents( ".page-numbers" )
					.find( ".page-numbers" )
					.removeClass( "current" )
					.removeAttr( "aria-current" );
				$triggerElem.addClass( "current" ).attr( "aria-current", "page" );
			}
			$paginationWrapper.addClass( "awsm-b-jobs-pagination-loading" );

			// filters
			const $filterForm = $mainContainer.find( filterSelector + " form" );
			if ( filterCheck( $filterForm ) ) {
				const $filterOption = $filterForm.find( ".awsm-b-filter-option" );
				wpData = $filterOption.serializeArray();
			}

			$( document ).trigger( "awsm_block_collect_checkbox_filters", [ wpData, $filterForm ] );

			if ( ! isDefaultPagination ) {
				let paginationBaseURL = $triggerElem.attr( "href" );
				const splittedURL = paginationBaseURL.split( "?" );
				let queryString = "";
				const isHomepage = window.awsmJobsPublic && awsmJobsPublic.is_homepage;
				const pageKey = isHomepage ? "page" : "paged";

				if ( splittedURL.length > 1 ) {
					const searchParams = new URLSearchParams( splittedURL[ 1 ] );
					paged =
						searchParams.get( pageKey ) ||
						searchParams.get( pageKey === "page" ? "paged" : "page" );

					if ( ! paged ) {
						paged = 1;
					}

					searchParams.delete( "page" );
					searchParams.delete( "paged" );

					if ( searchParams.toString().length > 0 ) {
						queryString = "?" + searchParams.toString();
					}
				} else {
					const pageMatch = paginationBaseURL.match( /\/page\/(\d+)\/?/ );
					if ( pageMatch ) {
						paged = pageMatch[ 1 ];
					} else {
						paged = 1;
					}
				}

				paginationBaseURL = splittedURL[ 0 ] + queryString;
				wpData.push( {
					name: "awsm_pagination_base",
					value: splittedURL[ 0 ] + queryString
				} );
				if ( awsmJobsPublic.deep_linking.pagination ) {
					updateQuery( pageKey, paged, paginationBaseURL );
				}
			}

			// taxonomy archives
			if ( awsmJobsPublic.is_tax_archive ) {
				var taxonomy = $listingsContainer.data( "taxonomy" );
				const termId = $listingsContainer.data( "termId" );
				if (
					typeof taxonomy !== "undefined" &&
					typeof termId !== "undefined"
				) {
					wpData.push( {
						name: "awsm_job_spec[" + taxonomy + "]",
						value: termId
					} );
				}
			}

			wpData.push(
				{
					name: "action",
					value: "block_loadmore"
				},
				{
					name: "paged",
					value: paged
				}
			);
			if ( typeof listings !== "undefined" ) {
				wpData.push( {
					name: "listings_per_page",
					value: listings
				} );
			}

			/* added for block */
			if ( typeof layout !== "undefined" ) {
				wpData.push( {
					name: "awsm-layout",
					value: layout
				} );
			}
			if ( typeof hide_expired_jobs !== "undefined" ) {
				wpData.push( {
					name: "awsm-hide-expired-jobs",
					value: hide_expired_jobs
				} );
			}

			if ( typeof other_options !== "undefined" ) {
				wpData.push( {
					name: "awsm-other-options",
					value: other_options
				} );
			}

			if ( typeof show_spec_icon !== "undefined" ) {
				wpData.push( {
					name: "awsm-spec-icons",
					value: show_spec_icon
				} );
			}

			if ( typeof order_by !== "undefined" ) {
				wpData.push( {
					name: "awsm-order-by",
					value: order_by
				} );
			}

			if ( typeof filter_items_order !== "undefined" ) {
				wpData.push( {
					name: "awsm-filter-items-order",
					value: filter_items_order
				} );
			}

			if ( typeof button_style !== "undefined" ) {
				wpData.push( {
					name: "awsm-button-style",
					value: button_style
				} );
			}

			if ( button_text ) {
				wpData.push( {
					name: "awsm-button-text",
					value: button_text
				} );
			}

			if ( typeof lang !== "undefined" ) {
				wpData.push( {
					name: "lang",
					value: lang
				} );
			}

			if ( typeof searchQuery !== "undefined" ) {
				wpData.push( {
					name: "jq",
					value: searchQuery
				} );
			}

			if ( awsmJobsPublic.block_nonce ) {
				wpData.push( {
					name: "awsm_block_nonce",
					value: awsmJobsPublic.block_nonce
				} );
			}

			$( document ).trigger( "awsmjobs_block_load_more", [
				$listingsContainer,
				wpData
			] );
			const listingsData = getListingsData( $listingsContainer );
			if ( listingsData.length > 0 ) {
				wpData = wpData.concat( listingsData );
			}
			wpData = normalizeAndDedupeParams( wpData );

			// now, handle ajax
			$.ajax( {
				url: awsmJobsPublic.ajaxurl,
				data: $.param( wpData ),
				type: "POST",
				beforeSend() {
					if ( isDefaultPagination ) {
						$triggerElem.text( awsmJobsPublic.i18n.loading_text );
					} else {
						$listingsContainer.addClass( "awsm-b-jobs-loading" );
					}
				}
			} )
				.done( function ( response ) {
					if ( response.data.html ) {
						let effectDuration = $paginationWrapper.data( "effectDuration" );
						$paginationWrapper.remove();
						if ( isDefaultPagination ) {
							$listingsrowContainer.append( response.data.html );
						} else {
							$listingsrowContainer.html( response.data.html );
							$listingsContainer.removeClass( "awsm-b-jobs-loading" );
							if ( typeof effectDuration !== "undefined" ) {
								effectDuration = isNaN( effectDuration )
									? effectDuration
									: Number( effectDuration );
								$( "html, body" ).animate(
									{
										scrollTop: $mainContainer.offset().top - 25
									},
									effectDuration
								);
							}
						}
						if ( response.data.pagination_html ) {
							$listingsrowContainer.after( response.data.pagination_html );
						}
					} else {
						$triggerElem.remove();
					}

					$( document ).trigger( "awsmjobs_load_more", [
						$triggerElem,
						response.data.html
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
	$( document ).on( "click", ".awsm-b-filter-toggle", function ( e ) {
		e.preventDefault();
		const $elem = $( this );
		$elem.toggleClass( "awsm-on" );
		if ( $elem.hasClass( "awsm-on" ) ) {
			$elem.attr( "aria-pressed", "true" );
		} else {
			$elem.attr( "aria-pressed", "false" );
		}
		const $parent = $elem.parent();
		$parent.find( ".awsm-b-filter-items" ).slideToggle();
	} );

	/**
	 * Handle the responsive styles for filters in the job listing when search is enabled.
	 */
	function filtersResponsiveStylesHandler() {
		const $filtersWrap = $( ".awsm-b-filter-wrap" ).not(
			".awsm-b-no-search-filter-wrap"
		);
		$filtersWrap.each( function () {
			const $wrapper = $( this );
			const $items = $wrapper.find( ".awsm-b-filter-item" );

			if ( $items.length === 0 ) {
				$wrapper.removeClass( "awsm-b-full-width-search-filter-wrap" );
				return;
			}

			const firstOffset = $items.first().offset();
			const lastOffset = $items.last().offset();
			if ( ! firstOffset || ! lastOffset ) {
				$wrapper.removeClass( "awsm-b-full-width-search-filter-wrap" );
				return;
			}

			const filterFirstTop = firstOffset.top;
			const filterLastTop = lastOffset.top;
			if ( window.innerWidth < 768 ) {
				$wrapper.removeClass( "awsm-b-full-width-search-filter-wrap" );
				return;
			}
			if ( filterLastTop > filterFirstTop ) {
				$wrapper.addClass( "awsm-b-full-width-search-filter-wrap" );
			} else {
				$wrapper.removeClass( "awsm-b-full-width-search-filter-wrap" );
			}
		} );
	}

	if (
		$( ".awsm-b-filter-wrap" ).not( ".awsm-b-no-search-filter-wrap" ).length > 0
	) {
		filtersResponsiveStylesHandler();
		$( window ).on( "resize", filtersResponsiveStylesHandler );
	}
} );
