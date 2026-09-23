# CSRF / broken access control / IDOR

## Full AJAX handler inventory (test every row)

| Action | Handler (file:line) | Nonce action | Capability required | nopriv? |
|---|---|---|---|---|
| `awsm_jobs_setup` | `admin/class-awsm-job-openings-info.php:20` (`handle_setup`) | `awsm-jobs-setup` | `manage_options` | no |
| `settings_switch` | `admin/class-awsm-job-openings-settings.php:22` (`settings_switch_ajax`) | `awsm-admin-nonce` | `manage_awsm_jobs` | no |
| `awsm_plugin_rating` | `wp-job-openings.php:472` (`plugin_rating`) | `awsm-admin-nonce` | *(none checked — see test below)* | no |
| `awsm_job_status_panel` | `admin/class-awsm-job-openings-meta.php:28` (`ajax_job_status_panel`) | `awsm_job_status_panel` | `edit_post` on the target post (checked at `admin/class-awsm-job-openings-meta.php:126`) | no |
| `awsm_view_count` | `wp-job-openings.php:110-111` (`job_views_handler`) | `awsm_view_count_nonce` | none (by design — public view counter) | **yes** |
| `jobfilter` / `loadmore` | `inc/class-awsm-job-openings-filters.php:13-16` (`awsm_posts_filters`) | **none (intentional, Tier 2)** | none | **yes** |
| `block_jobfilter` / `block_loadmore` | `inc/class-awsm-job-openings-block.php:13-16` (`awsm_block_posts_filters`) | `awsm_block_ajax` at `inc/class-awsm-job-openings-block.php:193` | none | **yes** |
| `awsm_applicant_form_submission` | `inc/class-awsm-job-openings-form.php:34-35` (`ajax_handle`/`insert_application`) | `awsm_application_nonce` | none (public, by design — Tier 3) | **yes** |
| resume/file download | `admin/class-awsm-job-openings-meta.php:355` (`attached_file_download_handler`) — reached via `admin_action_download_resume`/`admin_action_download_file`, not `admin-ajax.php` | `awsm_{type}_download` | `edit_others_applications` | no |
| post-meta save on `save_post` | `wp-job-openings.php:1982` (`awsm_job_save_post`, not a standalone AJAX action) | `awsm_save_post_meta` | implicit via core's own `save_post`/`edit_post` capability check upstream | no |

Note the `block_jobfilter`/`block_loadmore` row: unlike its non-block twin (`jobfilter`/`loadmore`,
genuinely nonce-less), this one **does** check a nonce despite also being a read-only listing
endpoint — confirm this during testing rather than assuming the Tier 2 exemption in
`hirezoot/references/security.md` covers it too; if it turns out inconsistent, that's a
documentation gap to flag, not a vulnerability.

## Test cases

### AC-1: Nonce bypass on every Tier 1/3 handler

For each row above with a nonce action, replay the real request three ways and confirm all three are
rejected (`wp_die()`/error response, no state change):
1. Omit the nonce param entirely.
2. Send an empty string.
3. Send a syntactically-plausible but invalid nonce (e.g. `wp_create_nonce()` output for a
   *different* action string, so it's well-formed but wrong).

**Pass**: no state mutation occurs (option not updated, post meta not written, application not
created) and no data is returned. **Fail** if any variant still performs the action — cite the
missing/broken check at the file:line in the table.

### AC-2: `awsm_plugin_rating` nonce-check-without-die

`wp-job-openings.php`'s `plugin_rating()` handler appends an error message to `$response['errors']`
on nonce failure but does **not** call `wp_die()`/return immediately — it relies on a later
`count( $response['errors'] ) === 0` gate to skip the `update_option()`/`set_transient()` calls.
Confirm this gate actually holds under a forged/missing nonce: send the request with no nonce and a
valid `context`/`status` param, and confirm the option is *not* updated. This is a low-severity
pattern (no direct bypass today) but is fragile to a future edit that reorders the checks — flag it
if you find any code path where the mutation runs before the error count is checked.

### AC-3: Capability-boundary tests (per custom capability)

Using the test users from `setup-and-tooling.md`, replay each Tier 1 handler and the download handler
as a user who is authenticated but lacks the *specific* required capability (not just "not an admin"):
- `settings_switch` as `sectest_editapp` (has `edit_applications`, not `manage_awsm_jobs`) → must be
  rejected at `admin/class-awsm-job-openings-settings.php:985`.
- `awsm_jobs_setup` as `sectest_pluginadmin` (has `manage_awsm_jobs`, not core `manage_options`) →
  must be rejected at `admin/class-awsm-job-openings-info.php:54`. This is the one handler that
  deliberately requires the *higher* core capability instead of the plugin's own — confirm it wasn't
  accidentally loosened to `manage_awsm_jobs`.
- Resume/file download as `sectest_editapp` (has `edit_applications`, not `edit_others_applications`)
  → must be rejected; only `sectest_editothers` should succeed. This is the capability that gates
  applicant PII (resumes contain names, contact info, sometimes full CVs) — treat any bypass here as
  high severity.
- `settings_switch`'s option whitelist (`admin/class-awsm-job-openings-settings.php:996`,
  `apply_filters( 'awsm_jobs_switchable_settings_options', ... )`): as a user who *does* have
  `manage_awsm_jobs`, attempt to pass an `option` param **not** in the default whitelist
  (`awsm_jobs_acknowledgement`, `awsm_jobs_enable_admin_notification`) — e.g. try
  `option=awsm_jobs_admin_upload_file_ext` or an arbitrary core option like `option=siteurl`. Confirm
  the handler ignores/rejects anything outside the filtered whitelist; this is what stops the handler
  from being an arbitrary-option-overwrite primitive for an otherwise-legitimate plugin-admin user (or
  for a plugin-admin whose account is compromised via a separate low-severity issue).

### AC-4: IDOR on the resume/attachment download handler

`attached_file_download_handler()` (`admin/class-awsm-job-openings-meta.php:355`) gates on
`current_user_can( 'edit_others_applications' )` — a *global* capability, not tied to a specific
post/application. That's the documented design (this capability means "can see all applications"),
so enumerating `awsm_id` across different applications while holding this capability is **expected
behavior, not a bug**. What to actually test:
1. As a user who holds `edit_others_applications`, pass an `awsm_id` that does **not** belong to any
   `awsm_job_application`'s attachment (e.g. a media-library image from an unrelated post, or the ID
   of a core post) — check whether `get_attached_file_details()` validates the attachment's parent
   post type before serving it, or will happily `readfile()` anything on disk with that attachment ID.
   If it serves arbitrary site media through this endpoint, that's a scope-creep information
   disclosure (not privilege escalation, since the requester already has a broad capability, but
   still worth flagging if it can reach files outside the intended applications context).
2. Confirm a user who lacks `edit_others_applications` gets rejected regardless of which `awsm_id`
   they try (i.e., the capability check happens before the ID is even looked at) — replay with
   several different `awsm_id` values as `sectest_editapp`.
3. Confirm the nonce is genuinely bound to the specific `awsm_id`/type pair and not just "any valid
   download nonce works for any ID" — request a nonce for one attachment/type combination, then
   replay it against a *different* `awsm_id` or the other `type` (`resume` vs `file`). `wp_verify_nonce`
   here only checks the action string `'awsm_' . $type . '_download'`, which does **not** include the
   attachment ID — confirm whether a resume-download nonce for application A can be replayed to
   download application B's resume (same type). This is expected to work per the current code (the
   nonce isn't per-ID) since the capability check is the real boundary; treat this as background
   information for the finding above, not a separate bug, unless the capability check can *also* be
   bypassed.

### AC-5: `save_post` meta-save nonce read without `sanitize_key()`/`wp_unslash()`

`wp-job-openings.php:1982` reads `$_POST['awsm_jobs_posts_nonce']` directly into `wp_verify_nonce()`
without the `sanitize_key( wp_unslash( ... ) )` wrapping used elsewhere in this codebase (see
`hirezoot/references/security.md`'s note on the same shortcut in the download-nonce read). Confirm
this is genuinely low-risk (as documented) by attempting to pass an array or slashed string as the
nonce value and confirming `wp_verify_nonce()` degrades safely (returns falsy) rather than throwing a
type error or being coerced into truthy — WordPress core's own type-juggling inside
`wp_verify_nonce()`/`hash_equals()` should handle this, but confirm on the actual WP version in use.

### AC-6: Tier 2 endpoints are genuinely read-only

For `jobfilter`/`loadmore`/`block_jobfilter`/`block_loadmore`, confirm the "no nonce is fine because
it's read-only" premise actually holds by checking these handlers never call any `update_*`/
`wp_insert_*`/`wp_delete_*`/`set_transient` function — grep
`inc/class-awsm-job-openings-filters.php` and `inc/class-awsm-job-openings-block.php` for any write
call. If a future edit adds one (e.g. a "save this search" feature bolted onto `jobfilter`), that
handler needs to move to the Tier 3 pattern (nonce + real validation), not keep Tier 2's exemption —
flag it if you find any write call already present.

### AC-7: `awsm_view_count` — public counter abuse

`job_views_handler()` (`wp-job-openings.php:1318`) is nonce-checked (`awsm_view_count_nonce`) despite
being `nopriv`, unlike the Tier 2 listing endpoints. Since the nonce is emitted on every page load of
a job post and isn't otherwise rate-limited, confirm (informational, not a hard fail) that repeated
requests with a valid nonce can inflate `awsm_views_count` post meta arbitrarily — this is a
view-count integrity issue (low severity: cosmetic metric, not a security boundary), not something to
fix via nonce hardening. Worth a one-line note in the report, not a high-severity finding.
