# JavaScript

Use this file for JS work in this repo. There are genuinely **two eras of JS coexisting** here —
match whichever the file you're editing already uses; don't force one style onto the other as part
of an unrelated change.

## Two styles in this codebase

1. **Legacy jQuery admin/public JS** (`assets/js/admin/admin.js` ~945 lines,
   `assets/js/admin-overview/overview.js` ~173 lines, `assets/js/public/job-application.js`
   ~332 lines, `assets/js/public/job-listings.js` ~444 lines) — `'use strict'` +
   `jQuery(document).ready(function($) { ... })` (or the `jQuery(function($){...})` shorthand in
   `job-listings.js`). PHP data reaches these files via `wp_localize_script()`:
   - `awsmJobsPublic` → handle `awsm-job-scripts`; carries `ajaxurl`, `block_nonce`
     (`wp_create_nonce( 'awsm_block_ajax' )`), `view_count_nonce`, `i18n`, etc.
   - `awsmJobsAdmin` → handle `awsm-job-admin`; carries `ajaxurl`, `nonce`
     (`wp_create_nonce( 'awsm-admin-nonce' )`), `i18n`, filter/spec data.
   - `awsmJobsAdminOverview` → handle `awsm-job-admin-overview`; carries `screen_id`,
     `analytics_data`, `i18n` — no `ajaxurl`/nonce, since it doesn't make its own AJAX calls.
2. **Modern, block-editor JS** (`blocks/src/*.js`) — React via WordPress's own bindings
   (`@wordpress/element`'s `useState`/`useEffect`/`Fragment`, `@wordpress/data`'s `useSelect`,
   `@wordpress/i18n`, `@wordpress/components`, `@wordpress/block-editor`). `edit.js` and
   `inspector.js` are function components using hooks; `save.js` is a pure JSX return
   (`useBlockProps.save()`) since the block is dynamic — see `frontend-rendering.md`.

## `blocks/src/view.js` is the one place jQuery leaks into the "modern" pipeline

Don't assume the block's build pipeline is jQuery-free just because it's built with
`@wordpress/scripts`. `blocks/src/view.js` (~1044 lines — the block's **frontend** filter/pagination
script, distinct from the editor-only `edit.js`) imports `jquery` directly and wraps its logic in
`jQuery(function($){...})`, structurally mirroring the legacy `assets/js/public/job-listings.js`.
This is expected — it runs on the public page, not inside the block editor, so there's no React
tree to hook into — but it means a genuinely jQuery-free block pipeline doesn't exist today. If
you're asked to remove the jQuery dependency from the block, `view.js` is where that work is, not
`edit.js`/`inspector.js`/`save.js` (which already have none).

## AJAX call patterns — match action names to the correct security tier

Legacy files call the backend with `jQuery.ajax`/`$.post`, never `fetch`. Before wiring a new call,
check `security.md` for which tier the target action name belongs to:

- `job-application.js` posts `action: 'awsm_view_count'` with `awsmJobsPublic.view_count_nonce` —
  ↔ `wp_ajax_awsm_view_count`/`wp_ajax_nopriv_awsm_view_count`. Nonce sent, as expected for a
  Tier-2-adjacent but still-nonced call.
- The application-submission form itself posts via `FormData(form)`; the `action` and nonce
  (`wp_nonce_field( 'awsm_application_nonce', 'awsm_nonce' )`) are embedded as hidden fields in the
  server-rendered form markup, not added inline in JS — don't assume a missing nonce in the JS file
  itself means the request is unauthenticated; check the form template too.
- `job-listings.js` posts `action: 'loadmore'` / the filter form posts `action: 'jobfilter'` — these
  intentionally carry **no** nonce field (Tier 2 in `security.md`); don't add one to "make it
  consistent" with the application form — the two have different security requirements.

## Modern (block editor) rules

- Function components with hooks, not class components.
- Batch related `setAttributes` calls into one call; spread nested style objects before updating a
  sub-key (`{ ...hz_sf_border, color: newColor }`, never a bare replacement).
- Keep `useEffect` dependency arrays exhaustive; run `npm run lint:js` (via `@wordpress/scripts`,
  from inside `blocks/`) — it catches missing/extra deps automatically.
- Debounce range/slider controls that call `setAttributes` on every `onChange` tick.
- Never wrap `ServerSideRender` in a custom `apiFetch` call — it breaks WordPress's built-in
  request-cancellation/dedup.

## Legacy (jQuery) rules

- Don't rewrite an existing jQuery file to vanilla JS or a framework as a drive-by change —
  `admin.js` alone is ~950 lines of large, working code; a rewrite is a separate, deliberate
  project, not a side effect of a small feature addition.
- Follow the existing localized-global-object pattern (`awsmJobsPublic`, `awsmJobsAdmin`,
  `awsmJobsAdminOverview`) for passing PHP data into these files, rather than introducing a second
  data-passing mechanism (inline `<script>` blocks, a new AJAX round-trip just to fetch config).

## Build pipeline — two independent pipelines, no shared config

- `gulpfile.js` + `config.js` at the repo root: concatenation/minification only (no bundler, no
  JSX/ESM transpilation) via `gulp-concat`/`gulp-uglify`/`gulp-clean-css`/`gulp-autoprefixer`, over
  `assets/css/*` and `assets/js/*`. This pipeline has **zero references to `blocks/`**.
- `blocks/package.json` delegates entirely to `@wordpress/scripts` (`wp-scripts build`/`start`/
  `lint:js`/`lint:css`/`plugin-zip`) — the standard WordPress zero-config webpack/Babel toolchain,
  with no custom `webpack.config.js` override. This is why `blocks/src/*.js` can use JSX/ESM imports
  directly while the gulp-built legacy files can't.
- Running `npm run build` at the root does **not** rebuild the block; run `npm run build` inside
  `blocks/` separately (or vice versa) — check which pipeline actually owns the file you changed
  before assuming one `npm run build` covers everything.

## What NOT to do

- Don't add a build step or transpilation-target change to either pipeline without checking the
  other isn't affected — they're independent by design.
- Don't add `console.log`/`debugger` statements left in for release.
