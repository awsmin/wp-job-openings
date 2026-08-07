# Security

Use this file for any change touching input handling, output rendering, AJAX, or file uploads in
this repo. This plugin's largest untrusted-input surface is the public job-application form and its
file-upload flow — ground new work in the patterns already used there, not generic textbook advice.

## Golden rule

Sanitize/validate on input, escape on output. Both are required; neither substitutes for the other.

## This repo has two tiers of AJAX handler — and, unusually, one tier splits again

### Tier 1: admin/authenticated handlers

Reachable only by a logged-in user with plugin capabilities:

- `wp_ajax_awsm_jobs_setup` (`admin/class-awsm-job-openings-info.php`) — setup wizard.
- `wp_ajax_settings_switch` (`admin/class-awsm-job-openings-settings.php`) — settings toggles.

Pattern (`admin/class-awsm-job-openings-settings.php` `settings_switch_ajax`):

```php
if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'awsm-admin-nonce' ) ) {
    wp_die();
}
if ( ! current_user_can( 'manage_awsm_jobs' ) ) {
    wp_die( ... );
}
```

Notes:

- The capability is the plugin's own **custom capability `manage_awsm_jobs`**, not
  `manage_options` — use this same capability for new admin-only handlers so permissions stay
  centrally manageable. The one deliberate exception is the setup wizard
  (`admin/class-awsm-job-openings-info.php`'s `handle_setup()`), which requires the higher core
  `manage_options` capability — appropriate since it's a one-time site-setup flow, not routine
  plugin administration.
- `settings_switch_ajax` additionally whitelists which option names can be updated via
  `apply_filters( 'awsm_jobs_switchable_settings_options', ... )` before calling `update_option()` —
  don't remove that whitelist to "simplify" a new switchable setting; it's what prevents the handler
  from being turned into an arbitrary-option overwrite.
- Resume/attachment downloads (`admin/class-awsm-job-openings-meta.php`) are gated by
  `current_user_can( 'edit_others_applications' )` **and** a per-download nonce (action
  `awsm_{type}_download`). The nonce value there is read from `$_GET` without `wp_unslash()`/
  `sanitize_key()` first — low risk since `wp_verify_nonce()` itself validates format, but don't
  copy that shortcut into new code; use `sanitize_key( wp_unslash( $_GET['awsm_nonce'] ) )` like the
  rest of the codebase does.

### Tier 2: public, read-only listing handlers — intentionally nonce-less

`jobfilter`/`loadmore` and their block-editor counterparts `block_jobfilter`/`block_loadmore`
(`inc/class-awsm-job-openings-filters.php`, `inc/class-awsm-job-openings-block.php`) have **no nonce
check**, marked explicitly:

```php
// phpcs:disable WordPress.Security.NonceVerification.Missing
```

This is intentional, not an oversight: these are read-only `WP_Query` listing/pagination endpoints —
no state mutation, no privilege boundary crossed — so a nonce adds CSRF friction with no actual
security benefit (CSRF exists to protect state-changing requests). Inputs are individually
sanitized (`sanitize_text_field()`, `intval()`, `absint()`) before being used to build query args.

**Guardrail**: if you add a new public listing/read endpoint, this pattern (no nonce, strict
per-field sanitization, no write operation) is fine to copy. If the new endpoint does anything that
writes data or reveals non-public information, it needs Tier 3's approach instead, not this one.

### Tier 3: public, write-capable handler — validation instead of a nonce

`awsm_applicant_form_submission` (`inc/class-awsm-job-openings-form.php`) is the one public handler
that **does** mutate state (creates an `awsm_job_application` post + attachments) but is reachable
by anonymous visitors. Unlike Tier 2, it **does** check a nonce
(`wp_verify_nonce( sanitize_key( $_POST['awsm_nonce'] ), 'awsm_application_nonce' )`, field emitted
in `inc/templates/single-job/form.php`) — a nonce works here because the form is rendered per page
load, unlike a truly anchor-less anonymous API. On top of the nonce it layers real server-side
validation before anything is written:

- The target job must resolve to an actual `awsm_job_openings` post and be `publish`/non-expired.
- Required fields (name/email/phone/cover letter) are checked for presence; phone is regex-validated.
- CAPTCHA/reCAPTCGA is checked when enabled; a GDPR-consent flag is checked when required.
- File presence and PHP's own upload-error code are checked before the file is touched.

**Guardrail for any new public write-capable handler**: don't skip the nonce just because Tier 2's
handlers skip theirs — Tier 2's exemption is specifically because those endpoints don't write
anything. A new write-capable public handler needs both the nonce **and** this depth of server-side
validation (real post-type/ownership checks, a server-side-known field schema, never trust a
client-supplied field name or size) — a nonce alone or validation alone is not equivalent to both.

## Sanitization (input)

- Never process the entire `$_POST`/`$_GET`/`$_FILES` array; read explicit keys.
- Use `wp_unslash()` before sanitizing (as `inc/class-awsm-job-openings-form.php` does throughout).
- Use the most specific sanitizer: `sanitize_text_field()`, `sanitize_email()`, `absint()`/`intval()`
  for IDs, the plugin's own `awsm_jobs_sanitize_textarea()` for multi-line text, etc.

## Escaping (output)

| Context | Function |
|---|---|
| HTML content | `esc_html()` |
| HTML attribute | `esc_attr()` |
| URL | `esc_url()` |
| Translated string in HTML | `esc_html__()` / `esc_attr__()` |
| Mail-template admin-configurable content | `wp_kses()` with a filterable allowlist (see below) |

No raw `echo $variable` without escaping; no echoing `$_GET`/`$_POST` directly. The codebase already
follows this consistently in `inc/templates/` and `admin/templates/`. The few places that echo an
`apply_filters()` result unescaped (e.g. two spots in `inc/class-awsm-job-openings-form.php`) carry
an explicit `// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped` — that content is
developer-controlled markup returned by a filter, not raw user input; it's an accepted, documented
tradeoff. Don't copy the ignore-comment onto a new call site unless the same condition genuinely
holds (filtered content is dev/theme-controlled, not end-user input).

For admin-configurable **content** that does carry a risk of user-influenced input (e.g. the mail
customizer's footer text), the codebase uses `wp_kses( $content, $allowed_html )` — with
`$allowed_html` itself filterable (`awsm_jobs_notification_customizer_allowed_html`) — rather than
either raw escaping or raw trust. Reach for this pattern (sanitize-with-an-HTML-allowlist), not a
bare `esc_html()`, wherever admin-authored rich text needs to keep some markup.

## SQL injection prevention

There is **no raw `$wpdb` usage anywhere in this codebase** — every query goes through `WP_Query`,
`get_posts()`, `get_post_meta()`/`update_post_meta()`, or `wp_insert_post()`/`wp_insert_attachment()`.
If you're tempted to add a raw `$wpdb` query for a new feature, that's a signal to first check
whether `WP_Query`/`get_posts()` can do it instead — this repo has never needed to reach past those
APIs. If a raw query genuinely is necessary, always use `$wpdb->prepare()`; check
`wp-plugin-development`'s `data-and-cron.md` on `%i` identifier placeholders before assuming it's
available — this plugin's `testVersion="5.6-"` / `minimum_supported_wp_version` predate `%i`.

## XSS / CSRF prevention

- XSS: covered by the escaping table above — every value reaching HTML output must be escaped at
  the point of output, including values already "sanitized" on input.
- CSRF: nonces for Tier 1 and Tier 3 handlers as above; Tier 2's read-only endpoints are the one
  deliberate exception — see the guardrail above before extending that exception to a new handler.

## Secure file uploads

Beyond the standard sanitize/validate/escape rules, `inc/class-awsm-job-openings-form.php`'s upload
path additionally:

- Uses WordPress's own attachment pipeline — `wp_handle_upload()` → `wp_insert_attachment()` →
  `wp_generate_attachment_metadata()`/`wp_update_attachment_metadata()` — never hand-rolled
  filesystem writes. Keep any new upload handling inside this pipeline.
- Builds its mime-type allowlist from `get_allowed_mime_types()` intersected with the
  admin-configured `awsm_jobs_admin_upload_file_ext` option (default `pdf,doc,docx`), passed to
  `wp_handle_upload()` as a `mimes` override.
- Hooks `wp_check_filetype_and_ext` to work around a known WordPress/PHP mime-detection issue for
  Office XML formats (docx/xlsx/pptx) — scoped only to the application-submission action; extend
  this filter rather than adding a second, separate file-type check elsewhere.
- Randomizes stored filenames via a `unique_filename_callback` (`hashed_file_name()`: a hash of the
  original name + `random_bytes(16)` + time, then `sanitize_file_name()`) — this is part of why
  directly guessing an uploaded resume's URL is impractical even though...
- ...**directory-listing/direct-access protection is admin-configurable, not secure by default.**
  `admin/class-awsm-job-openings-settings.php` writes an `.htaccess` with `Options -Indexes`
  normally, and only writes `deny from all` if the admin explicitly enables
  `awsm_hide_uploaded_files`. This is Apache-only (no Nginx equivalent) and off by default. Be aware
  of this if you're asked to harden upload security — the randomized filename is real protection but
  isn't the same as denying direct access, and don't assume `.htaccess` alone covers non-Apache
  hosts.
- **No app-level file-size or file-count cap in code** — the form has a single `awsm_file` field and
  relies entirely on PHP's `upload_max_filesize`/`post_max_size` ini limits. Don't assume a
  configurable per-field size/count limit exists here (that's a Pro Pack feature, not present in the
  free plugin) — if asked to add one, it doesn't exist yet.

## Capabilities

Custom capability `manage_awsm_jobs` (defined in `inc/class-awsm-job-openings-core.php`) gates the
plugin's settings menu, its settings AJAX handler, and the Add-ons submenu. Other custom
capabilities — `edit_jobs`, `edit_applications`, `edit_others_applications`, `hiring_panelist` — gate
finer-grained actions (e.g. resume downloads need `edit_others_applications`; the admin dashboard
widget needs `edit_jobs`). See `project-specifics.md` for the full capability model and the custom
`hr` role. Prefer the existing, most-specific capability for a new check rather than defaulting to
`manage_options` or `manage_awsm_jobs` out of convenience.
