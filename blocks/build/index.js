/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/edit.js":
/*!*********************!*\
  !*** ./src/edit.js ***!
  \*********************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ Edit; }
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./editor.scss */ "./src/editor.scss");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _inspector__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./inspector */ "./src/inspector.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_7__);


function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */


/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
// import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */


/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */




function Edit(props) {
  var filter_options = props.attributes.filter_options,
    setAttributes = props.setAttributes;
  var blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.useBlockProps)();
  var specifications = awsmJobsAdmin.awsm_filters_block;
  specifications = specifications.filter(function (spec) {
    if (typeof filter_options !== 'undefined' && filter_options.includes(spec.key)) {
      return spec;
    }
  });

  // Event handler to ignore clicks
  var handleClick = function handleClick(event) {
    event.preventDefault();
    event.stopPropagation();
  };
  var handleResize = function handleResize() {
    var filtersWraps = document.querySelectorAll('.awsm-b-filter-wrap:not(.awsm-no-search-filter-wrap)');
    filtersWraps.forEach(function (wrapper) {
      var filterItems = wrapper.querySelectorAll('.awsm-b-filter-item');
      if (filterItems.length > 0) {
        var filterFirstTop = filterItems[0].getBoundingClientRect().top;
        var filterLastTop = filterItems[filterItems.length - 1].getBoundingClientRect().top;
        if (window.innerWidth < 768) {
          wrapper.classList.remove('awsm-b-full-width-search-filter-wrap');
          return;
        }
        if (filterLastTop > filterFirstTop) {
          wrapper.classList.add('awsm-b-full-width-search-filter-wrap');
        }
      }
    });
  };
  var _checkElement = function checkElement() {
    var dynamicElement = document.querySelector('.awsm-b-job-wrap');
    if (dynamicElement) {
      handleResize();
    } else {
      setTimeout(_checkElement, 300);
    }
  };
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_7__.useEffect)(function () {
    _checkElement();
    handleResize();
    return function () {
      window.removeEventListener('resize', handleResize);
    };
  }, []);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_7__.useEffect)(function () {}, [props.attributes.enable_job_filter, props.attributes.filter_options]);
  var checkFilters = function checkFilters() {
    var wrapper = document.querySelector('#block-' + props.clientId + ' .awsm-b-filter-wrap');
    if (!wrapper) {
      return;
    }
    var filterItems = document.querySelectorAll('#block-' + props.clientId + ' .awsm-b-filter-item');
    if (filterItems.length > 0) {
      var filterFirstTop = filterItems[0].getBoundingClientRect().top;
      var filterLastTop = filterItems[filterItems.length - 1].getBoundingClientRect().top;
      if (window.innerWidth < 768) {
        wrapper.classList.remove('awsm-b-full-width-search-filter-wrap');
        return;
      }
      if (filterLastTop > filterFirstTop) {
        wrapper.classList.add('awsm-b-full-width-search-filter-wrap');
      }
    }
  };
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_7__.useEffect)(function () {
    var observer = new MutationObserver(function () {
      checkFilters();
    });
    var observeItem = document.querySelector('#block-' + props.clientId);
    if (observeItem) {
      observer.observe(observeItem, {
        childList: true,
        subtree: true
      });
    }
    (function () {
      observer.disconnect();
    });
  }, []);
  return (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)("div", _objectSpread(_objectSpread({}, blockProps), {}, {
    onClick: handleClick
  }), (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)(_inspector__WEBPACK_IMPORTED_MODULE_6__["default"], _objectSpread({}, props)), (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_5___default()), {
    block: "wp-job-openings/blocks",
    attributes: props.attributes
  }));
}

/***/ }),

/***/ "./src/icon.js":
/*!*********************!*\
  !*** ./src/icon.js ***!
  \*********************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

var icon = {
  block: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("svg", {
    width: "16",
    height: "16",
    viewBox: "0 0 16 16",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("g", {
    "clip-path": "url(#clip0_111_1312)"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("path", {
    "fill-rule": "evenodd",
    "clip-rule": "evenodd",
    d: "M8 0C12.4183 0 16 3.58172 16 8C16 12.4183 12.4183 16 8 16C3.58172 16 0 12.4183 0 8C0 3.58172 3.58172 0 8 0ZM6.92211 4.3475H4.69508V11.7011H6.77263V10.8088H9.23937V11.7011H11.3628V4.3475H9.07407L9.23937 8.94386L8.02895 9.85267L6.77263 8.94386L6.92211 4.3475Z",
    fill: "black"
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("defs", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("clipPath", {
    id: "clip0_111_1312"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("rect", {
    width: "16",
    height: "16",
    fill: "white"
  }))))
};
/* harmony default export */ __webpack_exports__["default"] = (icon);

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/style.scss");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./save */ "./src/save.js");
/* harmony import */ var _icon__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./icon */ "./src/icon.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./block.json */ "./src/block.json");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__);
/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */


/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */


/**
 * Internal dependencies
 */






/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_5__.name, {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Job Listings(Beta)', 'wp-job-openings'),
  // Block title.
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('Add and customise your Job Listing layout', 'wp-job-openings'),
  // Block description
  icon: _icon__WEBPACK_IMPORTED_MODULE_4__["default"].block,
  // Block icon
  category: 'widgets',
  // Block category,
  keywords: [(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('jobs listings', 'wp-job-openings'), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('add jobs', 'wp-job-openings'), (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_6__.__)('job application', 'wp-job-openings')],
  // Access the block easily with keyword aliases
  /**
   * @see ./edit.js
   */
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  /**
   * @see ./save.js
   */
  save: _save__WEBPACK_IMPORTED_MODULE_3__["default"]
});

/***/ }),

/***/ "./src/inspector.js":
/*!**************************!*\
  !*** ./src/inspector.js ***!
  \**************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__);




function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }





var WidgetInspectorControls = function WidgetInspectorControls(props) {
  var _props$attributes = props.attributes,
    search = _props$attributes.search,
    placement = _props$attributes.placement,
    filter_options = _props$attributes.filter_options,
    pagination = _props$attributes.pagination,
    search_placeholder = _props$attributes.search_placeholder,
    hide_expired_jobs = _props$attributes.hide_expired_jobs,
    orderBy = _props$attributes.orderBy,
    listType = _props$attributes.listType,
    jobsPerPage = _props$attributes.jobsPerPage,
    layout = _props$attributes.layout,
    selectedTerms = _props$attributes.selectedTerms,
    selected_terms_main = _props$attributes.selected_terms_main,
    number_of_columns = _props$attributes.number_of_columns,
    other_options = _props$attributes.other_options,
    _props$attributes$hz_ = _props$attributes.hz_sf_border,
    hz_sf_border = _props$attributes$hz_ === void 0 ? {} : _props$attributes$hz_,
    _props$attributes$hz_2 = _props$attributes.hz_sf_border_radius,
    hz_sf_border_radius = _props$attributes$hz_2 === void 0 ? {} : _props$attributes$hz_2,
    _props$attributes$hz_3 = _props$attributes.hz_sf_padding,
    hz_sf_padding = _props$attributes$hz_3 === void 0 ? {} : _props$attributes$hz_3,
    _props$attributes$hz_4 = _props$attributes.hz_ls_border,
    hz_ls_border = _props$attributes$hz_4 === void 0 ? {} : _props$attributes$hz_4,
    _props$attributes$hz_5 = _props$attributes.hz_ls_border_radius,
    hz_ls_border_radius = _props$attributes$hz_5 === void 0 ? {} : _props$attributes$hz_5,
    _props$attributes$hz_6 = _props$attributes.hz_jl_border,
    hz_jl_border = _props$attributes$hz_6 === void 0 ? {} : _props$attributes$hz_6,
    _props$attributes$hz_7 = _props$attributes.hz_jl_border_radius,
    hz_jl_border_radius = _props$attributes$hz_7 === void 0 ? {} : _props$attributes$hz_7,
    _props$attributes$hz_8 = _props$attributes.hz_jl_padding,
    hz_jl_padding = _props$attributes$hz_8 === void 0 ? {} : _props$attributes$hz_8,
    _props$attributes$hz_9 = _props$attributes.hz_bs_border,
    hz_bs_border = _props$attributes$hz_9 === void 0 ? {} : _props$attributes$hz_9,
    _props$attributes$hz_10 = _props$attributes.hz_bs_border_radius,
    hz_bs_border_radius = _props$attributes$hz_10 === void 0 ? {} : _props$attributes$hz_10,
    _props$attributes$hz_11 = _props$attributes.hz_bs_padding,
    hz_bs_padding = _props$attributes$hz_11 === void 0 ? {} : _props$attributes$hz_11,
    hz_button_background_color = _props$attributes.hz_button_background_color,
    hz_button_text_color = _props$attributes.hz_button_text_color,
    hz_sidebar_width = _props$attributes.hz_sidebar_width,
    blockId = _props$attributes.blockId,
    filtersInitialized = _props$attributes.filtersInitialized,
    specsInitialized = _props$attributes.specsInitialized,
    setAttributes = props.setAttributes,
    clientId = props.clientId;

  // Local state for block settings
  var specifications = awsmJobsAdmin.awsm_filters_block;
  var _useState = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.useState)(false),
    _useState2 = (0,_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2__["default"])(_useState, 2),
    isProEnabled = _useState2[0],
    setIsProEnabled = _useState2[1];
  var _useState3 = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.useState)(selected_terms_main || {}),
    _useState4 = (0,_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2__["default"])(_useState3, 2),
    toggleState = _useState4[0],
    setToggleState = _useState4[1];
  var _useState5 = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.useState)(selectedTerms || {}),
    _useState6 = (0,_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_2__["default"])(_useState5, 2),
    selectedTermsState = _useState6[0],
    setSelectedTermsState = _useState6[1];
  var block_appearance_list = [];
  var block_job_listing = [];
  var block_styles_panel = [];
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.useEffect)(function () {
    if (typeof awsmJobsAdmin !== 'undefined' && awsmJobsAdmin.isProEnabled) {
      setIsProEnabled(true);
    }

    // Sync state with selectedTerms attribute
    var initialSelectedTerms = specifications.reduce(function (acc, spec) {
      acc[spec.key] = selectedTerms[spec.key] || [];
      return acc;
    }, {});
    setSelectedTermsState(initialSelectedTerms);
    setToggleState(function () {
      var initialState = Array.isArray(selected_terms_main) ? selected_terms_main.reduce(function (acc, key) {
        acc[key] = true;
        return acc;
      }, {}) : {};
      return initialState;
    });
    if (clientId && !blockId) {
      setAttributes({
        blockId: "job-block-".concat(clientId)
      });
    }

    // Ensure default filters are initialized only once
    if (!filtersInitialized && specifications.length > 0) {
      var defaultFilters = specifications.map(function (spec) {
        return {
          specKey: spec.key,
          value: 'dropdown'
        };
      });
      setAttributes({
        filter_options: defaultFilters,
        filtersInitialized: true // Mark as initialized
      });
    }
    if (Array.isArray(specifications) && specifications.length >= 2 && (!Array.isArray(other_options) || other_options.length === 0) && !specsInitialized) {
      var defaultKeys = specifications.slice(0, 2).map(function (spec) {
        return spec.key;
      });
      setAttributes({
        other_options: defaultKeys,
        specsInitialized: true
      });
    }
  }, [specifications, selectedTerms, selected_terms_main]);
  var handleTermChange = function handleTermChange(newTokens, specKey, spec) {
    setSelectedTermsState(function (prevSelectedTerms) {
      var updatedSelectedTerms = _objectSpread({}, prevSelectedTerms);
      var newTermIds = newTokens.map(function (token) {
        var term = spec.terms.find(function (t) {
          return t.name === token;
        });
        return term ? term.term_id : null;
      }).filter(function (id) {
        return id !== null;
      }); // Filter out invalid IDs

      updatedSelectedTerms[specKey] = newTermIds;
      setAttributes({
        selectedTerms: updatedSelectedTerms
      });
      return updatedSelectedTerms;
    });
  };
  var handleToggleChange = function handleToggleChange(specKey, isChecked) {
    var updatedTermsMain = (0,_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(selected_terms_main || []);
    if (isChecked) {
      // Add the specKey if it's not already in the array
      if (!updatedTermsMain.includes(specKey)) {
        updatedTermsMain.push(specKey);
      }
    } else {
      // Remove the specKey if it exists
      updatedTermsMain = updatedTermsMain.filter(function (key) {
        return key !== specKey;
      });

      // Clear the selectedTerms for the specKey when toggled off
      setSelectedTermsState(function (prevSelectedTerms) {
        var updatedSelectedTerms = _objectSpread({}, prevSelectedTerms);
        delete updatedSelectedTerms[specKey];

        // Ensure attributes are updated and re-rendered
        setAttributes({
          selectedTerms: updatedSelectedTerms,
          selected_terms_main: updatedTermsMain // Keep this consistent
        });
        return updatedSelectedTerms;
      });
    }

    // Update the toggle state for the editor reactivity
    setToggleState(function (prevState) {
      return _objectSpread(_objectSpread({}, prevState), {}, (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_1__["default"])({}, specKey, isChecked));
    });

    // Sync the selected_terms_main attribute with the editor
    setAttributes({
      selected_terms_main: updatedTermsMain
    });
  };
  var onchange_number_of_columns = function onchange_number_of_columns(value) {
    var columnsValue = parseInt(value, 10);
    setAttributes({
      number_of_columns: isNaN(columnsValue) ? 0 : columnsValue
    });
  };
  var other_options_handler = function other_options_handler(toggleValue, specKey) {
    var updated = (0,_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(other_options);
    if (toggleValue) {
      if (!updated.includes(specKey)) {
        updated.push(specKey);
      }
    } else {
      updated = updated.filter(function (key) {
        return key !== specKey;
      });
    }
    setAttributes({
      other_options: updated
    });
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__.InspectorControls, {
    group: "settings"
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Search & Filters', 'wp-job-openings'),
    initialOpen: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Enable Search & Filters', 'wp-job-openings'),
    checked: search,
    onChange: function onChange(search) {
      return setAttributes({
        search: search
      });
    }
  }), search && (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalToggleGroupControl, {
    label: "Placement",
    value: placement,
    onChange: function onChange(placement) {
      return setAttributes({
        placement: placement
      });
    },
    isBlock: true,
    __nextHasNoMarginBottom: true,
    __next40pxDefaultSize: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalToggleGroupControlOption, {
    value: "top",
    label: "Top"
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalToggleGroupControlOption, {
    value: "slide",
    label: "Side"
  })), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.TextControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Search Placeholder', 'wp-job-openings'),
    value: search_placeholder,
    onChange: function onChange(search_placeholder) {
      return setAttributes({
        search_placeholder: search_placeholder
      });
    },
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Search Jobs', 'wp-job-openings')
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)("h2", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Available Filters', 'wp-job-openings')), specifications.map(function (spec) {
    var filterOption = filter_options.find(function (option) {
      return option.specKey === spec.key;
    });

    // Check if there are multiple selected terms for the specKey
    var hasMultipleSelectedTerms = (selectedTermsState[spec.key] || []).length > 1;

    // If multiple terms are selected for this specKey, update the filter option to "checkbox"
    if (hasMultipleSelectedTerms && (filterOption === null || filterOption === void 0 ? void 0 : filterOption.value) !== 'checkbox') {
      var updatedFilters = filter_options.map(function (option) {
        return option.specKey === spec.key ? _objectSpread(_objectSpread({}, option), {}, {
          value: 'checkbox'
        }) : option;
      });
      setAttributes({
        filter_options: updatedFilters
      });
    }
    return (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)("div", {
      key: spec.key
    }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.ToggleControl, {
      label: spec.label,
      checked: filterOption !== undefined,
      onChange: function onChange(toggleValue) {
        var updatedFilters = toggleValue ? [].concat((0,_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__["default"])(filter_options), [{
          specKey: spec.key,
          value: hasMultipleSelectedTerms ? 'checkbox' : 'dropdown'
        }]) // Choose checkbox if multiple terms are selected
        : filter_options.filter(function (option) {
          return option.specKey !== spec.key;
        }); // Remove the filter

        // Update attributes to trigger re-render
        setAttributes({
          filter_options: updatedFilters
        });
      }
    }), filterOption && (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)("div", {
      className: "filters-button"
    }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.Button, {
      variant: "secondary",
      style: {
        backgroundColor: filterOption.value === 'dropdown' ? 'black' : 'initial',
        color: filterOption.value === 'dropdown' ? 'white' : 'black',
        marginRight: '10px'
      },
      size: "default",
      __next40pxDefaultSize: true,
      onClick: function onClick() {
        var updatedFilters = filter_options.map(function (option) {
          return option.specKey === spec.key ? _objectSpread(_objectSpread({}, option), {}, {
            value: 'dropdown'
          }) : option;
        });
        setAttributes({
          filter_options: updatedFilters
        }); // Update attributes
      }
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Single Select', 'wp-job-openings')), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.Button, {
      variant: "secondary",
      style: {
        backgroundColor: filterOption.value === 'checkbox' ? 'black' : 'initial',
        color: filterOption.value === 'checkbox' ? 'white' : 'black'
      },
      __next40pxDefaultSize: true,
      onClick: function onClick() {
        var updatedFilters = filter_options.map(function (option) {
          return option.specKey === spec.key ? _objectSpread(_objectSpread({}, option), {}, {
            value: 'checkbox'
          }) : option;
        });
        setAttributes({
          filter_options: updatedFilters
        }); // Update attributes
      }
    }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Multi Select', 'wp-job-openings'))));
  }))), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Layout Settings', 'wp-job-openings'),
    initialOpen: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalToggleGroupControl, {
    label: "Layout",
    value: layout,
    onChange: function onChange(layout) {
      return setAttributes({
        layout: layout
      });
    },
    isBlock: true,
    __nextHasNoMarginBottom: true,
    __next40pxDefaultSize: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalToggleGroupControlOption, {
    value: "list",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('List', 'wp-job-openings')
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalToggleGroupControlOption, {
    value: "grid",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Grid', 'wp-job-openings')
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalToggleGroupControlOption, {
    value: "stack",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Stack', 'wp-job-openings')
  })), typeof layout !== 'undefined' && layout == 'grid' && (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Columns', 'wp-job-openings'),
    value: number_of_columns,
    options: [{
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('1 Column', 'wp-job-openings'),
      value: '1'
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('2 Columns', 'wp-job-openings'),
      value: '2'
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('3 Columns', 'wp-job-openings'),
      value: '3'
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('4 Columns', 'wp-job-openings'),
      value: '4'
    }],
    onChange: function onChange(number_of_columns) {
      return onchange_number_of_columns(number_of_columns);
    }
  }), wp.hooks.doAction('after_awsm_job_appearance', block_appearance_list, props), block_appearance_list, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.RangeControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Jobs Per Page', 'wp-job-openings'),
    onChange: function onChange(sliderValue) {
      return setAttributes({
        jobsPerPage: sliderValue
      });
    },
    value: jobsPerPage,
    min: 1,
    max: 10,
    step: 1,
    withInputField: true
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Pagination', 'wp-job-openings'),
    value: pagination,
    options: [{
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Classic', 'wp-job-openings'),
      value: 'classic'
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Modern', 'wp-job-openings'),
      value: 'modern'
    }],
    onChange: function onChange(pagination) {
      return setAttributes({
        pagination: pagination
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)("h2", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)("Job Specs in the Listing", "wp-job-openings")), specifications.map(function (spec) {
    return (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.ToggleControl, {
      key: spec.key,
      label: spec.label,
      checked: Array.isArray(other_options) && other_options.includes(spec.key),
      onChange: function onChange(toggleValue) {
        return other_options_handler(toggleValue, spec.key);
      }
    });
  })), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Job Listing', 'wp-job-openings')
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalToggleGroupControl, {
    label: "List Type",
    value: listType,
    onChange: function onChange(newListType) {
      setAttributes({
        listType: newListType
      });

      // Clear all items in selectedTerms if listType is set to "all"
      if (newListType === 'all') {
        var clearedTerms = {};
        specifications.forEach(function (spec) {
          clearedTerms[spec.key] = [];
        });
        setAttributes({
          selectedTerms: clearedTerms,
          selected_terms_main: []
        });
      }
    },
    isBlock: true,
    __nextHasNoMarginBottom: true,
    __next40pxDefaultSize: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalToggleGroupControlOption, {
    value: "all",
    label: "All Jobs"
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalToggleGroupControlOption, {
    value: "filtered",
    label: "Filtered List"
  })), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)(' Display all jobs or filtered by job specifications', 'wp-job-openings')), listType === 'filtered' && (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)("h2", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Filters', 'wp-job-openings')), specifications.map(function (spec) {
    return (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)("div", {
      key: spec.key,
      className: "filter-item"
    }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.ToggleControl, {
      label: spec.label,
      checked: toggleState[spec.key] || false // Check the toggle state for the spec
      ,
      onChange: function onChange(isChecked) {
        // Handle toggle change and update attributes
        handleToggleChange(spec.key, isChecked);
      }
    }), toggleState[spec.key] && (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.FormTokenField, {
      value: (selectedTermsState[spec.key] || []).map(function (id) {
        var term = spec.terms.find(function (t) {
          return t.term_id === id;
        });
        return term ? term.name : '';
      }),
      onChange: function onChange(newTokens) {
        return handleTermChange(newTokens, spec.key, spec);
      },
      suggestions: spec.terms.map(function (term) {
        return term.name;
      }) // Suggestions are term names
      ,
      label: ""
    }));
  })), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Order By', 'wp-job-openings'),
    value: orderBy,
    options: [{
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Newest to oldest', 'wp-job-openings'),
      value: 'new_to_old'
    }, {
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Oldest to newest', 'wp-job-openings'),
      value: 'old_to_new'
    }],
    onChange: function onChange(orderBy) {
      return setAttributes({
        orderBy: orderBy
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.ToggleControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Hide Expired Jobs', 'wp-job-openings'),
    checked: hide_expired_jobs,
    onChange: function onChange(hide_expired_jobs) {
      return setAttributes({
        hide_expired_jobs: hide_expired_jobs
      });
    }
  }), wp.hooks.doAction('after_awsm_block_job_listing', block_job_listing, props), block_job_listing))), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__.InspectorControls, {
    group: "styles"
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)("div", {
    className: "hz-inspector-controls"
  }, search && (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.Fragment, null, placement === 'slide' && (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Sidebar', 'wp-job-openings'),
    initialOpen: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.RangeControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Sidebar Width', 'wp-job-openings'),
    __nextHasNoMarginBottom: true,
    min: 33.33,
    max: 80.33,
    step: 0.1,
    name: "hz_sidebar_width",
    value: parseFloat(hz_sidebar_width) || 33.33,
    onChange: function onChange(val) {
      setAttributes({
        hz_sidebar_width: val
      });
    },
    __next40pxDefaultSize: true
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.BorderControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Border', 'wp-job-openings'),
    withSlider: true,
    isCompact: true,
    value: hz_sf_border,
    onChange: function onChange(newBorder) {
      var width = newBorder === null || newBorder === void 0 ? void 0 : newBorder.width;
      setAttributes({
        hz_sf_border: _objectSpread(_objectSpread({}, newBorder), {}, {
          width: width
        })
      });
    },
    enableStyle: false
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalSpacer, null), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__.__experimentalBorderRadiusControl, {
    values: hz_sf_border_radius,
    onChange: function onChange(newRadius) {
      if (typeof newRadius === 'string') {
        var radiusObject = {
          topLeft: newRadius,
          topRight: newRadius,
          bottomRight: newRadius,
          bottomLeft: newRadius
        };
        setAttributes({
          hz_sf_border_radius: radiusObject
        });
      } else {
        setAttributes({
          hz_sf_border_radius: newRadius
        });
      }
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalSpacer, null), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.BoxControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Padding', 'wp-job-openings'),
    values: hz_sf_padding,
    onChange: function onChange(Padding) {
      setAttributes({
        hz_sf_padding: Padding
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Search and Filter Fields', 'wp-job-openings'),
    initialOpen: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.BorderControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Border', 'wp-job-openings'),
    withSlider: true,
    isCompact: true,
    value: hz_ls_border,
    __experimentalIsRenderedInSidebar: true,
    onChange: function onChange(newBorder) {
      var width = newBorder === null || newBorder === void 0 ? void 0 : newBorder.width;
      setAttributes({
        hz_ls_border: _objectSpread(_objectSpread({}, newBorder), {}, {
          width: width
        })
      });
      if (width === '0px') {
        setTimeout(function () {
          setAttributes({
            hz_ls_border: _objectSpread(_objectSpread({}, newBorder), {}, {
              width: '1px'
            })
          });
        }, 100);
      }
    },
    enableStyle: false
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalSpacer, null), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__.__experimentalBorderRadiusControl, {
    values: hz_ls_border_radius,
    onChange: function onChange(newRadius) {
      if (typeof newRadius === 'string') {
        var radiusObject = {
          topLeft: newRadius,
          topRight: newRadius,
          bottomRight: newRadius,
          bottomLeft: newRadius
        };
        setAttributes({
          hz_ls_border_radius: radiusObject
        });
      } else {
        setAttributes({
          hz_ls_border_radius: newRadius
        });
      }
    }
  }))), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Job Listing', 'wp-job-openings'),
    initialOpen: true
  }, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.BorderControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Border', 'wp-job-openings'),
    withSlider: true,
    isCompact: true,
    value: hz_jl_border // Use a valid default object
    ,
    __experimentalIsRenderedInSidebar: true,
    onChange: function onChange(newBorder) {
      var width = newBorder === null || newBorder === void 0 ? void 0 : newBorder.width;
      setAttributes({
        hz_jl_border: _objectSpread(_objectSpread({}, newBorder), {}, {
          width: width
        })
      });
      if (width === '0px') {
        setTimeout(function () {
          setAttributes({
            hz_jl_border: _objectSpread(_objectSpread({}, newBorder), {}, {
              width: '1px'
            })
          });
        }, 100);
      }
    },
    enableStyle: false
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalSpacer, null), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__.__experimentalBorderRadiusControl, {
    values: hz_jl_border_radius,
    onChange: function onChange(newRadius) {
      if (typeof newRadius === 'string') {
        var radiusObject = {
          topLeft: newRadius,
          topRight: newRadius,
          bottomRight: newRadius,
          bottomLeft: newRadius
        };
        setAttributes({
          hz_jl_border_radius: radiusObject
        });
      } else {
        setAttributes({
          hz_jl_border_radius: newRadius
        });
      }
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.BoxControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Padding', 'wp-job-openings'),
    values: hz_jl_padding // Ensure there is a fallback value
    ,
    onChange: function onChange(Padding) {
      setAttributes({
        hz_jl_padding: Padding
      });
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.PanelRow, null, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)("strong", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Button', 'wp-job-openings'))), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.BorderControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Border', 'wp-job-openings'),
    withSlider: true,
    isCompact: true,
    value: hz_bs_border // Use a valid default object
    ,
    __experimentalIsRenderedInSidebar: true,
    onChange: function onChange(newBorder) {
      var width = newBorder === null || newBorder === void 0 ? void 0 : newBorder.width;
      setAttributes({
        hz_bs_border: _objectSpread(_objectSpread({}, newBorder), {}, {
          width: width
        })
      });
    },
    enableStyle: false
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.__experimentalSpacer, null), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__.__experimentalBorderRadiusControl, {
    values: hz_bs_border_radius,
    onChange: function onChange(newRadius) {
      if (typeof newRadius === 'string') {
        var radiusObject = {
          topLeft: newRadius,
          topRight: newRadius,
          bottomRight: newRadius,
          bottomLeft: newRadius
        };
        setAttributes({
          hz_bs_border_radius: radiusObject
        });
      } else {
        setAttributes({
          hz_bs_border_radius: newRadius
        });
      }
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.BoxControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Padding', 'wp-job-openings'),
    values: hz_bs_padding // Ensure there is a fallback value
    ,
    onChange: function onChange(Padding) {
      setAttributes({
        hz_bs_padding: Padding
      });
    }
  })), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_6__.PanelColorSettings, {
    title: "Button Color Settings",
    initialOpen: true,
    colorSettings: [{
      value: hz_button_background_color,
      onChange: function onChange(color) {
        return setAttributes({
          hz_button_background_color: color
        });
      },
      label: 'Background Color'
    }, {
      value: hz_button_text_color,
      onChange: function onChange(color) {
        return setAttributes({
          hz_button_text_color: color
        });
      },
      label: 'Text Color'
    }]
  }), wp.hooks.doAction('after_awsm_block_styles_panel', block_styles_panel, props), block_styles_panel))));
};

// Define the HOC to add custom inspector controls
var withCustomInspectorControls = function withCustomInspectorControls(BlockEdit) {
  return function (props) {
    if (props.name !== 'wp-job-openings/blocks') {
      return (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(BlockEdit, _objectSpread({}, props));
    }
    return (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_5__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(BlockEdit, _objectSpread({}, props)), (0,react__WEBPACK_IMPORTED_MODULE_3__.createElement)(WidgetInspectorControls, _objectSpread({}, props)));
  };
};

// Add the filter to extend the block's inspector controls
(0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_7__.addFilter)('editor.BlockEdit', 'awsm-job-block-settings/awsm-block-inspector-controls', withCustomInspectorControls);
/* harmony default export */ __webpack_exports__["default"] = (withCustomInspectorControls);

/***/ }),

/***/ "./src/save.js":
/*!*********************!*\
  !*** ./src/save.js ***!
  \*********************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ save; }
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);


function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */


/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {Element} Element to render.
 */
function save() {
  return (0,react__WEBPACK_IMPORTED_MODULE_1__.createElement)("p", _objectSpread({}, _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.useBlockProps.save()));
}

/***/ }),

/***/ "./src/editor.scss":
/*!*************************!*\
  !*** ./src/editor.scss ***!
  \*************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/style.scss":
/*!************************!*\
  !*** ./src/style.scss ***!
  \************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ (function(module) {

module.exports = window["React"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ (function(module) {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ (function(module) {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/hooks":
/*!*******************************!*\
  !*** external ["wp","hooks"] ***!
  \*******************************/
/***/ (function(module) {

module.exports = window["wp"]["hooks"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ (function(module) {

module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js ***!
  \*********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _arrayLikeToArray; }
/* harmony export */ });
function _arrayLikeToArray(r, a) {
  (null == a || a > r.length) && (a = r.length);
  for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
  return n;
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js ***!
  \*******************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _arrayWithHoles; }
/* harmony export */ });
function _arrayWithHoles(r) {
  if (Array.isArray(r)) return r;
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js ***!
  \**********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _arrayWithoutHoles; }
/* harmony export */ });
/* harmony import */ var _arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js");

function _arrayWithoutHoles(r) {
  if (Array.isArray(r)) return (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(r);
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _defineProperty; }
/* harmony export */ });
/* harmony import */ var _toPropertyKey_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./toPropertyKey.js */ "./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js");

function _defineProperty(e, r, t) {
  return (r = (0,_toPropertyKey_js__WEBPACK_IMPORTED_MODULE_0__["default"])(r)) in e ? Object.defineProperty(e, r, {
    value: t,
    enumerable: !0,
    configurable: !0,
    writable: !0
  }) : e[r] = t, e;
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js ***!
  \********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _iterableToArray; }
/* harmony export */ });
function _iterableToArray(r) {
  if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r);
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js ***!
  \*************************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _iterableToArrayLimit; }
/* harmony export */ });
function _iterableToArrayLimit(r, l) {
  var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
  if (null != t) {
    var e,
      n,
      i,
      u,
      a = [],
      f = !0,
      o = !1;
    try {
      if (i = (t = t.call(r)).next, 0 === l) {
        if (Object(t) !== t) return;
        f = !1;
      } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
    } catch (r) {
      o = !0, n = r;
    } finally {
      try {
        if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return;
      } finally {
        if (o) throw n;
      }
    }
    return a;
  }
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js ***!
  \********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _nonIterableRest; }
/* harmony export */ });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js ***!
  \**********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _nonIterableSpread; }
/* harmony export */ });
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js ***!
  \******************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _slicedToArray; }
/* harmony export */ });
/* harmony import */ var _arrayWithHoles_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithHoles.js */ "./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js");
/* harmony import */ var _iterableToArrayLimit_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArrayLimit.js */ "./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js");
/* harmony import */ var _unsupportedIterableToArray_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js");
/* harmony import */ var _nonIterableRest_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./nonIterableRest.js */ "./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js");




function _slicedToArray(r, e) {
  return (0,_arrayWithHoles_js__WEBPACK_IMPORTED_MODULE_0__["default"])(r) || (0,_iterableToArrayLimit_js__WEBPACK_IMPORTED_MODULE_1__["default"])(r, e) || (0,_unsupportedIterableToArray_js__WEBPACK_IMPORTED_MODULE_2__["default"])(r, e) || (0,_nonIterableRest_js__WEBPACK_IMPORTED_MODULE_3__["default"])();
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js ***!
  \**********************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _toConsumableArray; }
/* harmony export */ });
/* harmony import */ var _arrayWithoutHoles_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithoutHoles.js */ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js");
/* harmony import */ var _iterableToArray_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArray.js */ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js");
/* harmony import */ var _unsupportedIterableToArray_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js");
/* harmony import */ var _nonIterableSpread_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./nonIterableSpread.js */ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js");




function _toConsumableArray(r) {
  return (0,_arrayWithoutHoles_js__WEBPACK_IMPORTED_MODULE_0__["default"])(r) || (0,_iterableToArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(r) || (0,_unsupportedIterableToArray_js__WEBPACK_IMPORTED_MODULE_2__["default"])(r) || (0,_nonIterableSpread_js__WEBPACK_IMPORTED_MODULE_3__["default"])();
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toPrimitive.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toPrimitive.js ***!
  \****************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ toPrimitive; }
/* harmony export */ });
/* harmony import */ var _typeof_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./typeof.js */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");

function toPrimitive(t, r) {
  if ("object" != (0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__["default"])(t) || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r || "default");
    if ("object" != (0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__["default"])(i)) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js ***!
  \******************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ toPropertyKey; }
/* harmony export */ });
/* harmony import */ var _typeof_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./typeof.js */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _toPrimitive_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./toPrimitive.js */ "./node_modules/@babel/runtime/helpers/esm/toPrimitive.js");


function toPropertyKey(t) {
  var i = (0,_toPrimitive_js__WEBPACK_IMPORTED_MODULE_1__["default"])(t, "string");
  return "symbol" == (0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__["default"])(i) ? i : i + "";
}


/***/ }),

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


/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* binding */ _unsupportedIterableToArray; }
/* harmony export */ });
/* harmony import */ var _arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js");

function _unsupportedIterableToArray(r, a) {
  if (r) {
    if ("string" == typeof r) return (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(r, a);
    var t = {}.toString.call(r).slice(8, -1);
    return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(r, a) : void 0;
  }
}


/***/ }),

/***/ "./src/block.json":
/*!************************!*\
  !*** ./src/block.json ***!
  \************************/
/***/ (function(module) {

module.exports = /*#__PURE__*/JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"wp-job-openings/blocks","version":"1.0.0","title":"","category":"","icon":"","description":"","attributes":{"search":{"type":"boolean","default":true},"placement":{"type":"string","default":"slide"},"search_placeholder":{"type":"string","default":""},"filter_options":{"type":"array","default":[]},"filtersInitialized":{"type":"boolean","default":false},"listType":{"type":"string","default":"all"},"layout":{"type":"string","default":"stack"},"selected_terms_main":{"type":"array","default":[]},"selectedTerms":{"type":"object","default":{}},"orderBy":{"type":"string","default":"new"},"hide_expired_jobs":{"type":"boolean","default":false},"jobsPerPage":{"type":"number","default":5},"pagination":{"type":"string","default":"modern"},"number_of_columns":{"type":"number","default":3},"other_options":{"type":"array","default":[]},"specsInitialized":{"type":"boolean","default":false},"hz_sf_border":{"type":"object","default":{"width":"1px","color":"#cccccc"}},"hz_sf_border_radius":{"type":"object","default":{"topLeft":"5px","topRight":"5px","bottomLeft":"5px","bottomRight":"5px"}},"hz_sf_padding":{"type":"object","default":{"top":"15px","right":"15px","bottom":"15px","left":"15px"}},"blockId":{"type":"string"},"hz_sidebar_width":{"type":"number","default":"33.33"},"hz_ls_border":{"type":"object","default":{"width":"1px","color":"#cccccc"}},"hz_ls_border_radius":{"type":"object","default":{"topLeft":"5px","topRight":"5px","bottomLeft":"5px","bottomRight":"5px"}},"hz_jl_border":{"type":"object","default":{"width":"1px","color":"#CBCBCB"}},"hz_jl_border_radius":{"type":"object","default":{"topLeft":"5px","topRight":"5px","bottomLeft":"5px","bottomRight":"5px"}},"hz_jl_padding":{"type":"object","default":{"top":"15px","right":"15px","bottom":"15px","left":"15px"}},"hz_bs_border":{"type":"object","default":{"width":"1px","color":"#4E35DF"}},"hz_bs_border_radius":{"type":"object","default":{"topLeft":"5px","topRight":"5px","bottomLeft":"5px","bottomRight":"5px"}},"hz_bs_padding":{"type":"object","default":{"top":"13px","right":"13px","bottom":"13px","left":"13px"}},"hz_button_background_color":{"type":"string"},"hz_button_text_color":{"type":"string"}},"example":{},"supports":{},"textdomain":"wp-job-openings","editorScript":"file:./index.js","editorStyle":"file:./index.css","style":"file:./style-index.css","viewScript":"file:./view.js"}');

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
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	!function() {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = function(result, chunkIds, fn, priority) {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var chunkIds = deferred[i][0];
/******/ 				var fn = deferred[i][1];
/******/ 				var priority = deferred[i][2];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every(function(key) { return __webpack_require__.O[key](chunkIds[j]); })) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	}();
/******/ 	
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
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	!function() {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"index": 0,
/******/ 			"./style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = function(chunkId) { return installedChunks[chunkId] === 0; };
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = function(parentChunkLoadingFunction, data) {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some(function(id) { return installedChunks[id] !== 0; })) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkjoblistings"] = self["webpackChunkjoblistings"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["./style-index"], function() { return __webpack_require__("./src/index.js"); })
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map