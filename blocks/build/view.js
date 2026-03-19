/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ (function(module) {

module.exports = window["jQuery"];

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
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
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
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

"use strict";
jQuery(function ($) {
  var rootWrapperSelector = ".awsm-b-job-wrap";
  var wrapperSelector = ".awsm-b-job-listings";
  var sectionSelector = ".awsm-b-job-listing-items";

  /* ========== Job Search and Filtering ========== */

  var filterSelector = ".awsm-b-filter-wrap";
  var currentUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
  function getListingsData($wrapper) {
    var data = [];
    var seen = {};
    var normalizeKey = function normalizeKey(key) {
      var strKey = String(key);

      // Only normalize AWSM block keys; keep everything else as-is (e.g. `termId`).
      if (strKey.indexOf("awsm") !== 0) {
        return strKey;
      }

      // jQuery `.data()` turns `data-awsm-layout` into `awsmLayout`; convert back.
      if (/[A-Z]/.test(strKey)) {
        return strKey.replace(/([a-z0-9])([A-Z])/g, "$1-$2").toLowerCase();
      }
      return strKey;
    };
    var parsedListingsAttrs = ["listings", "search", "lang", "taxonomy", "termId",
    // Prevent wrapper state from overriding computed pagination.
    "page", "paged",
    // block attrs (ONLY dashed)
    "awsm-layout", "awsm-hide-expired-jobs", "awsm-other-options", "awsm-selected-terms", "awsm-spec-icons", "awsm-order-by"];

    // Allow Pro to extend
    $(document).trigger("awsmJobBlockListingsData", [parsedListingsAttrs]);

    // Compare on normalized keys so `awsmLayout` and `awsm-layout` behave the same.
    var normalizedParsedKeys = parsedListingsAttrs.map(normalizeKey);
    var dataAttrs = $wrapper.data();
    $.each(dataAttrs, function (dataAttr, value) {
      var normalizedKey = normalizeKey(dataAttr);
      if ($.inArray(normalizedKey, normalizedParsedKeys) === -1 && !seen[normalizedKey]) {
        seen[normalizedKey] = true;
        data.push({
          name: normalizedKey,
          value: value
        });
      }
    });
    return data;
  }

  // Normalize AWSM camelCase params to dashed, then de-dupe scalar keys.
  // Keeps bracket params (e.g. `awsm_job_spec[tax][]`) untouched to preserve multi-values.
  function normalizeAndDedupeParams(params) {
    var out = [];
    var indexByName = {};
    (params || []).forEach(function (item) {
      if (!item || typeof item.name === "undefined") {
        return;
      }
      var origName = String(item.name);
      var isBracket = origName.indexOf("[") !== -1;
      var name = origName;
      // Only normalize AWSM keys; keep others as-is (e.g. `termId`).
      if (!isBracket && name.indexOf("awsm") === 0 && /[A-Z]/.test(name)) {
        name = name.replace(/([a-z0-9])([A-Z])/g, "$1-$2").toLowerCase();
      }
      var normalizedItem = name === origName ? item : {
        name: name,
        value: item.value
      };
      if (isBracket) {
        out.push(normalizedItem);
        return;
      }
      if (typeof indexByName[name] !== "undefined") {
        out[indexByName[name]] = normalizedItem; // keep last
        return;
      }
      indexByName[name] = out.length;
      out.push(normalizedItem);
    });
    return out;
  }
  function awsmJobFilters($rootWrapper) {
    var $wrapper = $rootWrapper.find(wrapperSelector);
    var $rowWrapper = $wrapper.find(sectionSelector);
    var $filterForm = $rootWrapper.find(filterSelector + " form");
    var formData = [];
    if ($filterForm.length > 0) {
      formData = $filterForm.serializeArray();
      var formMethod = $filterForm.attr("method") ? $filterForm.attr("method").toUpperCase() : "POST";
    } else {
      formData.push({
        name: "action",
        value: "block_jobfilter"
      });
      var formMethod = "POST";
    }
    var listings = $wrapper.data("listings");
    var layout = $wrapper.data("awsm-layout");
    var hide_expired_jobs = $wrapper.data("awsm-hide-expired-jobs");
    //	let selected_terms 		= $wrapper.data( 'awsm-selected-terms' );
    var other_options = $wrapper.data("awsm-other-options");
    var show_spec_icon = $wrapper.data("awsm-spec-icons");
    var order_by = $wrapper.data("awsm-order-by");

    /* Filter URL sync logic */
    $rootWrapper.find(".awsm-b-filter-item").each(function () {
      var currentLoopSpec = $(this).data("filter");
      var searchParams = new URLSearchParams(document.location.search);
      var currentSpecQueryVal = searchParams.get(currentLoopSpec);
      var $currentOption = $(this).find(".awsm-b-filter-option");
      if ($currentOption.val().length === 0 && currentSpecQueryVal && currentSpecQueryVal.length > 0) {
        formData.forEach(function (item) {
          if (item.name === $currentOption.attr("name")) {
            item.value = "-1";
          }
        });
      }
    });

    /* Core parameters */
    formData.push({
      name: "listings_per_page",
      value: listings
    });
    if (typeof layout !== "undefined") {
      formData.push({
        name: "awsm-layout",
        value: layout
      });
    }

    /* if ( selected_terms ) {
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
    } */

    if (typeof hide_expired_jobs !== "undefined") {
      formData.push({
        name: "awsm-hide-expired-jobs",
        value: hide_expired_jobs
      });
    }
    if (typeof other_options !== "undefined") {
      formData.push({
        name: "awsm-other-options",
        value: other_options
      });
    }
    if (typeof show_spec_icon !== "undefined") {
      formData.push({
        name: "awsm-spec-icons",
        value: show_spec_icon
      });
    }
    if (typeof order_by !== "undefined") {
      formData.push({
        name: "awsm-order-by",
        value: order_by
      });
    }
    var listingsData = getListingsData($wrapper);
    if (listingsData.length > 0) {
      formData = formData.concat(listingsData);
    }
    if (awsmJobsPublic.block_nonce) {
      formData.push({
        name: "awsm_block_nonce",
        value: awsmJobsPublic.block_nonce
      });
    }
    $(document).trigger("awsmJobBlockFiltersFormData", [$wrapper, formData]);
    formData = normalizeAndDedupeParams(formData);
    if (!$wrapper.data("awsmFilterBusy")) {
      $wrapper.data("awsmFilterBusy", true);
      var actionUrl = $filterForm.length > 0 ? $filterForm.attr("action") : awsmJobsPublic.ajaxurl;
      $.ajax({
        url: actionUrl,
        beforeSend: function beforeSend() {
          $wrapper.addClass("awsm-b-jobs-loading");
        },
        data: formData,
        type: formMethod
      }).done(function (response) {
        $rowWrapper.html(response.data.html);
        var $searchControl = $rootWrapper.find(".awsm-b-job-search");
        if ($searchControl.length > 0) {
          if ($searchControl.val().length > 0) {
            $rootWrapper.find(".awsm-b-job-search-btn").addClass("awsm-b-job-hide");
            $rootWrapper.find(".awsm-b-job-search-close-btn").removeClass("awsm-b-job-hide");
          } else {
            $rootWrapper.find(".awsm-b-job-search-btn").removeClass("awsm-b-job-hide");
            $rootWrapper.find(".awsm-b-job-search-close-btn").addClass("awsm-b-job-hide");
          }
        }
        $(document).trigger("awsmjobs_filtered_listings", [$rootWrapper, response.data.html]);
      }).fail(function (xhr) {
        console.log(xhr);
      }).always(function () {
        $wrapper.removeClass("awsm-b-jobs-loading");
        $wrapper.data("awsmFilterBusy", false);
      });
    }
  }
  function filterCheck($filterForm) {
    var check = false;
    if ($filterForm.length > 0) {
      var $filterOption = $filterForm.find(".awsm-b-filter-option");
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
    var searchQuery = $rootWrapper.find(".awsm-b-job-search").val();
    $rootWrapper.find(wrapperSelector).data("search", searchQuery);
    if (searchQuery.length === 0) {
      //$rootWrapper.find('.awsm-b-job-search-icon-wrapper').addClass('awsm-b-job-hide');
    }
    setPaginationBase($rootWrapper, "jq", searchQuery);
    if (awsmJobsPublic.deep_linking.search) {
      var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
      updateQuery("jq", searchQuery, $paginationBase.val());
    }
    awsmJobFilters($rootWrapper);
  }
  var updateQuery = function updateQuery(key, value, url) {
    url = typeof url !== "undefined" ? url : currentUrl;
    url = url.split("?")[0];
    var searchParams = new URLSearchParams(document.location.search);
    if (searchParams.has("paged")) {
      searchParams.delete("paged");
    }
    if (searchParams.has("page")) {
      searchParams.delete("page");
    }
    value = value !== undefined && value !== null ? String(value) : "";
    if (value !== "") {
      searchParams.set(key, value);
    } else {
      searchParams.delete(key);
    }
    var modQueryString = searchParams.toString();
    if (modQueryString.length > 0) {
      modQueryString = "?" + modQueryString;
    }
    window.history.replaceState({}, "", url + modQueryString);
  };
  var setPaginationBase = function setPaginationBase($rootWrapper, key, value) {
    var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
    if ($paginationBase.length > 0) {
      var splittedURL = $paginationBase.val().split("?");
      var queryString = "";
      if (splittedURL.length > 1) {
        queryString = splittedURL[1];
      }
      var searchParams = new URLSearchParams(queryString);
      if (value.length > 0) {
        searchParams.set(key, value);
      } else {
        searchParams.delete(key);
      }
      $paginationBase.val(splittedURL[0] + "?" + searchParams.toString());
      $rootWrapper.find('input[name="paged"]').val(1);
    }
  };
  if ($(".awsm-b-job-no-more-jobs-get").length > 0) {
    $(".awsm-b-job-listings").hide();
    $(".awsm-b-job-no-more-jobs-get").slice(1).hide();
  }

  // Markup may include `awsm-selectric-loading` to prevent the native <select> flash on refresh.
  // Remove the class once all Selectric instances inside the wrap are initialized.
  $(filterSelector + ".awsm-selectric-loading").each(function () {
    var $wrap = $(this);
    var count = $wrap.find("select.awsm-b-filter-option").length;
    $wrap.data("awsmSelectricPending", count);
    if (count === 0) {
      $wrap.removeClass("awsm-selectric-loading");
      return;
    }

    // Fallback: don't keep the UI hidden forever if Selectric fails to init for any reason.
    setTimeout(function () {
      $wrap.removeClass("awsm-selectric-loading");
    }, 2000);
  });

  // Init Selectric per-select so we can vary options based on placement.
  $(filterSelector + " .awsm-b-filter-option").each(function () {
    var $selectEl = $(this);
    var $rootWrapper = $selectEl.closest(rootWrapperSelector);
    var isTopPlacement = $rootWrapper.length > 0 && !$rootWrapper.hasClass("awsm-job-form-plugin-style");
    $selectEl.selectric({
      // Use Selectric UI on mobile too; native <select multiple> is not usable on many devices.
      nativeOnMobile: false,
      disableOnMobile: false,
      multiple: {
        keepMenuOpen: true,
        separator: ", ",
        // Top placement: show only first 2 labels and then "..." (Selectric built-in).
        maxLabelEntries: isTopPlacement ? 2 : false
      },
      onInit: function onInit(select, selectric) {
        var id = select.id;
        if (selectric && selectric.elements && selectric.elements.input) {
          var $input = $(selectric.elements.input);
          $(select).attr("id", "selectric-" + id);
          $input.attr("id", id);
        }
        var $select = $(select);
        var $filterWrap = $select.closest(filterSelector);
        if ($filterWrap.hasClass("awsm-selectric-loading")) {
          var pending = parseInt($filterWrap.data("awsmSelectricPending"), 10);
          if (isNaN(pending)) {
            pending = 0;
          }
          pending = Math.max(pending - 1, 0);
          $filterWrap.data("awsmSelectricPending", pending);
          if (pending === 0) {
            $filterWrap.removeClass("awsm-selectric-loading");
          }
        }
        setTimeout(function () {
          // Only multi-select dropdowns use the "All" sync behavior.
          if ($select.prop("multiple")) {
            syncAllOptionFromUrl($select);
            forceAllLabel($select);
          }

          // Sync URL for pre-selected values on page load (from block selected_terms or URL params).
          var $rootWrapper = $select.closest(rootWrapperSelector);
          var currentSpec = $select.closest(".awsm-b-filter-item").data("filter");
          if (currentSpec) {
            var slugString = "";
            if ($select.prop("multiple")) {
              var $allOption = $select.find("option").eq(0);
              var slugs = $select.find("option:selected").not($allOption).map(function () {
                return $(this).data("slug");
              }).get().filter(Boolean);
              slugString = slugs.join(",");
            } else {
              var $selected = $select.find("option:selected");
              if ($selected.index() > 0) {
                slugString = $selected.data("slug") || "";
              }
            }
            if (slugString) {
              setPaginationBase($rootWrapper, currentSpec, slugString);
              updateAwsmQuery($rootWrapper, currentSpec, slugString);
            }
          }
        }, 0);
      },
      arrowButtonMarkup: '<span class="awsm-selectric-arrow-drop">&#x25be;</span>',
      customClass: {
        prefix: "awsm-selectric",
        camelCase: false
      }
    });
  });
  $(document).on("change", filterSelector + " .awsm-b-filter-option", function () {
    var $select = $(this);
    handleAwsmMultiFilter($select);
    setTimeout(function () {
      forceAllLabel($select);
    }, 0);

    // Keep dropdown open only if Selectric exists (desktop)
    if ($select.prop("multiple") && $select.data("selectric")) {
      $select.selectric("open");
    }
  });
  function handleAwsmMultiFilter($select) {
    var $options = $select.find("option");
    var $all = $options.eq(0); // "All"
    var $others = $options.slice(1); // Individual options

    var $rootWrapper = $select.closest(rootWrapperSelector);
    var currentSpec = $select.closest(".awsm-b-filter-item").data("filter");
    var slugs = [];

    // CURRENT state
    var isAllSelected = $all.is(":selected");
    var selectedOthersCount = $others.filter(":selected").length;
    var totalOthersCount = $others.length;

    // PREVIOUS state
    var wasAllSelected = $select.data("wasAllSelected") === true;

    /* =================================================
    SINGLE SELECT DROPDOWN
    ================================================= */
    if (!$select.prop("multiple")) {
      if (isAllSelected) {
        $options.prop("selected", false);
        $all.prop("selected", true);
        setPaginationBase($rootWrapper, currentSpec, "");
        updateAwsmQuery($rootWrapper, currentSpec, "");
        awsmJobFilters($rootWrapper);
        $select.data("wasAllSelected", true);
        $select.selectric("refresh");
        return;
      }

      // Single selection (not All)
      var selectedSlug = $options.filter(":selected").data("slug") || "";
      setPaginationBase($rootWrapper, currentSpec, selectedSlug);
      updateAwsmQuery($rootWrapper, currentSpec, selectedSlug);
      awsmJobFilters($rootWrapper);
      $select.data("wasAllSelected", false);
      $select.selectric("refresh");
      return;
    }

    /* =================================================
    MULTI SELECT DROPDOWN
    ================================================= */
    // CASE 1: User UNCHECKED "All" → Clear everything
    if (wasAllSelected && !isAllSelected) {
      $options.prop("selected", false);
      $select.selectric("refresh");
      setPaginationBase($rootWrapper, currentSpec, "");
      updateAwsmQuery($rootWrapper, currentSpec, "");
      awsmJobFilters($rootWrapper);
      $select.data("wasAllSelected", false);
      return;
    }

    // CASE 2: User CLICKED "All" → Select everything
    if (isAllSelected && !wasAllSelected) {
      $options.prop("selected", true);
      slugs = $others.map(function () {
        return $(this).data("slug");
      }).get();
    }

    // CASE 3: User selected all individuals manually → Auto-check All
    else if (!isAllSelected && selectedOthersCount === totalOthersCount) {
      $all.prop("selected", true);
      slugs = $others.map(function () {
        return $(this).data("slug");
      }).get();
    }

    // CASE 4: Normal individual selection
    else if (selectedOthersCount > 0) {
      $all.prop("selected", false);
      slugs = $others.filter(":selected").map(function () {
        return $(this).data("slug");
      }).get();
    }

    // CASE 5: Nothing selected → Reset
    else {
      $options.prop("selected", false);
      $select.selectric("refresh");
      setPaginationBase($rootWrapper, currentSpec, "");
      updateAwsmQuery($rootWrapper, currentSpec, "");
      awsmJobFilters($rootWrapper);
      $select.data("wasAllSelected", false);
      return;
    }

    // Save state
    $select.data("wasAllSelected", $all.is(":selected"));

    // Sync Selectric UI
    $select.selectric("refresh");

    // Apply filters
    var slugString = slugs.join(",");
    setPaginationBase($rootWrapper, currentSpec, slugString);
    updateAwsmQuery($rootWrapper, currentSpec, slugString);
    awsmJobFilters($rootWrapper);
  }
  function syncAllOptionFromUrl($select) {
    // Single-select dropdowns should not sync the "All" option.
    if (!$select.prop("multiple")) {
      return;
    }
    var $options = $select.find("option");
    var $all = $options.eq(0);
    var $others = $options.slice(1);
    var totalOthers = $others.length;
    var selectedOthers = $others.filter(":selected").length;
    if (totalOthers > 0 && selectedOthers === totalOthers) {
      $all.prop("selected", true);
    } else {
      $all.prop("selected", false);
    }
    $select.selectric("refresh");
    $select.data("wasAllSelected", $all.is(":selected"));
  }
  function forceAllLabel($select) {
    var selectric = $select.data("selectric");
    var $allOption = $select.find("option").first();
    if (selectric && $allOption.is(":selected")) {
      // Selectric omits empty-value options in multi-select label; ensure "All" shows.
      selectric.elements.label.text($allOption.text());
    }
  }
  function updateAwsmQuery($rootWrapper, spec, value) {
    if (!awsmJobsPublic.deep_linking.spec) {
      return;
    }
    var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
    updateQuery(spec, value, $paginationBase.val());
  }
  $(filterSelector + " .awsm-b-job-search-btn").on("click", function () {
    searchJobs($(this));
  });
  $(filterSelector + " .awsm-b-job-search-close-btn").on("click", function () {
    var $elem = $(this);
    $elem.parents(rootWrapperSelector).find(".awsm-b-job-search").val("");
    searchJobs($elem);
  });
  $(filterSelector + " .awsm-b-job-search").on("keypress", function (e) {
    if (e.which == 13) {
      e.preventDefault();
      searchJobs($(this));
    }
  });

  /* ========== Job Listings Load More ========== */
  $(wrapperSelector).on("click", ".awsm-b-jobs-pagination .awsm-b-load-more-btn, .awsm-b-jobs-pagination a.page-numbers", function (e) {
    e.preventDefault();
    var $triggerElem = $(this);
    var isDefaultPagination = $triggerElem.hasClass("awsm-b-load-more-btn");
    var paged = 1;
    var wpData = [];
    var $mainContainer = $triggerElem.parents(rootWrapperSelector);
    var $listingsContainer = $mainContainer.find(wrapperSelector);
    var $listingsrowContainer = $listingsContainer.find(sectionSelector);
    var $paginationWrapper = $triggerElem.parents(".awsm-b-jobs-pagination");
    var listings = $listingsContainer.data("listings");
    var lang = $listingsContainer.data("lang");
    var searchQuery = $listingsContainer.data("search");
    var order_by = $listingsContainer.data("awsm-order-by");

    /* added for block */
    var layout = $listingsContainer.data("awsm-layout");
    var hide_expired_jobs = $listingsContainer.data("awsm-hide-expired-jobs");
    /* let selected_terms = $listingsContainer.data(
    	'awsm-selected-terms'
    ); */
    var other_options = $listingsContainer.data("awsm-other-options");
    var show_spec_icon = $listingsContainer.data("awsm-spec-icons");
    /* end */

    if (isDefaultPagination) {
      $triggerElem.prop("disabled", true);
      paged = $triggerElem.data("page");
      paged = typeof paged === "undefined" ? 1 : paged;
    } else {
      $triggerElem.parents(".page-numbers").find(".page-numbers").removeClass("current").removeAttr("aria-current");
      $triggerElem.addClass("current").attr("aria-current", "page");
    }
    $paginationWrapper.addClass("awsm-b-jobs-pagination-loading");

    // filters
    var $filterForm = $mainContainer.find(filterSelector + " form");
    if (filterCheck($filterForm)) {
      var $filterOption = $filterForm.find(".awsm-b-filter-option");
      wpData = $filterOption.serializeArray();
    }
    var specsList = {};
    $filterForm.find(".awsm-filter-checkbox:checked").each(function () {
      var $checkbox = $(this);
      var taxonomy = $checkbox.data("taxonomy"); // Get taxonomy from data attribute
      var termId = $checkbox.data("term-id"); // Get term ID from data attribute

      if (taxonomy && termId) {
        if (!specsList[taxonomy]) {
          specsList[taxonomy] = []; // Initialize array for this taxonomy
        }
        specsList[taxonomy].push(termId); // Add term ID to the array
      }
    });
    for (var taxonomy in specsList) {
      if (specsList.hasOwnProperty(taxonomy)) {
        specsList[taxonomy].forEach(function (termId) {
          wpData.push({
            name: "awsm_job_specs_list[".concat(taxonomy, "][]"),
            // Add taxonomy as part of the key
            value: termId
          });
        });
      }
    }
    if (!isDefaultPagination) {
      var paginationBaseURL = $triggerElem.attr("href");
      var splittedURL = paginationBaseURL.split("?");
      var queryString = "";
      var isHomepage = window.awsmJobsPublic && awsmJobsPublic.is_homepage;
      var pageKey = isHomepage ? "page" : "paged";
      if (splittedURL.length > 1) {
        var searchParams = new URLSearchParams(splittedURL[1]);
        paged = searchParams.get(pageKey) || searchParams.get(pageKey === "page" ? "paged" : "page");
        if (!paged) {
          paged = 1;
        }
        searchParams.delete("page");
        searchParams.delete("paged");
        if (searchParams.toString().length > 0) {
          queryString = "?" + searchParams.toString();
        }
      } else {
        var pageMatch = paginationBaseURL.match(/\/page\/(\d+)\/?/);
        if (pageMatch) {
          paged = pageMatch[1];
        } else {
          paged = 1;
        }
      }
      paginationBaseURL = splittedURL[0] + queryString;
      wpData.push({
        name: "awsm_pagination_base",
        value: splittedURL[0] + queryString
      });
      if (awsmJobsPublic.deep_linking.pagination) {
        updateQuery(pageKey, paged, paginationBaseURL);
      }
    }

    // taxonomy archives
    if (awsmJobsPublic.is_tax_archive) {
      var taxonomy = $listingsContainer.data("taxonomy");
      var termId = $listingsContainer.data("termId");
      if (typeof taxonomy !== "undefined" && typeof termId !== "undefined") {
        wpData.push({
          name: "awsm_job_spec[" + taxonomy + "]",
          value: termId
        });
      }
    }
    wpData.push({
      name: "action",
      value: "block_loadmore"
    }, {
      name: "paged",
      value: paged
    });
    if (typeof listings !== "undefined") {
      wpData.push({
        name: "listings_per_page",
        value: listings
      });
    }

    /* added for block */
    if (typeof layout !== "undefined") {
      wpData.push({
        name: "awsm-layout",
        value: layout
      });
    }
    if (typeof hide_expired_jobs !== "undefined") {
      wpData.push({
        name: "awsm-hide-expired-jobs",
        value: hide_expired_jobs
      });
    }

    /* if ( selected_terms ) {
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
    } */

    if (typeof other_options !== "undefined") {
      wpData.push({
        name: "awsm-other-options",
        value: other_options
      });
    }
    if (typeof show_spec_icon !== "undefined") {
      wpData.push({
        name: "awsm-spec-icons",
        value: show_spec_icon
      });
    }
    if (typeof order_by !== "undefined") {
      wpData.push({
        name: "awsm-order-by",
        value: order_by
      });
    }
    if (typeof lang !== "undefined") {
      wpData.push({
        name: "lang",
        value: lang
      });
    }
    if (typeof searchQuery !== "undefined") {
      wpData.push({
        name: "jq",
        value: searchQuery
      });
    }
    if (awsmJobsPublic.block_nonce) {
      wpData.push({
        name: "awsm_block_nonce",
        value: awsmJobsPublic.block_nonce
      });
    }
    $(document).trigger("awsmjobs_block_load_more", [$listingsContainer, wpData]);
    var listingsData = getListingsData($listingsContainer);
    if (listingsData.length > 0) {
      wpData = wpData.concat(listingsData);
    }
    wpData = normalizeAndDedupeParams(wpData);

    // now, handle ajax
    $.ajax({
      url: awsmJobsPublic.ajaxurl,
      data: $.param(wpData),
      type: "POST",
      beforeSend: function beforeSend() {
        if (isDefaultPagination) {
          $triggerElem.text(awsmJobsPublic.i18n.loading_text);
        } else {
          $listingsContainer.addClass("awsm-b-jobs-loading");
        }
      }
    }).done(function (response) {
      if (response.data.html) {
        var effectDuration = $paginationWrapper.data("effectDuration");
        $paginationWrapper.remove();
        if (isDefaultPagination) {
          $listingsrowContainer.append(response.data.html);
        } else {
          $listingsrowContainer.html(response.data.html);
          $listingsContainer.removeClass("awsm-b-jobs-loading");
          if (typeof effectDuration !== "undefined") {
            effectDuration = isNaN(effectDuration) ? effectDuration : Number(effectDuration);
            $("html, body").animate({
              scrollTop: $mainContainer.offset().top - 25
            }, effectDuration);
          }
        }
      } else {
        $triggerElem.remove();
      }
      $(document).trigger("awsmjobs_load_more", [$triggerElem, response.data.html]);
    }).fail(function (xhr) {
      // eslint-disable-next-line no-console
      console.log(xhr);
    });
  });

  /**
   * Handle the filters toggle button in the job listing.
   */
  $(document).on("click", ".awsm-b-filter-toggle", function (e) {
    e.preventDefault();
    var $elem = $(this);
    $elem.toggleClass("awsm-on");
    if ($elem.hasClass("awsm-on")) {
      $elem.attr("aria-pressed", "true");
    } else {
      $elem.attr("aria-pressed", "false");
    }
    var $parent = $elem.parent();
    $parent.find(".awsm-b-filter-items").slideToggle();
  });

  /**
   * Handle the responsive styles for filters in the job listing when search is enabled.
   */
  function filtersResponsiveStylesHandler() {
    var $filtersWrap = $(".awsm-b-filter-wrap").not(".awsm-b-no-search-filter-wrap");
    $filtersWrap.each(function () {
      var $wrapper = $(this);
      var $items = $wrapper.find(".awsm-b-filter-item");
      if ($items.length === 0) {
        $wrapper.removeClass("awsm-b-full-width-search-filter-wrap");
        return;
      }
      var firstOffset = $items.first().offset();
      var lastOffset = $items.last().offset();
      if (!firstOffset || !lastOffset) {
        $wrapper.removeClass("awsm-b-full-width-search-filter-wrap");
        return;
      }
      var filterFirstTop = firstOffset.top;
      var filterLastTop = lastOffset.top;
      if (window.innerWidth < 768) {
        $wrapper.removeClass("awsm-b-full-width-search-filter-wrap");
        return;
      }
      if (filterLastTop > filterFirstTop) {
        $wrapper.addClass("awsm-b-full-width-search-filter-wrap");
      }
    });
  }
  if ($(".awsm-b-filter-wrap").not(".awsm-b-no-search-filter-wrap").length > 0) {
    filtersResponsiveStylesHandler();
    $(window).on("resize", filtersResponsiveStylesHandler);
  }
});
}();
/******/ })()
;
//# sourceMappingURL=view.js.map