!function(){"use strict";var e,t={841:function(){var e=window.wp.blocks;function t(e){return t="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},t(e)}function r(e,r,n){return a=function(e,r){if("object"!=t(e)||!e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var a=n.call(e,"string");if("object"!=t(a))return a;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(r),(r="symbol"==t(a)?a:a+"")in e?Object.defineProperty(e,r,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[r]=n,e;var a}var n=window.React,a=window.wp.i18n,o=window.wp.element,c=window.wp.blockEditor;function i(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}var s=window.wp.components,l=function(e){var t=e.attributes,r=t.filter_options,l=t.layout,m=t.search,p=t.pagination,u=t.enable_job_filter,b=t.search_placeholder,f=e.setAttributes,w=awsmJobsAdmin.awsm_filters;return(0,o.useEffect)((function(){if(w.length>0&&void 0===r){var e=w.map((function(e){return e.key}));f({filter_options:e})}})),(0,n.createElement)(c.InspectorControls,null,(0,n.createElement)(s.PanelBody,{title:(0,a.__)("Appearance","wp-job-openings")},(0,n.createElement)(s.SelectControl,{label:(0,a.__)("Layout","wp-job-openings"),value:l,options:[{label:(0,a.__)("List Layout","wp-job-openings"),value:"list"},{label:(0,a.__)("Grid Layout","wp-job-openings"),value:"grid"}],onChange:function(e){return f({layout:e})}}),(0,n.createElement)(s.SelectControl,{label:(0,a.__)("Pagination","wp-job-openings"),value:p,options:[{label:(0,a.__)("Classic","wp-job-openings"),value:"classic"},{label:(0,a.__)("Modern","wp-job-openings"),value:"modern"}],onChange:function(e){return f({pagination:e})}}),(0,n.createElement)(s.ToggleControl,{label:(0,a.__)("Enable Search","wp-job-openings"),checked:m,onChange:function(e){return f({search:e})}}),(0,n.createElement)(s.TextControl,{label:(0,a.__)("Search Placeholder","wp-job-openings"),value:b,onChange:function(e){return f({search_placeholder:e})}}),(0,n.createElement)(s.ToggleControl,{label:(0,a.__)("Enable Job Filters","wp-job-openings"),checked:u,onChange:function(e){return f({enable_job_filter:e})}})),u&&1==u&&(0,n.createElement)(s.PanelBody,{title:(0,a.__)("Filter Options","wp-job-openings")},w.length>0&&w.map((function(e){return(0,n.createElement)(s.ToggleControl,{label:e.label,checked:void 0!==r&&r.includes(e.key),onChange:function(t){return function(e,t){if(void 0!==r){jQuery(".awsm-job-select-control").selectric("destroy");var n=function(e){if(Array.isArray(e))return i(e)}(a=r)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(a)||function(e,t){if(e){if("string"==typeof e)return i(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?i(e,t):void 0}}(a)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}();e?n.push(t):n=n.filter((function(e){return e!==t})),f({filter_options:n})}var a}(t,e.key)}})}))))},m=window.wp.data;function p(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function u(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?p(Object(n),!0).forEach((function(t){r(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):p(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function b(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}var f=JSON.parse('{"UU":"wp-job-openings/wjo-block"}');(0,e.registerBlockType)(f.UU,{edit:function(e){var t=e.attributes,r=t.filter_options,a=t.layout,i=t.search,s=t.enable_job_filter,p=t.search_placeholder,b=(e.setAttributes,(0,c.useBlockProps)()),f=awsmJobsAdmin.awsm_filters;f=f.filter((function(e){if(void 0!==r&&r.includes(e.key))return e}));var w=(0,m.useSelect)((function(e){return e("core").getEntityRecords("postType","awsm_job_openings",{per_page:5})}),[]);return(0,o.useEffect)((function(){var e;e=jQuery(".awsm-job-select-control"),"selectric"in awsmJobsPublic.vendors&&awsmJobsPublic.vendors.selectric&&e.selectric({onInit:function(e,t){var r=e.id,n=jQuery(t.elements.input);jQuery(e).attr("id","selectric-"+r),n.attr("id",r)},arrowButtonMarkup:'<span class="awsm-selectric-arrow-drop">&#x25be;</span>',customClass:{prefix:"awsm-selectric",camelCase:!1}})})),(0,n.createElement)("div",u({},b),(0,n.createElement)(l,u({},e)),(0,n.createElement)("div",{className:"awsm-job-wrap"},i&&1==i&&[(0,n.createElement)("div",{class:"awsm-filter-item-search"},(0,n.createElement)("div",{class:"awsm-filter-item-search-in"},(0,n.createElement)("label",{for:"awsm-jq-1",class:"awsm-sr-only"},"Search"),(0,n.createElement)("input",{type:"text",id:"awsm-jq-1",name:"jq",value:"",placeholder:p,class:"awsm-job-search awsm-job-form-control"}),(0,n.createElement)("span",{class:"awsm-job-search-btn awsm-job-search-icon-wrapper"},(0,n.createElement)("i",{class:"awsm-job-icon-search"})),(0,n.createElement)("span",{class:"awsm-job-search-close-btn awsm-job-search-icon-wrapper awsm-job-hide"},(0,n.createElement)("i",{class:"awsm-job-icon-close-circle"}))))],f.length>0&&s&&1==s&&(0,n.createElement)("div",{className:"awsm-filter-wrap"},(0,n.createElement)("div",{className:"awsm-filter-items"},f.map((function(e){return(0,n.createElement)("div",{className:"awsm-filter-item"},(0,n.createElement)("select",{name:"awsm_job_alerts_spec[".concat(e.key,"]"),className:"awsm-job-select-control",id:"awsm_job_alerts_specs",multiple:!0},(0,n.createElement)("option",{value:""},e.label),e.terms.map((function(e){return(0,n.createElement)("option",{value:e.term_id},e.name)}))))})))),(0,n.createElement)("div",{className:"awsm-job-listings ".concat("list"===a?"awsm-lists":"awsm-row")},null==w?void 0:w.map((function(e){return(0,n.createElement)("div",{key:"awsm-".concat(a,"-item-").concat(e.id),className:"awsm-job-listing-item awsm-".concat(a,"-item")},"list"===a?(0,n.createElement)("div",{className:"awsm-job-item"},(0,n.createElement)("div",{className:"awsm-".concat(a,"-left-col")},(0,n.createElement)("h2",{className:"awsm-job-post-title"},(0,n.createElement)("a",{href:e.link},e.title.rendered))),(0,n.createElement)("div",{className:"awsm-".concat(a,"-right-col")},(0,n.createElement)("div",{className:"awsm-job-specification-wrapper"},(0,n.createElement)("div",{className:"awsm-job-specification-item awsm-job-specification-job-location"},(0,n.createElement)("span",{className:"awsm-job-specification-term"},"London")),(0,n.createElement)("div",{className:"awsm-job-specification-item awsm-job-specification-job-category"},(0,n.createElement)("span",{className:"awsm-job-specification-term"},"Designer"))),(0,n.createElement)("div",{className:"awsm-job-more-container"},(0,n.createElement)("a",{className:"awsm-job-more",href:"http://localhost/awsm/jobs/rr/"},"More Details ",(0,n.createElement)("span",null))))):(0,n.createElement)("a",{href:e.link,className:"awsm-job-item"},(0,n.createElement)("div",{className:"awsm-".concat(a,"-left-col")},(0,n.createElement)("h2",{className:"awsm-job-post-title"},e.title.rendered)),(0,n.createElement)("div",{className:"awsm-".concat(a,"-right-col")},(0,n.createElement)("div",{className:"awsm-job-specification-wrapper"},(0,n.createElement)("div",{className:"awsm-job-specification-item awsm-job-specification-job-location"},(0,n.createElement)("span",{className:"awsm-job-specification-term"},"London")),(0,n.createElement)("div",{className:"awsm-job-specification-item awsm-job-specification-job-category"},(0,n.createElement)("span",{className:"awsm-job-specification-term"},"Designer"))),(0,n.createElement)("div",{className:"awsm-job-more-container"},(0,n.createElement)("span",{className:"awsm-job-more"},"More Details ",(0,n.createElement)("span",null))))))})))))},save:function(){return(0,n.createElement)("p",function(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?b(Object(n),!0).forEach((function(t){r(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):b(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}({},c.useBlockProps.save()),"Wjo Block – hello from the saved content!")}})}},r={};function n(e){var a=r[e];if(void 0!==a)return a.exports;var o=r[e]={exports:{}};return t[e](o,o.exports,n),o.exports}n.m=t,e=[],n.O=function(t,r,a,o){if(!r){var c=1/0;for(m=0;m<e.length;m++){r=e[m][0],a=e[m][1],o=e[m][2];for(var i=!0,s=0;s<r.length;s++)(!1&o||c>=o)&&Object.keys(n.O).every((function(e){return n.O[e](r[s])}))?r.splice(s--,1):(i=!1,o<c&&(c=o));if(i){e.splice(m--,1);var l=a();void 0!==l&&(t=l)}}return t}o=o||0;for(var m=e.length;m>0&&e[m-1][2]>o;m--)e[m]=e[m-1];e[m]=[r,a,o]},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){var e={57:0,350:0};n.O.j=function(t){return 0===e[t]};var t=function(t,r){var a,o,c=r[0],i=r[1],s=r[2],l=0;if(c.some((function(t){return 0!==e[t]}))){for(a in i)n.o(i,a)&&(n.m[a]=i[a]);if(s)var m=s(n)}for(t&&t(r);l<c.length;l++)o=c[l],n.o(e,o)&&e[o]&&e[o][0](),e[o]=0;return n.O(m)},r=self.webpackChunkwjo_block=self.webpackChunkwjo_block||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))}();var a=n.O(void 0,[350],(function(){return n(841)}));a=n.O(a)}();