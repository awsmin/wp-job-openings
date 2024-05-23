!function(){"use strict";var e,t={887:function(e,t,n){var r=window.wp.blocks;function o(e){return o="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},o(e)}function i(e,t,n){return r=function(e,t){if("object"!=o(e)||!e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,"string");if("object"!=o(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(t),(t="symbol"==o(r)?r:r+"")in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e;var r}var l=window.React,a=window.wp.i18n,c=window.wp.element,u=window.wp.blockEditor,s=window.wp.serverSideRender,p=n.n(s);function f(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}var b=window.wp.components,y=function(e){var t=e.attributes,n=t.filter_options,r=t.layout,o=t.listing_per_page,i=t.search,s=t.pagination,p=t.enable_job_filter,y=t.search_placeholder,g=e.setAttributes,w=awsmJobsAdmin.awsm_filters;return(0,c.useEffect)((function(){if(w.length>0&&void 0===n){var e=w.map((function(e){return e.key}));g({filter_options:e})}})),(0,l.createElement)(u.InspectorControls,null,(0,l.createElement)(b.PanelBody,{title:(0,a.__)("Appearance","wp-job-openings")},(0,l.createElement)(b.SelectControl,{label:(0,a.__)("Layout","wp-job-openings"),value:r,options:[{label:(0,a.__)("List Layout","wp-job-openings"),value:"list"},{label:(0,a.__)("Grid Layout","wp-job-openings"),value:"grid"}],onChange:function(e){return g({layout:e})}}),(0,l.createElement)(b.TextControl,{label:(0,a.__)("Listing per page","wp-job-openings"),value:o,onChange:function(e){return g({listing_per_page:e})}}),(0,l.createElement)(b.SelectControl,{label:(0,a.__)("Pagination","wp-job-openings"),value:s,options:[{label:(0,a.__)("Classic","wp-job-openings"),value:"classic"},{label:(0,a.__)("Modern","wp-job-openings"),value:"modern"}],onChange:function(e){return g({pagination:e})}}),(0,l.createElement)(b.ToggleControl,{label:(0,a.__)("Enable Search","wp-job-openings"),checked:i,onChange:function(e){return g({search:e})}}),(0,l.createElement)(b.TextControl,{label:(0,a.__)("Search Placeholder","wp-job-openings"),value:y,onChange:function(e){return g({search_placeholder:e})}}),(0,l.createElement)(b.ToggleControl,{label:(0,a.__)("Enable Job Filters","wp-job-openings"),checked:p,onChange:function(e){return g({enable_job_filter:e})}})),p&&1==p&&(0,l.createElement)(b.PanelBody,{title:(0,a.__)("Filter Options","wp-job-openings")},w.length>0&&w.map((function(e){return(0,l.createElement)(b.ToggleControl,{label:e.label,checked:void 0!==n&&n.includes(e.key),onChange:function(t){return function(e,t){if(void 0!==n){jQuery(".awsm-job-select-control").selectric("destroy");var r=function(e){if(Array.isArray(e))return f(e)}(o=n)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(o)||function(e,t){if(e){if("string"==typeof e)return f(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);return"Object"===n&&e.constructor&&(n=e.constructor.name),"Map"===n||"Set"===n?Array.from(e):"Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?f(e,t):void 0}}(o)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}();e?r.push(t):r=r.filter((function(e){return e!==t})),g({filter_options:r})}var o}(t,e.key)}})}))))};function g(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function w(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?g(Object(n),!0).forEach((function(t){i(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):g(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function v(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}window.wp.data;var d=JSON.parse('{"UU":"wp-job-openings/wjo-block"}');(0,r.registerBlockType)(d.UU,{edit:function(e){var t=e.attributes,n=t.filter_options,r=(t.layout,t.listing_per_page,t.search,t.enable_job_filter,t.search_placeholder,e.setAttributes,(0,u.useBlockProps)()),o=awsmJobsAdmin.awsm_filters;return o=o.filter((function(e){if(void 0!==n&&n.includes(e.key))return e})),(0,l.createElement)("div",w({},r),(0,l.createElement)(y,w({},e)),(0,l.createElement)(p(),{block:"wp-job-openings/wjo-block",attributes:e.attributes}))},save:function(){return(0,l.createElement)("p",function(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?v(Object(n),!0).forEach((function(t){i(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):v(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}({},u.useBlockProps.save()),"Wjo Block – hello from the saved content!")}})}},n={};function r(e){var o=n[e];if(void 0!==o)return o.exports;var i=n[e]={exports:{}};return t[e](i,i.exports,r),i.exports}r.m=t,e=[],r.O=function(t,n,o,i){if(!n){var l=1/0;for(s=0;s<e.length;s++){n=e[s][0],o=e[s][1],i=e[s][2];for(var a=!0,c=0;c<n.length;c++)(!1&i||l>=i)&&Object.keys(r.O).every((function(e){return r.O[e](n[c])}))?n.splice(c--,1):(a=!1,i<l&&(l=i));if(a){e.splice(s--,1);var u=o();void 0!==u&&(t=u)}}return t}i=i||0;for(var s=e.length;s>0&&e[s-1][2]>i;s--)e[s]=e[s-1];e[s]=[n,o,i]},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,{a:t}),t},r.d=function(e,t){for(var n in t)r.o(t,n)&&!r.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){var e={57:0,350:0};r.O.j=function(t){return 0===e[t]};var t=function(t,n){var o,i,l=n[0],a=n[1],c=n[2],u=0;if(l.some((function(t){return 0!==e[t]}))){for(o in a)r.o(a,o)&&(r.m[o]=a[o]);if(c)var s=c(r)}for(t&&t(n);u<l.length;u++)i=l[u],r.o(e,i)&&e[i]&&e[i][0](),e[i]=0;return r.O(s)},n=self.webpackChunkwjo_block=self.webpackChunkwjo_block||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))}();var o=r.O(void 0,[350],(function(){return r(887)}));o=r.O(o)}();