# WordPress best practices

Use this file for hook/filter design, lifecycle, backward compatibility, and "use the platform API,
don't reinvent it" concerns in this repo.

## Use WordPress APIs whenever possible

- File uploads go through WordPress's attachment pipeline (`wp_handle_upload()`,
  `wp_insert_attachment()`, `wp_generate_attachment_metadata()`) — don't hand-roll filesystem
  writes; see `security.md` § secure file uploads.
- Applicant status/notes changes go through WordPress's native `save_post` flow with a nonce
  (`awsm_save_post_meta`) and the CPT's own `edit_post` capability check — there's no bespoke
  "update status" AJAX handler; don't add one where the native post-save flow already covers it.
- Options are stored and gated through a Settings-API-adjacent pattern (`register_setting()` plus
  a custom capability filter) — see `project-specifics.md` for exactly how this repo's version
  differs from a textbook `add_settings_field()` setup.
- Cron/background work, if you add any, should use `wp_schedule_event()` — the plugin already does
  this for `awsm_check_for_expired_jobs` and `awsm_jobs_email_digest`; match that pattern rather than
  a custom scheduler.

## This plugin's hook surface is a real cross-plugin contract

This is the single most important fact about working in this repo, because unlike an add-on plugin,
**this plugin doesn't consume anyone else's hooks — it's the one being consumed**. Several sibling
plugins in the same product family (`pro-pack-for-wp-job-openings`, `job-alerts-for-wp-job-openings`,
`hirezoot-resume-scoring`, `auto-delete-applications-add-on-for-wp-job-openings`,
`user-access-control-for-wp-job-openings`) extend this plugin entirely through the filters/actions it
defines — none of them subclass or edit this plugin's files.

The block-rendering path alone (`inc/class-awsm-job-openings-block.php`,
`inc/template-functions-block.php`) exposes over two dozen filters/actions
(`awsm_jobs_block_attributes_set`, `awsm_jobs_listings_block_attributes`, `awsm_jobs_block_view_class`,
`hz_ui_styles`, `awsm_active_block_job_filters`, and many more — see `frontend-rendering.md` for the
fuller list) plus the AJAX action names themselves (`jobfilter`, `loadmore`, `block_jobfilter`,
`block_loadmore`, `awsm_applicant_form_submission`, `awsm_view_count`).

**Guardrails**:

- Never rename a hook or AJAX action name — the dependent plugin's callback silently stops firing,
  with no PHP error to surface the break.
- Never change a hook's callback argument count or order — existing callbacks (in this plugin or a
  sibling add-on) receive wrong values silently.
- When adding a new filter/action meant for an add-on to use, document it with a docblock at the
  `apply_filters`/`do_action` call site (see `coding-standards.md`).
- If a task genuinely requires renaming or re-signaturing an existing hook, that's a coordinated
  change across multiple repos, not a single-file edit — flag it before implementing rather than
  doing it unilaterally.
- Before trusting a sibling add-on's own skill/docs about which hook name it expects here, verify
  against this plugin's actual source — hook names have drifted from documentation before (e.g. the
  actual filter is `awsm_jobs_block_attributes_set`, not a name a sibling doc might reference
  slightly differently). Grep this repo, don't assume the other repo's doc is current.

## No custom REST API — and don't add one without a reason

There are **zero** `register_rest_route()` calls in this codebase. The `awsm_job_openings` CPT does
get WordPress core's default REST routes (`show_in_rest => true` in its `register_post_type()`
args) — that's core behavior from the CPT registration args, not bespoke endpoint code, and isn't
something to "harden" beyond the normal capability/visibility args already on the CPT. All dynamic
behavior instead goes through classic `wp_ajax_*`/`wp_ajax_nopriv_*` handlers (see `security.md`).
Don't migrate an existing AJAX handler to REST "to modernize it" without a concrete reason — that's
added risk for no behavior change. If a genuinely new use case needs a REST endpoint (e.g. a
headless frontend reading job listings), prefix the namespace with the plugin slug
(`wp-job-openings/v1/...`), give it a real `permission_callback` (never `__return_true` for anything
beyond public job-listing reads), and validate/sanitize via the route's `args` schema rather than
hand-checking inside the handler.

## Plugin lifecycle

- Activation and deactivation hooks are registered **once**, at the very bottom of
  `wp-job-openings.php`, against the single plugin instance — don't register additional
  activation/deactivation hooks elsewhere in the codebase.
- There is no `register_uninstall_hook()` call — uninstall runs through the standalone
  `uninstall.php` at the plugin root instead, which itself is gated by an opt-in destructive-uninstall
  option. See `project-specifics.md` for the exact flow.
- `load_classes()` requires `inc/` files unconditionally but `admin/` files only when `is_admin()` —
  keep new admin-only code behind that same guard to avoid loading admin UI classes on every
  frontend request.

## Backward compatibility

- Preserve existing hook names/signatures, option names, and public method signatures unless
  explicitly asked to break them — see the cross-plugin contract section above; the blast radius
  here is larger than a typical single-plugin change because of the sibling add-on ecosystem.
- Preserve the custom capability names (`manage_awsm_jobs`, `edit_jobs`, `edit_applications`,
  `edit_others_applications`, `hiring_panelist`) and their existing scope — don't quietly replace one
  with `manage_options` or a new capability for an existing admin action; see `security.md` and
  `project-specifics.md`.
- The `[awsmjobs]` shortcode, the Gutenberg block, and the two legacy widgets are all independently
  live, user-facing surfaces today (not one deprecated in favor of another) — a change to shared
  rendering logic needs to keep all of them working, not just the one you're actively testing. See
  `frontend-rendering.md`.
