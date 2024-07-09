!function(){"use strict";jQuery((function(a){var e=".awsm-job-wrap",s=".awsm-job-listings",t=".awsm-filter-wrap",i=window.location.protocol+"//"+window.location.host+window.location.pathname,n=!0;function o(e){var s=[],t=["listings","specs","search","lang","taxonomy","termId"];t.push("layout"),t.push("hide_expired_jobs"),t.push("other_options"),t.push("position_filling");var i=e.data();return a.each(i,(function(e,i){-1===a.inArray(e,t)&&s.push({name:e,value:i})})),s}function r(e){var i=e.find(s),r=e.find(t+" form"),l=r.serializeArray(),d=i.data("listings"),c=i.data("specs"),m=i.data("layout"),p=i.data("hide_expired_jobs"),u=i.data("other_options"),h=i.data("position_filling");l.push({name:"listings_per_page",value:d}),void 0!==c&&l.push({name:"shortcode_specs",value:c}),void 0!==m&&l.push({name:"layout",value:m}),void 0!==p&&l.push({name:"hide_expired_jobs",value:p}),void 0!==u&&l.push({name:"other_options",value:u}),void 0!==h&&l.push({name:"position_filling",value:h});var f=o(i);f.length>0&&(l=l.concat(f)),n&&(n=!1,a.ajax({url:r.attr("action"),beforeSend:function(){i.addClass("awsm-jobs-loading")},data:l,type:r.attr("method")}).done((function(s){i.html(s);var t=e.find(".awsm-job-search");t.length>0&&(t.val().length>0?(e.find(".awsm-job-search-btn").addClass("awsm-job-hide"),e.find(".awsm-job-search-close-btn").removeClass("awsm-job-hide")):e.find(".awsm-job-search-btn").removeClass("awsm-job-hide")),a(document).trigger("awsmjobs_filtered_listings",[e,s])})).fail((function(a){console.log(a)})).always((function(){i.removeClass("awsm-jobs-loading"),n=!0})))}function l(e){var s=!1;return e.length>0&&e.find(".awsm-filter-option").each((function(){a(this).val().length>0&&(s=!0)})),s}function d(a){var t=a.parents(e),i=t.find(".awsm-job-search").val();if(t.find(s).data("search",i),0===i.length&&t.find(".awsm-job-search-icon-wrapper").addClass("awsm-job-hide"),m(t,"jq",i),awsmJobsPublic.deep_linking.search){var n=t.find('input[name="awsm_pagination_base"]');c("jq",i,n.val())}r(t)}a(e).length>0&&a(e).each((function(){var e=a(this),s=e.find(t+" form");(awsmJobsPublic.is_search.length>0||l(s))&&(n=!0,r(e))}));var c=function(a,e,s){s=(s=void 0!==s?s:i).split("?")[0];var t=new URLSearchParams(document.location.search);t.has("paged")&&t.delete("paged"),e.length>0?t.set(a,e):t.delete(a);var n=t.toString();n.length>0&&(n="?"+n),window.history.replaceState({},"",s+n)},m=function(a,e,s){var t=a.find('input[name="awsm_pagination_base"]');if(t.length>0){var i=t.val().split("?"),n="";i.length>1&&(n=i[1]);var o=new URLSearchParams(n);s.length>0?o.set(e,s):o.delete(e),t.val(i[0]+"?"+o.toString()),a.find('input[name="paged"]').val(1)}};function p(e){"selectric"in awsmJobsPublic.vendors&&awsmJobsPublic.vendors.selectric&&e.selectric({onInit:function(e,s){var t=e.id,i=a(s.elements.input);a(e).attr("id","selectric-"+t),i.attr("id",t)},arrowButtonMarkup:'<span class="awsm-selectric-arrow-drop">&#x25be;</span>',customClass:{prefix:"awsm-selectric",camelCase:!1}})}function u(){a(".awsm-filter-wrap").not(".awsm-no-search-filter-wrap").each((function(){var e=a(this),s=e.find(".awsm-filter-item").first().offset().top;e.find(".awsm-filter-item").last().offset().top>s?e.addClass("awsm-full-width-search-filter-wrap"):e.removeClass("awsm-full-width-search-filter-wrap")}))}a(t+" .awsm-filter-option").on("change",(function(s){s.preventDefault();var t=a(this),i=t.find("option:selected"),n=t.parents(e),o=t.parents(".awsm-filter-item").data("filter"),l=i.data("slug");if(m(n,o,l=void 0!==l?l:""),awsmJobsPublic.deep_linking.spec){var d=n.find('input[name="awsm_pagination_base"]');c(o,l,d.val())}r(n)})),a(t+" .awsm-job-search-btn").on("click",(function(){d(a(this))})),a(t+" .awsm-job-search-close-btn").on("click",(function(){var s=a(this);s.parents(e).find(".awsm-job-search").val(""),d(s)})),a(t+" .awsm-job-search").on("keypress",(function(e){13==e.which&&(e.preventDefault(),d(a(this)))})),a(s).on("click",".awsm-b-jobs-pagination .awsm-b-load-more-btn, .awsm-b-jobs-pagination a.page-numbers",(function(i){i.preventDefault();var n=a(this),r=n.hasClass("awsm-b-load-more-btn"),d=1,m=[],p=n.parents(e),u=p.find(s),h=n.parents(".awsm-b-jobs-pagination"),f=u.data("listings"),v=u.data("specs"),w=u.data("lang"),g=u.data("search"),b=u.data("layout"),_=u.data("hide_expired_jobs"),j=u.data("other_options"),C=u.data("position_filling");r?(n.prop("disabled",!0),d=void 0===(d=n.data("page"))?1:d):(n.parents(".page-numbers").find(".page-numbers").removeClass("current").removeAttr("aria-current"),n.addClass("current").attr("aria-current","page")),h.addClass("awsm-jobs-pagination-loading");var y=p.find(t+" form");if(l(y)&&(m=y.find(".awsm-filter-option").serializeArray()),!r){var x=n.attr("href"),P=x.split("?"),S="";if(P.length>1){var k=new URLSearchParams(P[1]);d=k.get("paged"),k.delete("paged"),k.toString().length>0&&(S="?"+k.toString())}x=P[0]+S,m.push({name:"awsm_pagination_base",value:P[0]+S}),awsmJobsPublic.deep_linking.pagination&&c("paged",d,x)}if(awsmJobsPublic.is_tax_archive){var J=u.data("taxonomy"),D=u.data("termId");void 0!==J&&void 0!==D&&m.push({name:"awsm_job_spec["+J+"]",value:D})}m.push({name:"action",value:"block_loadmore"},{name:"paged",value:d}),void 0!==f&&m.push({name:"listings_per_page",value:f}),void 0!==v&&m.push({name:"shortcode_specs",value:v}),void 0!==b&&m.push({name:"layout",value:b}),void 0!==_&&m.push({name:"hide_expired_jobs",value:_}),void 0!==j&&m.push({name:"other_options",value:j}),void 0!==C&&m.push({name:"position_filling",value:C}),void 0!==w&&m.push({name:"lang",value:w}),void 0!==g&&m.push({name:"jq",value:g});var A=o(u);A.length>0&&(m=m.concat(A)),a.ajax({url:awsmJobsPublic.ajaxurl,data:a.param(m),type:"POST",beforeSend:function(){r?n.text(awsmJobsPublic.i18n.loading_text):u.addClass("awsm-jobs-loading")}}).done((function(e){if(e){var s=h.data("effectDuration");h.remove(),r?u.append(e):(u.html(e),u.removeClass("awsm-jobs-loading"),void 0!==s&&(s=isNaN(s)?s:Number(s),a("html, body").animate({scrollTop:p.offset().top-25},s)))}else n.remove();a(document).trigger("awsmjobs_load_more",[n,e])})).fail((function(a){console.log(a)}))})),p(a(".awsm-job-select-control")),p(a(".awsm-filter-item select")),a(document).on("click",".awsm-filter-toggle",(function(e){e.preventDefault();var s=a(this);s.toggleClass("awsm-on"),s.hasClass("awsm-on")?s.attr("aria-pressed","true"):s.attr("aria-pressed","false"),s.parent().find(".awsm-filter-items").slideToggle()})),a(".awsm-filter-wrap").not(".awsm-no-search-filter-wrap").length>0&&(u(),a(window).on("resize",u))}))}();