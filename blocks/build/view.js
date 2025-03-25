!(function () {
	"use strict";
	jQuery(function (a) {
		var e = ".awsm-b-job-wrap",
			s = ".awsm-b-job-listings",
			t = ".awsm-b-job-listing-items",
			i = ".awsm-b-filter-wrap",
			n =
				window.location.protocol +
				"//" +
				window.location.host +
				window.location.pathname,
			o = !0;
		function r(e) {
			var s = [],
				t = ["listings", "specs", "search", "lang", "taxonomy", "termId"];
			t.push("awsm-layout"),
				t.push("awsm-hide-expired-jobs"),
				t.push("awsm-other-options"),
				t.push("awsm-listings-total"),
				t.push("awsm-selected-terms"),
				a(document).trigger("awsmJobBlockListingsData", [t]);
			var i = e.data();
			return (
				a.each(i, function (e, i) {
					-1 === a.inArray(e, t) && s.push({name: e, value: i});
				}),
				s
			);
		}
		function l(e) {
			var n = e.find(s),
				l = n.find(t),
				d = e.find(i + " form"),
				m = [];
			if (d.length > 0) {
				m = d.serializeArray();
				var c = d.attr("method") ? d.attr("method").toUpperCase() : "POST";
			} else m.push({name: "action", value: "block_jobfilter"}), (c = "POST");
			var h = n.data("listings"),
				p = n.data("specs"),
				u = n.data("awsm-layout"),
				f = n.data("awsm-hide-expired-jobs"),
				b = n.data("awsm-selected-terms"),
				w = n.data("awsm-other-options"),
				g = n.data("awsm-listings-total");
			if (
				(m.push({name: "listings_per_page", value: h}),
				void 0 !== p && m.push({name: "shortcode_specs", value: p}),
				void 0 !== u && m.push({name: "awsm-layout", value: u}),
				b)
			) {
				if ("string" == typeof b)
					try {
						b = JSON.parse(b);
					} catch (a) {
						console.error("Failed to parse selected_terms JSON:", a), (b = {});
					}
				m.push({name: "awsm-selected-terms", value: JSON.stringify(b)});
			}
			void 0 !== f && m.push({name: "awsm-hide-expired-jobs", value: f}),
				void 0 !== w && m.push({name: "awsm-other-options", value: w}),
				void 0 !== g && m.push({name: "awsm-listings-total", value: g});
			var v = r(n);
			if (
				(v.length > 0 && (m = m.concat(v)),
				a(document).trigger("awsmJobBlockFiltersFormData", [n, m]),
				o)
			) {
				o = !1;
				var j = d.length > 0 ? d.attr("action") : awsmJobsPublic.ajaxurl;
				a.ajax({
					url: j,
					beforeSend: function () {
						n.addClass("awsm-jobs-loading");
					},
					data: m,
					type: c
				})
					.done(function (s) {
						l.html(s);
						var t = e.find(".awsm-b-job-search");
						t.length > 0 &&
							(t.val().length > 0
								? (e.find(".awsm-b-job-search-btn").addClass("awsm-job-hide"),
								  e
										.find(".awsm-b-job-search-close-btn")
										.removeClass("awsm-job-hide"))
								: (e
										.find(".awsm-b-job-search-btn")
										.removeClass("awsm-job-hide"),
								  e
										.find(".awsm-b-job-search-close-btn")
										.addClass("awsm-job-hide"))),
							a(document).trigger("awsmjobs_filtered_listings", [e, s]);
					})
					.fail(function (a) {
						console.log(a);
					})
					.always(function () {
						n.removeClass("awsm-jobs-loading"), (o = !0);
					});
			}
		}
		function d(e) {
			var s = !1;
			return (
				e.length > 0 &&
					e.find(".awsm-b-filter-option").each(function () {
						a(this).val().length > 0 && (s = !0);
					}),
				s
			);
		}
		function m(a) {
			var t = a.parents(e),
				i = t.find(".awsm-b-job-search").val();
			if (
				(t.find(s).data("search", i),
				i.length,
				h(t, "jq", i),
				awsmJobsPublic.deep_linking.search)
			) {
				var n = t.find('input[name="awsm_pagination_base"]');
				c("jq", i, n.val());
			}
			l(t);
		}
		a(e).length > 0 &&
			a(e).each(function () {
				var e = a(this),
					s = e.find(i + " form");
				(awsmJobsPublic.is_search.length > 0 || d(s)) && ((o = !0), l(e));
			}),
			a(e).length > 0 &&
				a(e).each(function () {
					var e = a(this),
						s = e.find(i + " form"),
						t = !1;
					new URLSearchParams(window.location.search).toString().length > 0 &&
						(t = !0),
						(t || d(s)) && ((o = !0), l(e));
				});
		var c = function (a, e, s) {
				s = (s = void 0 !== s ? s : n).split("?")[0];
				var t = new URLSearchParams(document.location.search);
				t.has("paged") && t.delete("paged"),
					e.length > 0 ? t.set(a, e) : t.delete(a);
				var i = t.toString();
				i.length > 0 && (i = "?" + i),
					window.history.replaceState({}, "", s + i);
			},
			h = function (a, e, s) {
				var t = a.find('input[name="awsm_pagination_base"]');
				if (t.length > 0) {
					var i = t.val().split("?"),
						n = "";
					i.length > 1 && (n = i[1]);
					var o = new URLSearchParams(n);
					s.length > 0 ? o.set(e, s) : o.delete(e),
						t.val(i[0] + "?" + o.toString()),
						a.find('input[name="paged"]').val(1);
				}
			};
		function p() {
			a(".awsm-b-filter-wrap")
				.not(".awsm-b-no-search-filter-wrap")
				.each(function () {
					var e = a(this),
						s = e.find(".awsm-b-filter-item").first().offset().top,
						t = e.find(".awsm-b-filter-item").last().offset().top;
					window.innerWidth < 768
						? e.removeClass("awsm-b-full-width-search-filter-wrap")
						: t > s && e.addClass("awsm-b-full-width-search-filter-wrap");
				});
		}
		a(".awsm-b-job-no-more-jobs-get").length > 0 &&
			(a(".awsm-b-job-listings").hide(),
			a(".awsm-b-job-no-more-jobs-get").slice(1).hide()),
			a(i + " .awsm-b-filter-option").on("change", function (s) {
				s.preventDefault(), a(".awsm-b-job-listings").show();
				var t = a(this),
					i = t.closest(e),
					n = t.closest(".awsm-b-filter-item").data("filter"),
					o = t.prop("multiple"),
					r = t.find("option"),
					d = r.eq(0),
					m = t.find("option:selected"),
					p = d.prop("selected"),
					u = i.find("ul li"),
					f = (u.eq(0), u.filter(".selected"), []);
				i.find('input[type="checkbox"]').length,
					o
						? p
							? (r.prop("selected", !0).addClass("selected"),
							  u.addClass("selected"),
							  (f = r
									.slice(1)
									.map(function () {
										return a(this).data("slug");
									})
									.get()
									.filter(Boolean)))
							: 0 === m.length
							? (r.prop("selected", !1).removeClass("selected"),
							  u.removeClass("selected"),
							  (f = []))
							: (m.each(function () {
									a(this).prop("selected", !0).addClass("selected");
									var e = a(this).index();
									u.eq(e).addClass("selected");
							  }),
							  (f = m
									.map(function () {
										return a(this).data("slug");
									})
									.get()
									.filter(Boolean)))
						: (f = m.data("slug") ? [m.data("slug")] : []);
				var b = f.length > 0 ? f.join(",") : "";
				if (
					(a(".awsm-job-listings").length > 0 &&
						i.find(".awsm-b-job-no-more-jobs-get").hide(),
					h(i, n, b),
					awsmJobsPublic.deep_linking.spec)
				) {
					var w = i.find('input[name="awsm_pagination_base"]');
					c(n, b, w.val());
				}
				l(i);
			}),
			a(i + " .awsm-filter-checkbox").on("change", function (s) {
				var t = {},
					i = [],
					n = a(this),
					o = n.parents(e),
					r = n.parents(".awsm-filter-list-item").data("filter");
				a(".awsm-filter-checkbox:checked").each(function () {
					var e = a(this).data("taxonomy"),
						s = a(this).data("term-id"),
						n = a(this).data("slug");
					n && i.push(n), t[e] || (t[e] = []), t[e].push(s);
				});
				var d = i.length > 0 ? i.join(",") : "";
				if (awsmJobsPublic.deep_linking.spec) {
					var m = o.find('input[name="awsm_pagination_base"]');
					c(r, d, m.val());
				}
				l(o);
			}),
			a(i + " .awsm-b-job-search-btn").on("click", function () {
				m(a(this));
			}),
			a(i + " .awsm-b-job-search-close-btn").on("click", function () {
				var s = a(this);
				s.parents(e).find(".awsm-b-job-search").val(""), m(s);
			}),
			a(i + " .awsm-b-job-search").on("keypress", function (e) {
				13 == e.which && (e.preventDefault(), m(a(this)));
			}),
			a(s).on(
				"click",
				".awsm-b-jobs-pagination .awsm-b-load-more-btn, .awsm-b-jobs-pagination a.page-numbers",
				function (n) {
					n.preventDefault();
					var o = a(this),
						l = o.hasClass("awsm-b-load-more-btn"),
						m = 1,
						h = [],
						p = o.parents(e),
						u = p.find(s),
						f = u.find(t),
						b = o.parents(".awsm-b-jobs-pagination"),
						w = u.data("listings"),
						g = (u.data("total-posts"), u.data("specs")),
						v = u.data("lang"),
						j = u.data("search"),
						_ = u.data("awsm-layout"),
						y = u.data("awsm-hide-expired-jobs"),
						C = u.data("awsm-selected-terms"),
						x = u.data("awsm-other-options");
					l
						? (o.prop("disabled", !0),
						  (m = void 0 === (m = o.data("page")) ? 1 : m))
						: (o
								.parents(".page-numbers")
								.find(".page-numbers")
								.removeClass("current")
								.removeAttr("aria-current"),
						  o.addClass("current").attr("aria-current", "page")),
						b.addClass("awsm-b-jobs-pagination-loading");
					var S = p.find(i + " form");
					if (d(S)) {
						var k = S.find(".awsm-b-filter-option");
						h = k.serializeArray();
					}
					var J = {};
					for (var P in (S.find(".awsm-filter-checkbox:checked").each(
						function () {
							var e = a(this),
								s = e.data("taxonomy"),
								t = e.data("term-id");
							s && t && (J[s] || (J[s] = []), J[s].push(t));
						}
					),
					J))
						J.hasOwnProperty(P) &&
							J[P].forEach(function (a) {
								h.push({
									name: "awsm_job_specs_list[".concat(P, "][]"),
									value: a
								});
							});
					if (!l) {
						var O = o.attr("href"),
							N = O.split("?"),
							D = "";
						if (N.length > 1) {
							var q = new URLSearchParams(N[1]);
							(m = q.get("paged")),
								q.delete("paged"),
								q.toString().length > 0 && (D = "?" + q.toString());
						}
						(O = N[0] + D),
							h.push({name: "awsm_pagination_base", value: N[0] + D}),
							awsmJobsPublic.deep_linking.pagination && c("paged", m, O);
					}
					if (awsmJobsPublic.is_tax_archive) {
						P = u.data("taxonomy");
						var L = u.data("termId");
						void 0 !== P &&
							void 0 !== L &&
							h.push({name: "awsm_job_spec[" + P + "]", value: L});
					}
					if (
						(h.push(
							{name: "action", value: "block_loadmore"},
							{name: "paged", value: m}
						),
						void 0 !== w && h.push({name: "listings_per_page", value: w}),
						void 0 !== g && h.push({name: "shortcode_specs", value: g}),
						void 0 !== _ && h.push({name: "awsm-layout", value: _}),
						void 0 !== y && h.push({name: "awsm-hide-expired-jobs", value: y}),
						C)
					) {
						if ("string" == typeof C)
							try {
								C = JSON.parse(C);
							} catch (a) {
								console.error("Failed to parse selected_terms JSON:", a),
									(C = {});
							}
						h.push({name: "awsm-selected-terms", value: JSON.stringify(C)});
					}
					void 0 !== x && h.push({name: "awsm-other-options", value: x}),
						"undefined" != typeof listings_total &&
							h.push({name: "awsm-listings-total", value: listings_total}),
						void 0 !== v && h.push({name: "lang", value: v}),
						void 0 !== j && h.push({name: "jq", value: j}),
						a(document).trigger("awsmjobs_block_load_more", [u, h]);
					var T = r(u);
					T.length > 0 && (h = h.concat(T)),
						a
							.ajax({
								url: awsmJobsPublic.ajaxurl,
								data: a.param(h),
								type: "POST",
								beforeSend: function () {
									l
										? o.text(awsmJobsPublic.i18n.loading_text)
										: u.addClass("awsm-jobs-loading");
								}
							})
							.done(function (e) {
								if (e) {
									var s = b.data("effectDuration");
									b.remove(),
										l
											? f.append(e)
											: (f.html(e),
											  u.removeClass("awsm-jobs-loading"),
											  void 0 !== s &&
													((s = isNaN(s) ? s : Number(s)),
													a("html, body").animate(
														{scrollTop: p.offset().top - 25},
														s
													)));
								} else o.remove();
								a(document).trigger("awsmjobs_load_more", [o, e]);
							})
							.fail(function (a) {
								console.log(a);
							});
				}
			),
			a(document).on("click", ".awsm-b-filter-toggle", function (e) {
				e.preventDefault();
				var s = a(this);
				s.toggleClass("awsm-on"),
					s.hasClass("awsm-on")
						? s.attr("aria-pressed", "true")
						: s.attr("aria-pressed", "false"),
					s.parent().find(".awsm-b-filter-items").slideToggle();
			}),
			a(".awsm-b-filter-wrap").not(".awsm-b-no-search-filter-wrap").length >
				0 && (p(), a(window).on("resize", p));
	});
})();
