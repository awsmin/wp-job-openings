!function(){"use strict";var e,t={595:function(){var e=window.wp.blocks;function t(e){return t="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},t(e)}function r(e){var r=function(e,r){if("object"!=t(e)||!e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var o=n.call(e,"string");if("object"!=t(o))return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return String(e)}(e);return"symbol"==t(r)?r:r+""}var n=window.React,o=window.wp.element,i=window.wp.i18n,l=window.wp.blockEditor;function a(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}var c=window.wp.components,s=function(e){var t=e.attributes,r=t.filter_options,s=t.layout,u=t.listing_order,p=e.setAttributes,f=awsmJobsAdmin.awsm_filters;return(0,o.useEffect)((function(){if(f.length>0&&void 0===r){var e=f.map((function(e){return e.key}));p({filter_options:e})}})),(0,n.createElement)(l.InspectorControls,null,(0,n.createElement)(c.PanelBody,{title:(0,i.__)("Appearance","wp-job-openings")},(0,n.createElement)(c.SelectControl,{label:(0,i.__)("Layout","wp-job-openings"),value:s,options:[{label:(0,i.__)("List Layout","wp-job-openings"),value:"list"},{label:(0,i.__)("Grid Layout","wp-job-openings"),value:"grid"}],onChange:function(e){return p({layout:e})}}),(0,n.createElement)(c.SelectControl,{label:(0,i.__)("Listing Order","wp-job-openings"),value:u,options:[{label:(0,i.__)("Ascending","wp-job-openings"),value:"ascending"},{label:(0,i.__)("Descending","wp-job-openings"),value:"descending"}],onChange:function(e){return p({listing_order:e})}})),(0,n.createElement)(c.PanelBody,{title:(0,i.__)("Filter Options","wp-job-openings")},f.length>0&&f.map((function(e){return(0,n.createElement)(c.ToggleControl,{label:e.label,checked:void 0!==r&&r.includes(e.key),onChange:function(t){return function(e,t){if(void 0!==r){jQuery(".awsm-job-select-control").selectric("destroy");var n=function(e){if(Array.isArray(e))return a(e)}(o=r)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(o)||function(e,t){if(e){if("string"==typeof e)return a(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?a(e,t):void 0}}(o)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}();e?n.push(t):n=n.filter((function(e){return e!==t})),p({filter_options:n})}var o}(t,e.key)}})}))))};function u(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function p(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?u(Object(n),!0).forEach((function(t){var o,i,l;o=e,i=t,l=n[t],(i=r(i))in o?Object.defineProperty(o,i,{value:l,enumerable:!0,configurable:!0,writable:!0}):o[i]=l})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):u(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var f=JSON.parse('{"UU":"create-block/job-list-block"}');wp.i18n.__,(0,e.registerBlockType)(f.UU,{edit:function(e){var t=e.attributes.filter_options,r=(e.setAttributes,(0,l.useBlockProps)()),i=awsmJobsAdmin.awsm_filters;return i=i.filter((function(e){if(void 0!==t&&t.includes(e.key))return e})),(0,o.useEffect)((function(){var e;e=jQuery(".awsm-job-select-control"),"selectric"in awsmJobsPublic.vendors&&awsmJobsPublic.vendors.selectric&&e.selectric({onInit:function(e,t){var r=e.id,n=jQuery(t.elements.input);jQuery(e).attr("id","selectric-"+r),n.attr("id",r)},arrowButtonMarkup:'<span class="awsm-selectric-arrow-drop">&#x25be;</span>',customClass:{prefix:"awsm-selectric",camelCase:!1}})})),(0,n.createElement)("div",p({},r),(0,n.createElement)(s,p({},e)),(0,n.createElement)("div",{className:"awsm-jobs-alerts-widget-wrapper"},i.length>0&&(0,n.createElement)("div",{className:"awsm-jobs-alerts-form-group awsm-jobs-alerts-specs-group"},i.map((function(e){return(0,n.createElement)("div",{className:"awsm-jobs-alerts-specs-group-in"},(0,n.createElement)("select",{name:"awsm_job_alerts_spec[".concat(e.key,"]"),className:"awsm-job-select-control",id:"awsm_job_alerts_specs",multiple:!0},(0,n.createElement)("option",{value:""},e.label),e.terms.map((function(e){return(0,n.createElement)("option",{value:e.term_id},e.name)}))))})))))},save:function(e){return(0,n.createElement)(o.Fragment,null,(0,n.createElement)(l.InnerBlocks.Content,null))}})}},r={};function n(e){var o=r[e];if(void 0!==o)return o.exports;var i=r[e]={exports:{}};return t[e](i,i.exports,n),i.exports}n.m=t,e=[],n.O=function(t,r,o,i){if(!r){var l=1/0;for(u=0;u<e.length;u++){r=e[u][0],o=e[u][1],i=e[u][2];for(var a=!0,c=0;c<r.length;c++)(!1&i||l>=i)&&Object.keys(n.O).every((function(e){return n.O[e](r[c])}))?r.splice(c--,1):(a=!1,i<l&&(l=i));if(a){e.splice(u--,1);var s=o();void 0!==s&&(t=s)}}return t}i=i||0;for(var u=e.length;u>0&&e[u-1][2]>i;u--)e[u]=e[u-1];e[u]=[r,o,i]},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){var e={57:0,350:0};n.O.j=function(t){return 0===e[t]};var t=function(t,r){var o,i,l=r[0],a=r[1],c=r[2],s=0;if(l.some((function(t){return 0!==e[t]}))){for(o in a)n.o(a,o)&&(n.m[o]=a[o]);if(c)var u=c(n)}for(t&&t(r);s<l.length;s++)i=l[s],n.o(e,i)&&e[i]&&e[i][0](),e[i]=0;return n.O(u)},r=self.webpackChunkjob_list_block=self.webpackChunkjob_list_block||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))}();var o=n.O(void 0,[350],(function(){return n(595)}));o=n.O(o)}();