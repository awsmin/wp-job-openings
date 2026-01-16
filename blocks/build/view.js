/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _typeof; }
/* harmony export */ });
function _typeof(o) {
  "@babel/helpers - typeof";

  return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, _typeof(o);
}


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Check if module exists (development only)
/******/ 		if (__webpack_modules__[moduleId] === undefined) {
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
!function() {
/*!*********************!*\
  !*** ./src/view.js ***!
  \*********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");



jQuery(function ($) {
  var rootWrapperSelector = '.awsm-b-job-wrap';
  var wrapperSelector = '.awsm-b-job-listings';
  var sectionSelector = '.awsm-b-job-listing-items';

  /* ========== Job Search and Filtering ========== */

  var filterSelector = '.awsm-b-filter-wrap';
  var currentUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
  var triggerFilter = true;
  function getListingsData($wrapper) {
    var data = [];
    var parsedListingsAttrs = ['listings', 'specs', 'search', 'lang', 'taxonomy', 'termId'];

    /* added for block */
    parsedListingsAttrs.push('awsm-layout');
    parsedListingsAttrs.push('awsm-hide-expired-jobs');
    parsedListingsAttrs.push('awsm-other-options');
    parsedListingsAttrs.push('awsm-listings-total');
    parsedListingsAttrs.push('awsm-selected-terms');

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

    $(document).trigger('awsmJobBlockListingsData', [parsedListingsAttrs]);
    var dataAttrs = $wrapper.data();
    $.each(dataAttrs, function (dataAttr, value) {
      if ($.inArray(dataAttr, parsedListingsAttrs) !== -1) {
        data.push({
          name: dataAttr,
          value: value
        });
      }
    });
    return data;
  }
  function awsmJobFilters($rootWrapper) {
    var $wrapper = $rootWrapper.find(wrapperSelector);
    var $rowWrapper = $wrapper.find(sectionSelector);
    var $filterForm = $rootWrapper.find(filterSelector + ' form');
    var formData = [];
    var formMethod = 'POST';
    if ($filterForm.length > 0) {
      formData = $filterForm.serializeArray();
      formMethod = $filterForm.attr('method') ? $filterForm.attr('method').toUpperCase() : 'POST';
    } else {
      formData.push({
        name: 'action',
        value: 'block_jobfilter'
      });
    }

    /* ========================
    Wrapper data
    ======================== */
    var listings = $wrapper.data('listings');
    var specs = $wrapper.data('specs');
    var layout = $wrapper.data('awsm-layout');
    var hide_expired_jobs = $wrapper.data('awsm-hide-expired-jobs');
    var selected_terms = $wrapper.data('awsm-selected-terms');
    var other_options = $wrapper.data('awsm-other-options');
    var listings_total = $wrapper.data('awsm-listings-total');

    /* ========================
    Style variables
    ======================== */
    var styleFields = {
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
      var spec = $(this).data('filter');
      var searchParams = new URLSearchParams(document.location.search);
      var queryVal = searchParams.get(spec);
      var $option = $(this).find('.awsm-b-filter-option');
      if (!$option.val() && queryVal) {
        formData.forEach(function (item) {
          if (item.name === $option.attr('name')) {
            item.value = '-1';
          }
        });
      }
    });

    /* ========================
    Core data
    ======================== */
    formData.push({
      name: 'listings_per_page',
      value: listings
    });
    if (specs !== undefined) {
      formData.push({
        name: 'shortcode_specs',
        value: specs
      });
    }
    if (layout !== undefined) {
      formData.push({
        name: 'awsm-layout',
        value: layout
      });
    }
    if (selected_terms) {
      if (typeof selected_terms === 'string') {
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
    if (hide_expired_jobs !== undefined) {
      formData.push({
        name: 'awsm-hide-expired-jobs',
        value: hide_expired_jobs
      });
    }
    if (other_options !== undefined) {
      formData.push({
        name: 'awsm-other-options',
        value: other_options
      });
    }
    if (listings_total !== undefined) {
      formData.push({
        name: 'awsm-listings-total',
        value: listings_total
      });
    }

    /* ========================
    Style data (FIXED)
    ======================== */
    Object.keys(styleFields).forEach(function (key) {
      var val = styleFields[key];
      if (val === undefined) return;

      // stringify objects safely
      var value = (0,_babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0__["default"])(val) === 'object' ? JSON.stringify(val) : val;
      formData.push({
        name: key,
        value: value
      });
    });

    /* ========================
    REMOVE EMPTY VALUES (IMPORTANT)
    ======================== */
    formData = formData.filter(function (item) {
      return item.value !== '';
    });

    /* ========================
    External hook
    ======================== */
    $(document).trigger('awsmJobBlockFiltersFormData', [$wrapper, formData]);
    if (!triggerFilter) return;
    triggerFilter = false;
    var actionUrl = $filterForm.length > 0 ? $filterForm.attr('action') : awsmJobsPublic.ajaxurl;
    $.ajax({
      url: actionUrl,
      type: formMethod,
      data: formData,
      beforeSend: function beforeSend() {
        $wrapper.addClass('awsm-b-jobs-loading');
      }
    }).done(function (response) {
      $rowWrapper.html(response.data.html);
      $(document).trigger('awsmjobs_filtered_listings', [$rootWrapper, response.data.html]);
    }).fail(function (xhr) {
      console.error(xhr);
    }).always(function () {
      $wrapper.removeClass('awsm-b-jobs-loading');
      triggerFilter = true;
    });
  }
  function filterCheck($filterForm) {
    var check = false;
    if ($filterForm.length > 0) {
      var $filterOption = $filterForm.find('.awsm-b-filter-option');
      $filterOption.each(function () {
        if ($(this).val().length > 0) {
          check = true;
        }
      });
    }
    return check;
  }
  function searchJobs($elem) {
    var $rootWrapper = $elem.parents(rootWrapperSelector);
    var searchQuery = $rootWrapper.find('.awsm-b-job-search').val();
    $rootWrapper.find(wrapperSelector).data('search', searchQuery);
    if (searchQuery.length === 0) {

      //$rootWrapper.find('.awsm-b-job-search-icon-wrapper').addClass('awsm-b-job-hide');
    }
    setPaginationBase($rootWrapper, 'jq', searchQuery);
    if (awsmJobsPublic.deep_linking.search) {
      var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
      updateQuery('jq', searchQuery, $paginationBase.val());
    }
    awsmJobFilters($rootWrapper);
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
  var updateQuery = function updateQuery(key, value, url) {
    url = typeof url !== 'undefined' ? url : currentUrl;
    url = url.split('?')[0];
    var searchParams = new URLSearchParams(document.location.search);
    if (searchParams.has('paged')) {
      searchParams.delete('paged');
    }
    if (value.length > 0) {
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
  var setPaginationBase = function setPaginationBase($rootWrapper, key, value) {
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
  if ($('.awsm-b-job-no-more-jobs-get').length > 0) {
    $('.awsm-b-job-listings').hide();
    $('.awsm-b-job-no-more-jobs-get').slice(1).hide();
  }
  $(filterSelector + ' .awsm-b-filter-option').on('change', function (e) {
    e.preventDefault();
    $('.awsm-b-job-listings').show();
    var $elem = $(this);
    var $rootWrapper = $elem.closest(rootWrapperSelector);
    var currentSpec = $elem.closest('.awsm-b-filter-item').data('filter');
    var isMultiple = $elem.prop('multiple'); // Check if it's a multiple select
    var allOptions = $elem.find('option');
    var firstOption = allOptions.eq(0); // "All Job Type"
    var selectedOptions = $elem.find('option:selected');
    var isAllSelected = firstOption.prop('selected');

    // **Fix: Restrict list item selection to current dropdown only**
    var allLiItems = $elem.closest('.awsm-b-filter-item').find('ul li');
    var firstLiItem = allLiItems.eq(0); // "All Job Type" in <ul>
    var selectedLiItems = allLiItems.filter('.selected');
    var isCheckboxFilter = $elem.closest('.awsm-b-filter-item').find('input[type="checkbox"]').length > 0;
    var slugs = [];
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
          var index = $(this).index();
          allLiItems.eq(index).addClass('selected'); // **Fix: Apply changes to corresponding <li>**
        });
        slugs = selectedOptions.map(function () {
          return $(this).data('slug');
        }).get().filter(Boolean);
      }
    } else if (isCheckboxFilter) {
      // **Handle checkboxes**
      var $checkboxes = $elem.closest('.awsm-b-filter-item').find('input[type="checkbox"]');
      var $allCheckbox = $checkboxes.eq(0); // First checkbox is "All"

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
    var slugString = slugs.length > 0 ? slugs.join(',') : '';

    // **Update pagination and filters only for the affected dropdown**
    if ($('.awsm-job-listings').length > 0) {
      $rootWrapper.find('.awsm-b-job-no-more-jobs-get').hide();
    }
    setPaginationBase($rootWrapper, currentSpec, slugString);

    // **Update the URL without affecting other dropdowns**
    if (awsmJobsPublic.deep_linking.spec) {
      var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
      updateQuery(currentSpec, slugString, $paginationBase.val());
    }
    awsmJobFilters($rootWrapper);
  });
  $(filterSelector + ' .awsm-filter-checkbox').on('change', function (e) {
    var selectedFilters = {};
    var slugs = []; // Initialize an array to collect slugs
    var $elem = $(this);
    var $rootWrapper = $elem.parents(rootWrapperSelector);
    var currentSpec = $elem.parents('.awsm-filter-list-item').data('filter');

    // Loop through checked checkboxes and build selectedFilters and slugs array
    $('.awsm-filter-checkbox:checked').each(function () {
      var taxonomy = $(this).data('taxonomy');
      var termId = $(this).data('term-id');
      var slug = $(this).data('slug'); // Get the slug from the checkbox

      // Add the slug to the slugs array if it exists
      if (slug) {
        slugs.push(slug);
      }

      // Populate the selectedFilters object
      if (!selectedFilters[taxonomy]) {
        selectedFilters[taxonomy] = [];
      }
      selectedFilters[taxonomy].push(termId);
    });

    // Convert slugs array to a comma-separated string
    var slugString = slugs.length > 0 ? slugs.join(',') : '';

    // Handle deep linking
    if (awsmJobsPublic.deep_linking.spec) {
      var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
      updateQuery(currentSpec, slugString, $paginationBase.val()); // Use the comma-separated slugString
    }

    // Apply the job filters
    awsmJobFilters($rootWrapper);
  });
  $(filterSelector + ' .awsm-b-job-search-btn').on('click', function () {
    searchJobs($(this));
  });
  $(filterSelector + ' .awsm-b-job-search-close-btn').on('click', function () {
    var $elem = $(this);
    $elem.parents(rootWrapperSelector).find('.awsm-b-job-search').val('');
    searchJobs($elem);
  });
  $(filterSelector + ' .awsm-b-job-search').on('keypress', function (e) {
    if (e.which == 13) {
      e.preventDefault();
      searchJobs($(this));
    }
  });

  /* =========================
  * Helpers (ADD ONCE)
  * ========================= */

  function addToRequest(data, name, value) {
    var stringify = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
    if (typeof value === 'undefined') return;
    if (stringify && (0,_babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0__["default"])(value) === 'object') {
      try {
        value = JSON.stringify(value);
      } catch (e) {
        return;
      }
    }
    data.push({
      name: name,
      value: value
    });
  }
  function normalizeRequestData(data) {
    var map = {};
    data.forEach(function (item) {
      var value = item.value;
      if ((0,_babel_runtime_helpers_typeof__WEBPACK_IMPORTED_MODULE_0__["default"])(value) === 'object') {
        try {
          value = JSON.stringify(value);
        } catch (e) {
          value = '';
        }
      }
      map[item.name] = value; // last value wins
    });
    return Object.keys(map).map(function (key) {
      return {
        name: key,
        value: map[key]
      };
    });
  }

  /* =========================
  * Pagination / Load More
  * ========================= */

  $(wrapperSelector).on('click', '.awsm-b-jobs-pagination .awsm-b-load-more-btn, .awsm-b-jobs-pagination a.page-numbers', function (e) {
    e.preventDefault();
    var $triggerElem = $(this);
    var isDefaultPagination = $triggerElem.hasClass('awsm-b-load-more-btn');
    var paged = 1;
    var wpData = [];
    var $mainContainer = $triggerElem.parents(rootWrapperSelector);
    var $listingsContainer = $mainContainer.find(wrapperSelector);
    var $listingsrowContainer = $listingsContainer.find(sectionSelector);
    var $paginationWrapper = $triggerElem.parents('.awsm-b-jobs-pagination');
    var listings = $listingsContainer.data('listings');
    var totalPosts = $listingsContainer.data('total-posts');
    var specs = $listingsContainer.data('specs');
    var lang = $listingsContainer.data('lang');
    var searchQuery = $listingsContainer.data('search');

    /* block data */
    var layout = $listingsContainer.data('awsm-layout');
    var hide_expired_jobs = $listingsContainer.data('awsm-hide-expired-jobs');
    var selected_terms = $listingsContainer.data('awsm-selected-terms');
    var other_options = $listingsContainer.data('awsm-other-options');

    /* style data */
    var hz_sf_border_color = $listingsContainer.data('hz_sf_border_color');
    var hz_sf_border_width = $listingsContainer.data('hz_sf_border_width');
    var hz_sf_padding = $listingsContainer.data('hz_sf_padding');
    var hz_sf_border_radius = $listingsContainer.data('hz_sf_border_radius');
    var hz_sidebar_width = $listingsContainer.data('hz_sidebar_width');
    var block_id = $listingsContainer.data('block_id');
    var hz_ls_border_color = $listingsContainer.data('hz_ls_border_color');
    var hz_ls_border_width = $listingsContainer.data('hz_ls_border_width');
    var hz_ls_border_radius = $listingsContainer.data('hz_ls_border_radius');
    var hz_jl_border_color = $listingsContainer.data('hz_jl_border_color');
    var hz_jl_border_width = $listingsContainer.data('hz_jl_border_width');
    var hz_jl_border_radius = $listingsContainer.data('hz_jl_border_radius');
    var hz_jl_padding = $listingsContainer.data('hz_jl_padding');
    var hz_bs_border_color = $listingsContainer.data('hz_bs_border_color');
    var hz_bs_border_width = $listingsContainer.data('hz_bs_border_width');
    var hz_bs_border_radius = $listingsContainer.data('hz_bs_border_radius');
    var hz_bs_padding = $listingsContainer.data('hz_bs_padding');
    var hz_button_background_color = $listingsContainer.data('hz_button_background_color');
    var hz_button_text_color = $listingsContainer.data('hz_button_text_color');
    if (isDefaultPagination) {
      $triggerElem.prop('disabled', true);
      paged = $triggerElem.data('page') || 1;
    } else {
      $triggerElem.parents('.page-numbers').find('.page-numbers').removeClass('current').removeAttr('aria-current');
      $triggerElem.addClass('current').attr('aria-current', 'page');
    }
    $paginationWrapper.addClass('awsm-b-jobs-pagination-loading');

    /* =========================
    * Filters
    * ========================= */

    var $filterForm = $mainContainer.find(filterSelector + ' form');
    if (filterCheck($filterForm)) {
      wpData = $filterForm.find('.awsm-b-filter-option').serializeArray();
    }
    var specsList = {};
    $filterForm.find('.awsm-filter-checkbox:checked').each(function () {
      var $checkbox = $(this);
      var taxonomy = $checkbox.data('taxonomy');
      var termId = $checkbox.data('term-id');
      if (taxonomy && termId) {
        if (!specsList[taxonomy]) {
          specsList[taxonomy] = [];
        }
        specsList[taxonomy].push(termId);
      }
    });
    var _loop = function _loop(taxonomy) {
      specsList[taxonomy].forEach(function (termId) {
        wpData.push({
          name: "awsm_job_specs_list[".concat(taxonomy, "][]"),
          value: termId
        });
      });
    };
    for (var taxonomy in specsList) {
      _loop(taxonomy);
    }

    /* =========================
    * Pagination Base
    * ========================= */

    if (!isDefaultPagination) {
      var paginationBaseURL = $triggerElem.attr('href');
      var parts = paginationBaseURL.split('?');
      var queryString = '';
      if (parts[1]) {
        var params = new URLSearchParams(parts[1]);
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

    var listingsData = getListingsData($listingsContainer);
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
      beforeSend: function beforeSend() {
        if (isDefaultPagination) {
          $triggerElem.text(awsmJobsPublic.i18n.loading_text);
        } else {
          $listingsContainer.addClass('awsm-b-jobs-loading');
        }
      }
    }).done(function (response) {
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
    }).fail(function (xhr) {
      console.log(xhr);
    });
  });

  /**
   * Handle the filters toggle button in the job listing.
   */
  $(document).on('click', '.awsm-b-filter-toggle', function (e) {
    e.preventDefault();
    var $elem = $(this);
    $elem.toggleClass('awsm-on');
    if ($elem.hasClass('awsm-on')) {
      $elem.attr('aria-pressed', 'true');
    } else {
      $elem.attr('aria-pressed', 'false');
    }
    var $parent = $elem.parent();
    $parent.find('.awsm-b-filter-items').slideToggle();
  });

  /**
   * Handle the responsive styles for filters in the job listing when search is enabled.
   */
  function filtersResponsiveStylesHandler() {
    var $filtersWrap = $('.awsm-b-filter-wrap').not('.awsm-b-no-search-filter-wrap');
    $filtersWrap.each(function () {
      var $wrapper = $(this);
      var filterFirstTop = $wrapper.find('.awsm-b-filter-item').first().offset().top;
      var filterLastTop = $wrapper.find('.awsm-b-filter-item').last().offset().top;
      if (window.innerWidth < 768) {
        $wrapper.removeClass('awsm-b-full-width-search-filter-wrap');
        return;
      }
      if (filterLastTop > filterFirstTop) {
        $wrapper.addClass('awsm-b-full-width-search-filter-wrap');
      }
    });
  }
  if ($('.awsm-b-filter-wrap').not('.awsm-b-no-search-filter-wrap').length > 0) {
    filtersResponsiveStylesHandler();
    $(window).on('resize', filtersResponsiveStylesHandler);
  }
});
}();
/******/ })()
;
//# sourceMappingURL=view.js.map