# This repo's own concerns

Use this file for anything touching custom post types/taxonomies, custom capabilities, settings
storage, or uninstall/activation lifecycle. These are unique to this plugin's own data model — the
generic Settings API/CPT guidance in the core `wp-plugin-development` skill doesn't capture the
specific shape this repo actually uses.

## Custom post types & taxonomies (`inc/class-awsm-job-openings-core.php`)

- `awsm_job_openings` — `capability_type => 'job'`, `map_meta_cap => true`, `show_in_rest => true`,
  public with full UI, `supports` includes `title`/`editor`/`excerpt`/`author`/`custom-fields` (plus
  conditional `thumbnail`), rewrite slug configurable via the `awsm_permalink_slug` option (default
  `jobs`). `remove_post_type_support( ..., 'autosave' )` is called right after registration —
  intentional; don't reintroduce autosave for this CPT without understanding why it was removed.
- `awsm_job_application` — `capability_type => 'application'`, `capabilities => ['create_posts' =>
  'do_not_allow']` (applications are never created through the normal "Add New" UI, only
  programmatically via form submission), `public => false`, shown under the Job Openings menu,
  `supports => false`, `rewrite => false`.
- No taxonomies are registered in this file with `register_taxonomy()` directly — job specification
  taxonomies (e.g. job type, department) are registered **dynamically**, one per admin-configured
  filter spec, from `wp-job-openings.php`'s `awsm_jobs_taxonomies()`, with `show_ui`/`show_in_menu`
  set to `false` (they're managed through the plugin's own settings UI, not the default taxonomy
  admin screens) and `query_var => true`.

## Custom capabilities and the `hr` role

`inc/class-awsm-job-openings-core.php`'s `get_caps()` defines four capability "levels":
`manage_awsm_jobs` (level 4, admin-only), plus `edit_jobs`/`edit_applications`/
`edit_others_applications`/`hiring_panelist` at other levels, granted to different combinations of
`administrator`/`editor`/`author` via `manage_default_roles_caps()`. A dedicated `hr` role is also
created (`add_custom_role()`, run on plugin activation) with all these caps plus `read`/
`upload_files` — for a site owner who wants an HR-focused role without full `editor`/`administrator`
access. `remove_custom_role()`/`remove_role_caps()` mirror this on deactivation/uninstall
respectively — **deactivation removes the role's caps grant, but only uninstall actually deletes the
`hr` role itself** (matches "deactivation is reversible, uninstall is destructive," see below). If
you add a new capability-gated feature, decide deliberately which of the existing levels it belongs
to rather than inventing a new capability name for something an existing one already covers.

## Settings storage — Settings-API-adjacent, not a textbook implementation

`admin/class-awsm-job-openings-settings.php` calls `register_setting()` per option (many individual
options, e.g. `awsm_job_company_name`, `awsm_permalink_slug`, `awsm_jobs_filter`,
`awsm_jobs_recaptcha_secret_key` — not one serialized options blob) with a per-field sanitize
callback (`sanitize_text_field` by default, or a custom one like `sanitize_permalink_slug`/
`sanitize_html_content`/`sanitize_array_fields`). **It does not use `add_settings_section()`/
`add_settings_field()`/`do_settings_sections()`** — fields are rendered through the plugin's own
custom templates (`admin/templates/*.php`), not the built-in Settings API renderer. If you're
adding a new setting, follow the existing per-option `register_setting()` + custom-template pattern,
not a fresh `add_settings_field()` call that nothing will actually render.

Capability gating for the settings page/save goes through the `option_page_capability_{group}`
filter, which this plugin points at its own `manage_awsm_jobs` capability rather than core's default
`manage_options` — the settings submenu registration and its AJAX handler both check the same
capability, so gating stays centrally consistent. See `security.md`.

## Uninstall — opt-in and destructive, activation/deactivation are not

- `uninstall.php` at the plugin root (no `register_uninstall_hook()` call — this file *is* the
  mechanism) is gated by the `awsm_delete_data_on_uninstall` option: if it isn't explicitly set to
  `'delete_data'`, uninstall does nothing and all plugin data survives removal. This is an
  admin-facing opt-in, not a default-destructive uninstall — don't change this default without a
  very deliberate reason; it protects users who uninstall accidentally or temporarily.
- When the opt-in is set, `AWSM_Job_Openings_Uninstall::uninstall()` (all-static class) runs, in
  order: clears cron jobs, removes specification-taxonomy terms, deletes application attachments
  (`wp_delete_attachment(..., true)`) then bulk-deletes `awsm_job_openings`/`awsm_job_application`/
  `awsm_job_form` posts and orphaned postmeta, strips all custom capabilities from every role and
  removes the `hr` role, then deletes roughly 90 named plugin options.
- **What's preserved even on full opt-in uninstall**: the physical `wp-content/uploads/awsm-job-openings/`
  directory itself is never removed — only the database attachment rows parented to deleted
  applications are. Don't assume uninstall leaves zero filesystem trace.

## Activation / deactivation specifics

- Both hooks are registered once, at the very bottom of `wp-job-openings.php`, against the single
  plugin instance — see `wp-best-practices.md`.
- `activate()` runs, in order: seed default settings (once, gated by `awsm_register_default_settings`),
  `AWSM_Job_Openings_Core::register()` (registers post types + grants capabilities/role),
  seed default specification taxonomy terms, create the default "Jobs" page (content depends on
  `get_active_page_builder()` — see `frontend-rendering.md`), `flush_rewrite_rules()`, and set up a
  fresh-install redirect transient.
- `deactivate()` clears transients and cron jobs, unregisters post types/capabilities via
  `AWSM_Job_Openings_Core::unregister()`, and flushes rewrite rules — **it does not delete any
  data**, matching the "deactivation is reversible" principle above. Only the opt-in uninstall flow
  actually deletes data.

## Verification specific to this plugin

- After touching CPT/capability registration, confirm both activation (fresh install) and
  deactivation/reactivation (existing install) still leave the `hr` role and custom capabilities in
  the expected state — these are easy to get half-right in one direction only.
- After touching the uninstall class, test with `awsm_delete_data_on_uninstall` **unset** (default —
  confirm nothing is deleted) and explicitly set to `'delete_data'` (confirm the full cleanup runs) —
  don't only test the destructive path.
- Confirm any new setting follows the per-option `register_setting()` + custom-template pattern
  above, with a real sanitize callback, rather than a raw `update_option()` call with no
  registration.
