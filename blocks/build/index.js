!function(){"use strict";var e,t={945:function(e,t,n){var r=window.wp.blocks;function o(e){return o="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},o(e)}function i(e,t,n){return(t=function(e){var t=function(e,t){if("object"!=o(e)||!e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,"string");if("object"!=o(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(e);return"symbol"==o(t)?t:t+""}(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var l=window.React,a=window.wp.i18n,c=window.wp.element,s=window.wp.blockEditor,u=window.wp.serverSideRender,p=n.n(u);function b(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=Array(t);n<t;n++)r[n]=e[n];return r}function f(e,t){if(e){if("string"==typeof e)return b(e,t);var n={}.toString.call(e).slice(8,-1);return"Object"===n&&e.constructor&&(n=e.constructor.name),"Map"===n||"Set"===n?Array.from(e):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?b(e,t):void 0}}function d(e){return function(e){if(Array.isArray(e))return b(e)}(e)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(e)||f(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}var g=window.wp.hooks,m=window.wp.components;function y(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function w(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?y(Object(n),!0).forEach((function(t){i(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):y(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var v=function(e){var t,n,r=e.attributes,o=r.filter_options,i=r.other_options,u=r.layout,p=r.listing_per_page,b=r.search,g=r.pagination,y=r.enable_job_filter,w=r.search_placeholder,v=r.hide_expired_jobs,j=r.featured_image_size,h=e.setAttributes,_=awsmJobsAdmin.awsm_filters_block,O=awsmJobsAdmin.awsm_featured_image_block,C=(t=(0,c.useState)(!1),n=2,function(e){if(Array.isArray(e))return e}(t)||function(e,t){var n=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=n){var r,o,i,l,a=[],c=!0,s=!1;try{if(i=(n=n.call(e)).next,0===t){if(Object(n)!==n)return;c=!1}else for(;!(c=(r=i.call(n)).done)&&(a.push(r.value),a.length!==t);c=!0);}catch(e){s=!0,o=e}finally{try{if(!c&&null!=n.return&&(l=n.return(),Object(l)!==l))return}finally{if(s)throw o}}return a}}(t,n)||f(t,n)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()),E=(C[0],C[1]);return(0,c.useEffect)((function(){if(_.length>0&&void 0===o){var e=_.map((function(e){return e.value}));h({filter_options:e})}if(O.length>0&&void 0===j){var t=O.map((function(e){return e.value}));h({featured_image_size:t})}"undefined"!=typeof awsmJobsAdmin&&awsmJobsAdmin.isProEnabled&&E(!0)}),[]),(0,l.createElement)(s.InspectorControls,null,(0,l.createElement)(m.PanelBody,{title:(0,a.__)("Appearance","wp-job-openings")},(0,l.createElement)(m.SelectControl,{label:(0,a.__)("Layout","wp-job-openings"),value:u,options:[{label:(0,a.__)("List Layout","wp-job-openings"),value:"list"},{label:(0,a.__)("Grid Layout","wp-job-openings"),value:"grid"}],onChange:function(e){return h({layout:e})}}),(0,l.createElement)(m.TextControl,{label:(0,a.__)("Listing per page","wp-job-openings"),value:p,onChange:function(e){return t=parseInt(e,10),void h({listing_per_page:isNaN(t)?0:t});var t}}),(0,l.createElement)(m.SelectControl,{label:(0,a.__)("Pagination","wp-job-openings"),value:g,options:[{label:(0,a.__)("Classic","wp-job-openings"),value:"classic"},{label:(0,a.__)("Modern","wp-job-openings"),value:"modern"}],onChange:function(e){return h({pagination:e})}}),(0,l.createElement)(m.ToggleControl,{label:(0,a.__)("Hide Expired Jobs","wp-job-openings"),checked:v,onChange:function(e){return h({hide_expired_jobs:e})}})),_.length>0&&(0,l.createElement)(m.PanelBody,{title:(0,a.__)("Filter Options","wp-job-openings")},(0,l.createElement)(m.ToggleControl,{label:(0,a.__)("Enable Search","wp-job-openings"),checked:b,onChange:function(e){return h({search:e})}}),b&&(0,l.createElement)(m.TextControl,{label:(0,a.__)("Search Placeholder","wp-job-openings"),value:w,onChange:function(e){return h({search_placeholder:e})}}),(0,l.createElement)(m.ToggleControl,{label:(0,a.__)("Enable Job Filters","wp-job-openings"),checked:y,onChange:function(e){return h({enable_job_filter:e})}}),y&&(0,l.createElement)(c.Fragment,null,(0,l.createElement)("h2",null,"Available filters"),_.map((function(e){return(0,l.createElement)(m.ToggleControl,{key:e.key,label:e.label,checked:o.includes(e.key),onChange:function(t){return function(e,t){if(void 0!==o){jQuery(".awsm-job-select-control").selectric("destroy");var n=d(o);e?n.push(t):n=n.filter((function(e){return e!==t})),h({filter_options:n})}}(t,e.key)}})})))),(0,l.createElement)(m.PanelBody,{title:(0,a.__)("Other Options","wp-job-openings")},(0,l.createElement)("h2",null,"Job specs in the listing"),_.length>0&&_.map((function(e){return(0,l.createElement)(m.ToggleControl,{label:e.label,checked:void 0!==i&&i.includes(e.key),onChange:function(t){return function(e,t){if(void 0!==i){jQuery(".awsm-job-select-control").selectric("destroy");var n=d(i);e?n.push(t):n=n.filter((function(e){return e!==t})),h({other_options:n})}}(t,e.key)}})}))))},j=function(e){return function(t){return"wp-job-openings/blocks"!==t.name?(0,l.createElement)(e,w({},t)):(0,l.createElement)(c.Fragment,null,(0,l.createElement)(e,w({},t)),(0,l.createElement)(v,w({},t)))}};(0,g.addFilter)("editor.BlockEdit","awsm-job-block-settings/awsm-block-inspector-controls",j);var h=j;function _(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function O(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?_(Object(n),!0).forEach((function(t){i(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):_(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function C(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}window.wp.data;var E={block:(0,l.createElement)("svg",{width:"31",height:"41",viewBox:"0 0 31 41",version:"1.1"},(0,l.createElement)("title",null,"Job Listings"),(0,l.createElement)("g",{id:"Page-1",stroke:"none","stroke-width":"1",fill:"none","fill-rule":"evenodd"},(0,l.createElement)("g",{id:"wpjo-logo-v12",transform:"translate(-9, -3.9998)",fill:"#000000","fill-rule":"nonzero"},(0,l.createElement)("g",{id:"path42",transform:"translate(24.5, 24.4998) rotate(180) translate(-24.5, -24.4998)translate(9, 3.9998)"},(0,l.createElement)("path",{d:"M22.5545146,31.023493 C22.5827932,30.996742 22.7426515,30.8370421 22.7684766,30.8050483 C23.1900735,30.2026791 23.0251794,29.0621967 22.8743601,28.3060424 C22.7365824,27.64802 22.5116447,27.0882641 22.2756022,26.8251897 C21.7061559,23.5291633 19.1725716,19.3702475 15.5973269,19.1807048 C12.7879293,19.0310871 9.28783599,22.6176109 8.72742851,26.8251897 C8.49241893,27.0893396 8.29059476,27.5121139 8.15953172,28.1663722 C8.00561335,28.921989 7.73083292,30.2037544 8.1534628,30.8050483 C8.21247345,30.8943081 8.28465496,30.966227 8.36277627,31.023493 C7.55018544,34.1908723 8.22151226,35.8607298 8.32545882,36.1238043 C9.76405323,39.8384376 12.8821914,42.0722183 17.298693,40.4791187 C18.7937155,39.9392583 20.4497583,39.2555602 21.6400433,37.8939448 C23.8397072,35.3829747 22.9566135,32.5893037 22.5545146,31.023493 L22.5545146,31.023493 Z M28.3531824,1.97033654 C22.28774,-0.656778846 8.71541978,-0.656778846 2.65191417,1.97033654 C2.16420472,2.18353844 -0.0828743212,4.42994188 0.00236184841,6.43217133 C0.124437708,9.32757698 0.280292968,12.5071891 2.62273166,16.2632261 C2.62273166,16.2632261 3.2905721,17.2457562 4.8763703,17.9000146 C4.8763703,17.9000146 8.3193899,19.0310871 9.90170171,20.2555865 C9.90170171,20.2555865 10.6370751,19.6790272 10.6275198,19.7200276 C10.6290693,19.7032241 10.5483654,14.6412247 10.5483654,14.6412247 L14.8926855,2.11632473 C14.9881098,1.84064094 15.2307378,1.66052162 15.5015153,1.66052162 C15.771389,1.66052162 16.0135005,1.84064094 16.1093121,2.11632473 L20.4547944,14.6412247 L20.3749944,19.7200276 L21.0973261,20.2555865 C22.6836408,19.0310871 26.1257564,17.9000146 26.1257564,17.9000146 C27.7125877,17.2457562 28.3793951,16.2632261 28.3793951,16.2632261 C30.7227765,12.5071891 30.8751453,9.32757698 30.997686,6.43217133 C31.0818763,4.42994188 28.8438618,2.18353844 28.3531824,1.97033654 Z M15.7163812,15.0790548 L17.2634416,13.4675387 L15.6715744,3.0389271 C15.6573706,2.94755678 15.5853182,2.88297785 15.5025483,2.88297785 C15.4178416,2.88297785 15.3466931,2.94755678 15.3326183,3.0389271 L13.7416551,13.4675387 L15.2876824,15.0790548 L13.5646231,17.9242115 C14.1911432,17.6899045 14.8281225,17.5576279 15.4596785,17.5576279 L15.6563376,17.5628706 C16.2733023,17.5881429 16.8588894,17.7057669 17.4127114,17.8821357 L15.7163812,15.0790548 Z",id:"path-1"})))))},k=JSON.parse('{"UU":"wp-job-openings/blocks"}');(0,r.registerBlockType)(k.UU,{title:(0,a.__)("Job Listings","wp-job-openings"),description:(0,a.__)("Super simple Job Listing plugin to manage Job Openings and Applicants on your WordPress site.","wp-job-openings"),icon:E.block,category:"widgets",keywords:[(0,a.__)("jobs listings","wp-job-openings"),(0,a.__)("add jobs","wp-job-openings"),(0,a.__)("job application","wp-job-openings")],edit:function(e){var t=e.attributes,n=t.filter_options,r=(t.layout,t.listing_per_page,t.search,t.enable_job_filter,t.search_placeholder,e.setAttributes,(0,s.useBlockProps)()),o=awsmJobsAdmin.awsm_filters_block;return o=o.filter((function(e){if(void 0!==n&&n.includes(e.key))return e})),(0,c.useEffect)((function(){var e;e=jQuery(".awsm-job-select-control"),"selectric"in awsmJobsPublic.vendors&&awsmJobsPublic.vendors.selectric&&e.selectric({onInit:function(e,t){var n=e.id,r=jQuery(t.elements.input);jQuery(e).attr("id","selectric-"+n),r.attr("id",n)},arrowButtonMarkup:'<span class="awsm-selectric-arrow-drop">&#x25be;</span>',customClass:{prefix:"awsm-selectric",camelCase:!1}})})),(0,l.createElement)("div",O({},r),(0,l.createElement)(h,O({},e)),(0,l.createElement)(p(),{block:"wp-job-openings/blocks",attributes:e.attributes}))},save:function(){return(0,l.createElement)("p",function(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?C(Object(n),!0).forEach((function(t){i(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):C(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}({},s.useBlockProps.save()))}})}},n={};function r(e){var o=n[e];if(void 0!==o)return o.exports;var i=n[e]={exports:{}};return t[e](i,i.exports,r),i.exports}r.m=t,e=[],r.O=function(t,n,o,i){if(!n){var l=1/0;for(u=0;u<e.length;u++){n=e[u][0],o=e[u][1],i=e[u][2];for(var a=!0,c=0;c<n.length;c++)(!1&i||l>=i)&&Object.keys(r.O).every((function(e){return r.O[e](n[c])}))?n.splice(c--,1):(a=!1,i<l&&(l=i));if(a){e.splice(u--,1);var s=o();void 0!==s&&(t=s)}}return t}i=i||0;for(var u=e.length;u>0&&e[u-1][2]>i;u--)e[u]=e[u-1];e[u]=[n,o,i]},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,{a:t}),t},r.d=function(e,t){for(var n in t)r.o(t,n)&&!r.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){var e={57:0,350:0};r.O.j=function(t){return 0===e[t]};var t=function(t,n){var o,i,l=n[0],a=n[1],c=n[2],s=0;if(l.some((function(t){return 0!==e[t]}))){for(o in a)r.o(a,o)&&(r.m[o]=a[o]);if(c)var u=c(r)}for(t&&t(n);s<l.length;s++)i=l[s],r.o(e,i)&&e[i]&&e[i][0](),e[i]=0;return r.O(u)},n=self.webpackChunkjoblistings=self.webpackChunkjoblistings||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))}();var o=r.O(void 0,[350],(function(){return r(945)}));o=r.O(o)}();