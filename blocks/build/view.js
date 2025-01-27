/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/*!*********************!*\
  !*** ./src/view.js ***!
  \*********************/


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
    parsedListingsAttrs.push('sort');
    /* end */
    $(document).trigger('awsmJobBlockListingsData', [parsedListingsAttrs]);
    var dataAttrs = $wrapper.data();
    $.each(dataAttrs, function (dataAttr, value) {
      if ($.inArray(dataAttr, parsedListingsAttrs) === -1) {
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
    var formData = $filterForm.serializeArray();
    var listings = $wrapper.data('listings');
    var specs = $wrapper.data('specs');
    var sortFilter = $rootWrapper.find('.awsm-job-sort-filter').val();

    /* added for block */
    var layout = $wrapper.data('awsm-layout');
    var hide_expired_jobs = $wrapper.data('awsm-hide-expired-jobs');
    var selected_terms = $wrapper.data('awsm-selected-terms');
    var other_options = $wrapper.data('awsm-other-options');
    var listings_total = $wrapper.data('awsm-listings-total');
    var sort = $wrapper.data('sort');
    formData.push({
      name: 'listings_per_page',
      value: listings
    });
    formData.push({
      name: 'listings_per_page',
      value: listings
    });
    if (typeof sortFilter !== 'undefined' && sortFilter !== '') {
      formData.push({
        name: 'filter_sort',
        value: sortFilter
      });
    } else if (typeof sort !== 'undefined') {
      formData.push({
        name: 'filter_sort',
        value: sort
      });
    }
    if (typeof specs !== 'undefined') {
      formData.push({
        name: 'shortcode_specs',
        value: specs
      });
    }

    /* added for block */
    if (typeof layout !== 'undefined') {
      formData.push({
        name: 'awsm-layout',
        value: layout
      });
    }
    if (selected_terms) {
      if (typeof selected_terms === 'string') {
        try {
          // Parse the JSON string into an object
          selected_terms = JSON.parse(selected_terms);
        } catch (error) {
          console.error("Failed to parse selected_terms JSON:", error);
          selected_terms = {}; // Fallback to an empty object
        }
      }

      // Push to wpData
      formData.push({
        name: 'awsm-selected-terms',
        value: JSON.stringify(selected_terms) // Send as JSON string
      });
    }
    if (typeof hide_expired_jobs !== 'undefined') {
      formData.push({
        name: 'awsm-hide-expired-jobs',
        value: hide_expired_jobs
      });
    }
    if (typeof other_options !== 'undefined') {
      formData.push({
        name: 'awsm-other-options',
        value: other_options
      });
    }
    if (typeof listings_total !== 'undefined') {
      formData.push({
        name: 'awsm-listings-total',
        value: listings_total
      });
    }
    var listingsData = getListingsData($wrapper);
    if (listingsData.length > 0) {
      formData = formData.concat(listingsData);
    }

    // Trigger custom event to provide formData
    $(document).trigger('awsmJobBlockFiltersFormData', [$wrapper, formData]);
    if (triggerFilter) {
      // stop the duplicate requests
      triggerFilter = false;

      // now, make the request
      $.ajax({
        url: $filterForm.attr('action'),
        beforeSend: function beforeSend() {
          $wrapper.addClass('awsm-jobs-loading');
        },
        data: formData,
        type: $filterForm.attr('method')
      }).done(function (data) {
        $rowWrapper.html(data);
        var $searchControl = $rootWrapper.find('.awsm-b-job-search');
        if ($searchControl.length > 0) {
          if ($searchControl.val().length > 0) {
            $rootWrapper.find('.awsm-b-job-search-btn').addClass('awsm-job-hide');
            $rootWrapper.find('.awsm-b-job-search-close-btn').removeClass('awsm-job-hide');
          } else {
            $rootWrapper.find('.awsm-b-job-search-btn').removeClass('awsm-job-hide');
            $rootWrapper.find('.awsm-b-job-search-close-btn').addClass('awsm-job-hide');
          }
        }
        $(document).trigger('awsmjobs_filtered_listings', [$rootWrapper, data]);
      }).fail(function (xhr) {
        // eslint-disable-next-line no-console
        console.log(xhr);
      }).always(function () {
        $wrapper.removeClass('awsm-jobs-loading');
        triggerFilter = true;
      });
    }
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
  if ($(rootWrapperSelector).length > 0) {
    $(rootWrapperSelector).each(function () {
      var $currentWrapper = $(this);
      var $filterForm = $currentWrapper.find(filterSelector + ' form');
      if (awsmJobsPublic.is_search.length > 0 || filterCheck($filterForm)) {
        triggerFilter = true;
        awsmJobFilters($currentWrapper);
      }
    });
  }
  if ($(rootWrapperSelector).length > 0) {
    $(rootWrapperSelector).each(function () {
      var $currentWrapper = $(this);
      var $filterForm = $currentWrapper.find(filterSelector + ' form');
      var searchParams = new URLSearchParams(window.location.search);
      var hasFiltersInURL = false;
      if (searchParams.toString().length > 0) {
        hasFiltersInURL = true;
      }
      if (hasFiltersInURL || filterCheck($filterForm)) {
        triggerFilter = true;
        awsmJobFilters($currentWrapper);
      }
    });
  }
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
    var $selected = $elem.find('option:selected');
    var $rootWrapper = $elem.parents(rootWrapperSelector);
    var currentSpec = $elem.parents('.awsm-b-filter-item').data('filter');
    /* var slug = $selected.data('slug'); */

    var slugs = [];
    $selected.each(function () {
      var slug = $(this).data('slug');
      if (slug) {
        slugs.push(slug);
      }
    });
    var slugString = slugs.length > 0 ? slugs.join(',') : '';
    if ($('.awsm-b-job-listings').length > 0) {
      $rootWrapper.find('.awsm-b-job-no-more-jobs-get').hide();
    }
    /* slug = typeof slug !== 'undefined' ? slug : ''; */
    /* setPaginationBase($rootWrapper, currentSpec, slug); */
    setPaginationBase($rootWrapper, currentSpec, slugString);
    if (awsmJobsPublic.deep_linking.spec) {
      var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
      /* updateQuery(currentSpec, slug, $paginationBase.val()); */
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
  $(wrapperSelector + ' .awsm-job-sort-filter').on('change', function (e) {
    //$('.awsm-job-sort-filter').on('change', function() {
    var $elem = $(this);
    var $rootWrapper = $elem.parents(rootWrapperSelector);
    var sortValue = $rootWrapper.find('select.awsm-job-sort-filter').val();
    setPaginationBase($rootWrapper, 'sort', sortValue);
    if (awsmJobsPublic.deep_linking.search) {
      var $paginationBase = $rootWrapper.find('input[name="awsm_pagination_base"]');
      updateQuery('sort', sortValue, $paginationBase.val());
    }
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

  /* ========== Job Listings Load More ========== */
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
    var totalPosts = $listingsContainer.data('total-posts'); // Assuming this is passed via data
    var specs = $listingsContainer.data('specs');
    var lang = $listingsContainer.data('lang');
    var searchQuery = $listingsContainer.data('search');
    var sort = $listingsContainer.data('sort');

    /* added for block */
    var layout = $listingsContainer.data('awsm-layout');
    var hide_expired_jobs = $listingsContainer.data('awsm-hide-expired-jobs');
    var selected_terms = $listingsContainer.data('awsm-selected-terms');
    var other_options = $listingsContainer.data('awsm-other-options');
    /* end */

    if (isDefaultPagination) {
      $triggerElem.prop('disabled', true);
      paged = $triggerElem.data('page');
      paged = typeof paged == 'undefined' ? 1 : paged;
    } else {
      $triggerElem.parents('.page-numbers').find('.page-numbers').removeClass('current').removeAttr('aria-current');
      $triggerElem.addClass('current').attr('aria-current', 'page');
    }
    $paginationWrapper.addClass('awsm-b-jobs-pagination-loading');

    // filters
    var $filterForm = $mainContainer.find(filterSelector + ' form');
    if (filterCheck($filterForm)) {
      var $filterOption = $filterForm.find('.awsm-b-filter-option');
      wpData = $filterOption.serializeArray();
    }
    var specsList = {};
    $filterForm.find('.awsm-filter-checkbox:checked').each(function () {
      var $checkbox = $(this);
      var taxonomy = $checkbox.data('taxonomy'); // Get taxonomy from data attribute
      var termId = $checkbox.data('term-id'); // Get term ID from data attribute

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
      var paginationBaseURL = $triggerElem.attr('href');
      var splittedURL = paginationBaseURL.split('?');
      var queryString = '';
      if (splittedURL.length > 1) {
        var searchParams = new URLSearchParams(splittedURL[1]);
        paged = searchParams.get('paged');
        searchParams.delete('paged');
        if (searchParams.toString().length > 0) {
          queryString = '?' + searchParams.toString();
        }
      }
      paginationBaseURL = splittedURL[0] + queryString;
      wpData.push({
        name: 'awsm_pagination_base',
        value: splittedURL[0] + queryString
      });
      if (awsmJobsPublic.deep_linking.pagination) {
        updateQuery('paged', paged, paginationBaseURL);
      }
    }

    // taxonomy archives
    if (awsmJobsPublic.is_tax_archive) {
      var taxonomy = $listingsContainer.data('taxonomy');
      var termId = $listingsContainer.data('termId');
      if (typeof taxonomy !== 'undefined' && typeof termId !== 'undefined') {
        wpData.push({
          name: 'awsm_job_spec[' + taxonomy + ']',
          value: termId
        });
      }
    }
    wpData.push({
      name: 'action',
      value: 'block_loadmore'
    }, {
      name: 'paged',
      value: paged
    });
    if (typeof listings !== 'undefined') {
      wpData.push({
        name: 'listings_per_page',
        value: listings
      });
    }
    if (typeof specs !== 'undefined') {
      wpData.push({
        name: 'shortcode_specs',
        value: specs
      });
    }

    /* added for block */
    if (typeof layout !== 'undefined') {
      wpData.push({
        name: 'awsm-layout',
        value: layout
      });
    }
    if (typeof hide_expired_jobs !== 'undefined') {
      wpData.push({
        name: 'awsm-hide-expired-jobs',
        value: hide_expired_jobs
      });
    }
    if (selected_terms) {
      if (typeof selected_terms === 'string') {
        try {
          // Parse the JSON string into an object
          selected_terms = JSON.parse(selected_terms);
        } catch (error) {
          console.error("Failed to parse selected_terms JSON:", error);
          selected_terms = {}; // Fallback to an empty object
        }
      }

      // Push to wpData
      wpData.push({
        name: 'awsm-selected-terms',
        value: JSON.stringify(selected_terms) // Send as JSON string
      });
    }
    if (typeof other_options !== 'undefined') {
      wpData.push({
        name: 'awsm-other-options',
        value: other_options
      });
    }
    if (typeof listings_total !== 'undefined') {
      wpData.push({
        name: 'awsm-listings-total',
        value: listings_total
      });
    }
    if (typeof lang !== 'undefined') {
      wpData.push({
        name: 'lang',
        value: lang
      });
    }
    if (typeof searchQuery !== 'undefined') {
      wpData.push({
        name: 'jq',
        value: searchQuery
      });
    }
    if (typeof sort !== 'undefined') {
      wpData.push({
        name: 'filter_sort',
        value: sort
      });
    }
    $(document).trigger('awsmjobs_block_load_more', [$listingsContainer, wpData]);
    var listingsData = getListingsData($listingsContainer);
    if (listingsData.length > 0) {
      wpData = wpData.concat(listingsData);
    }

    // now, handle ajax
    $.ajax({
      url: awsmJobsPublic.ajaxurl,
      data: $.param(wpData),
      type: 'POST',
      beforeSend: function beforeSend() {
        if (isDefaultPagination) {
          $triggerElem.text(awsmJobsPublic.i18n.loading_text);
        } else {
          $listingsContainer.addClass('awsm-jobs-loading');
        }
      }
    }).done(function (data) {
      if (data) {
        var effectDuration = $paginationWrapper.data('effectDuration');
        $paginationWrapper.remove();
        if (isDefaultPagination) {
          $listingsrowContainer.append(data);
        } else {
          $listingsrowContainer.html(data);
          $listingsContainer.removeClass('awsm-jobs-loading');
          if (typeof effectDuration !== 'undefined') {
            effectDuration = isNaN(effectDuration) ? effectDuration : Number(effectDuration);
            $('html, body').animate({
              scrollTop: $mainContainer.offset().top - 25
            }, effectDuration);
          }
        }
      } else {
        $triggerElem.remove();
      }
      $(document).trigger('awsmjobs_load_more', [$triggerElem, data]);
    }).fail(function (xhr) {
      // eslint-disable-next-line no-console
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
/******/ })()
;
//# sourceMappingURL=view.js.map