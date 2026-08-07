# Performance

Use this file for changes touching job-listing rendering (block/shortcode/widget), asset
enqueueing, or queries.

## Conditional asset loading — a known gap, not a template

`blocks/class-awsm-job-guten-blocks.php`'s `block_assets()` is hooked on `enqueue_block_assets`,
which fires on **both** the editor and every frontend page load. It enqueues admin-only assets
behind an `is_admin()` check, but enqueues `awsm-job-scripts`/`awsm-jobs-style` unconditionally —
**no `has_block()` guard**. Likewise, the general frontend enqueue (`wp-job-openings.php`'s
`wp_enqueue_scripts` → `awsm_enqueue_scripts`) has no `has_block()` guard either. The only
`has_block()` call anywhere in the plugin is unrelated to asset loading (it decides default page
content on activation).

**Guardrail**: treat this as a known pattern to avoid, not copy. Any new frontend script/style
enqueue should check `has_block( 'wp-job-openings/blocks' )` (or an equivalent guard for whatever
content it serves) before enqueueing, so the cost is paid only on pages that actually use the
feature. Don't "fix" the existing unconditional enqueues as a side effect of an unrelated change —
that's a real behavior change (assets stop loading on pages that relied on them via the shortcode or
widget, which don't go through this same enqueue path) — flag it separately if you notice it.

## Caching

This plugin doesn't lean on transients or object caching for job-listing data — rendering re-queries
`WP_Query` per request. If you add an expensive, rarely-changing lookup (e.g. a specification/
taxonomy option list used to populate block/Elementor-style controls), consider
`wp_cache_get()`/`wp_cache_set()` or a transient, but confirm the data is actually expensive/stable
enough to warrant a cache layer before adding one for a cheap query — most of what this plugin
queries (a page of jobs, a job's own postmeta) isn't.

## Efficient queries

- There is **no raw `$wpdb` usage anywhere in this codebase** — every read goes through `WP_Query`/
  `get_posts()`/`get_post_meta()`. Prefer the same: explicit `fields`, `posts_per_page`/
  `numberposts`, and `post_type` args rather than pulling full post objects when only IDs or a count
  are needed.
- Avoid N+1 patterns: if rendering a list of jobs each needs a per-job specification/taxonomy
  lookup, batch that lookup once for the whole list rather than querying per iteration — check
  `inc/class-awsm-job-openings-block.php`'s existing filter-term lookups for the pattern already
  used there before adding a new per-item query.
- The `jobfilter`/`loadmore`/`block_jobfilter`/`block_loadmore` AJAX handlers
  (`inc/class-awsm-job-openings-filters.php`, `inc/class-awsm-job-openings-block.php`) are the
  highest-frequency query path in the plugin (fired on every filter/pagination interaction on a
  public listing page) — keep any change to their query-building path cheap; this is not the place
  to add a new per-request expensive lookup.

## Rendering cost

- The block's render path (`inc/class-awsm-job-openings-block.php`, `inc/template-functions-block.php`)
  exposes a long list of filters/actions (see `frontend-rendering.md`) that sibling add-ons hook
  into — those callbacks run on **every render** of the listing, block or shortcode. If you're
  reviewing or adding a filter callback (in this plugin or a sibling add-on), keep it cheap; an
  expensive computation inside one of these filters runs once per listing render, not once per page
  load.
- The shortcode (`[awsmjobs]`) and the block share underlying render logic — a change to shared
  code affects both surfaces' rendering cost, not just whichever one you were testing.

## Memory / duplicate work

- `AWSM_Job_Openings_Core`, `AWSM_Job_Openings_Settings`, and the other singleton classes are each
  constructed once via their own `init()`/`get_instance()` — don't introduce a second instantiation
  path (e.g. `new AWSM_Job_Openings_Core()` directly) for any of them, since that would register
  every hook callback twice and double any per-render work.
- `load_classes()` (`wp-job-openings.php`) only requires the `admin/` class files when `is_admin()`
  is true — keep new admin-only classes behind that same guard rather than loading them
  unconditionally on the frontend.
