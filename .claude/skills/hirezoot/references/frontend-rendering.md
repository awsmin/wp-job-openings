# Frontend rendering: block, shortcode, widgets, templates

Use this file for anything touching how job listings actually get onto a page — the Gutenberg
block, the `[awsmjobs]` shortcode, the legacy widgets, or the archive/single template override.
This is also the plugin's primary extension surface for sibling add-ons — see the hook list below
and `wp-best-practices.md`'s cross-plugin contract section before renaming anything here.

## The Gutenberg block is dynamic, not static

- Block name: `wp-job-openings/blocks` (`blocks/src/block.json`), `apiVersion: 3`, ~35 attributes
  (search/filter options, layout, pagination, per-element style objects like `hz_sf_border`, etc.).
- `save.js` returns only an empty wrapper `<p {...useBlockProps.save()}></p>` — **no real markup is
  saved to `post_content`**. All actual output comes from a server-side `render_callback`
  (`blocks/class-awsm-job-guten-blocks.php`, delegating to
  `AWSM_Job_Openings_Block::awsm_jobs_block_attributes()` in `inc/class-awsm-job-openings-block.php`).
  `edit.js` mirrors this by rendering via `@wordpress/server-side-render`'s `ServerSideRender`
  rather than a hand-built React preview.
- **Guardrail**: because the block is dynamic, any new attribute needs a matching read in the PHP
  render path — adding an attribute to `block.json` alone changes nothing visible. And because
  `save.js` saves no markup, there's no block-deprecation/migration concern for attribute changes
  the way there would be for a static block with saved markup — but do keep `ServerSideRender`
  wired for editor previews of any new attribute.

## The hook surface this plugin's block rendering exposes

`inc/class-awsm-job-openings-block.php` and `inc/template-functions-block.php` define this plugin's
richest filter/action surface — the exact seam Pro Pack and other add-ons hook into. A non-exhaustive
list of what's already there (grep for `apply_filters`/`do_action` in both files before assuming a
name — new ones get added occasionally): `awsm_jobs_block_supported_filter_types`,
`awsm_jobs_block_supported_layouts`, `awsm_jobs_block_supported_list_types`,
`awsm_jobs_block_attributes_set`, `awsm_jobs_block_output_content`, `awsm_jobs_block_view_class`,
`awsm_jobs_block_post_filters`, `awsm_block_no_filtered_jobs_content`,
`awsm_jobs_block_selected_terms_query`, `awsm_job_block_query_args`,
`awsm_block_job_listing_data_attrs`, `awsm_block_filter_terms`, `awsm_active_block_job_filters`,
`awsm_jobs_block_featured_image_content`, `awsm_job_block_listing_item_class`, `hz_ui_styles`, and
the `awsm_jobs_listings_block_attributes` filter that the top-level `render_callback` applies to the
final attributes array before rendering.

**Guardrail**: treat every one of these as a public API, even though nothing in the code enforces
that. See `wp-best-practices.md` for the rename/re-signature rules. If you're adding a genuinely new
Pro-style capability to the free plugin itself (not an add-on), prefer adding a new filter at the
relevant point over overloading an existing one's meaning.

## Assets

`blocks/class-awsm-job-guten-blocks.php`'s `block_assets()` is hooked on `enqueue_block_assets`
(fires in both the editor and on every frontend page) and enqueues the block's frontend script/style
unconditionally — no `has_block()` guard. See `performance.md` before adding more unconditional
enqueues here.

## The `[awsmjobs]` shortcode is a real, actively-used alternative — not a legacy leftover

`add_shortcode( 'awsmjobs', ... )` (`wp-job-openings.php`) registers a handler
(`awsm_jobs_shortcode()`) that's still the default seeded content for the plugin's auto-created
"Jobs" page **whenever a non-Gutenberg page builder is detected active** (see below) — it isn't a
deprecated code path. Any change to the shared listing-render logic needs to keep working through
both the block and the shortcode entry points.

## Legacy widgets — still registered, not deprecated

- `AWSM_Job_Openings_Recent_Jobs_Widget` (`inc/widgets/class-awsm-job-openings-recent-jobs-widget.php`)
  is a classic `WP_Widget` subclass, registered via `register_widget()` on `widgets_init`. It works
  through the Widgets screen and the Legacy Widget block; nothing in the code marks it deprecated.
- `AWSM_Job_Openings_Dashboard_Widget` (`inc/widgets/class-awsm-job-openings-dashboard-widget.php`)
  uses the dashboard-widget API directly (`wp_add_dashboard_widget()` on `wp_dashboard_setup`),
  gated by the `edit_jobs` capability.

## Template overriding for the CPT's archive/single views

- `single_template`/`archive_template` filters (`wp-job-openings.php`) swap in the plugin's own
  template when the queried post is `awsm_job_openings`, conditional on an admin-configured
  "use plugin template" option per view.
- `the_content` filter (priority 100) injects job-detail markup for singular `awsm_job_openings`
  requests.
- **Themes can override plugin templates**: `get_template_path()` checks the active theme's
  `wp-job-openings/` subdirectory first, falling back to the plugin's own `inc/templates/` — and the
  resolved path itself passes through a documented `apply_filters()` call, so a theme or add-on can
  redirect the lookup entirely without needing the file-in-theme convention. Don't assume
  `inc/templates/*.php` is the only place these templates can live when debugging a rendering issue
  — check the active theme for an override first.

## Page-builder detection: narrower than it sounds

`get_active_page_builder()` (`wp-job-openings.php`) detects Elementor, Divi, Beaver Builder,
WPBakery, Bricks, or Visual Composer via `defined()`/`class_exists()` checks. **The only thing this
detection currently changes is which default content gets seeded into the auto-created "Jobs" page
on activation** — block markup if no other builder is active, the `[awsmjobs]` shortcode if one is.
It does **not** register a builder-specific widget for any of these builders, and there is currently
**no Elementor (or other page-builder) widget anywhere in this plugin** — if you're told a sibling
plugin extends "this plugin's Elementor widget," verify that claim against this plugin's actual
source before building on it; it may be stale, may refer to a class that hasn't been added yet, or
may describe a different plugin. Don't build a new page-builder-specific widget speculatively —
confirm the actual ask first.
